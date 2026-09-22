<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Backup database dalam context MANAJEMEN SISTEM (pg_dump, format SQL polos). */
class Settings extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->auth->restrict('Backup.Settings.View');
        $this->load->model('audit/audit_log_model');
    }

    /** Folder penyimpanan (di luar web root publik). */
    private function folder()
    {
        $dir = APPPATH . 'archives' . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR;
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /** Cari biner pg_dump (instalasi umum Windows, lalu PATH). */
    private function biner()
    {
        $calon = array(
            'C:\\Program Files\\PostgreSQL\\18\\bin\\pg_dump.exe',
            'C:\\laragon\\bin\\postgresql\\18\\bin\\pg_dump.exe',
        );
        foreach ($calon as $b) {
            if (is_file($b)) {
                return $b;
            }
        }
        return 'pg_dump';
    }

    public function index()
    {
        $dari = $this->input->get('dari') ?: '';
        $sampai = $this->input->get('sampai') ?: '';
        $dari = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dari) ? $dari : '';
        $sampai = preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampai) ? $sampai : '';
        $daftar = array();
        foreach (glob($this->folder() . 'backup-*.zip') ?: array() as $f) {
            $tgl = date('Y-m-d', filemtime($f));
            if (preg_match('/^backup-(\d{4})(\d{2})(\d{2})-\d{6}\.zip$/', basename($f), $m)) {
                $tgl = $m[1] . '-' . $m[2] . '-' . $m[3];
            }
            if ($dari !== '' && $tgl < $dari) {
                continue;
            }
            if ($sampai !== '' && $tgl > $sampai) {
                continue;
            }
            $daftar[] = array(
                'nama'    => basename($f),
                'ukuran'  => filesize($f),
                'tanggal' => date('Y-m-d H:i:s', filemtime($f)),
            );
        }
        usort($daftar, function ($a, $b) {
            return strcmp($b['nama'], $a['nama']);
        });
        Template::set('backup_list', $daftar);
        Template::set('f_dari', $dari);
        Template::set('f_sampai', $sampai);
        Template::set('toolbar_title', 'Backup Database');
        Template::render();
    }

    /** Buat file backup ZIP via pg_dump (POST): ZIP berisi folder + database.sql + info.txt. */
    public function buat()
    {
        if ($this->input->method() !== 'post') {
            redirect(SITE_AREA . '/settings/backup');
        }
        if (function_exists('set_time_limit')) {
            set_time_limit(120);
        }
        $nama = 'backup-' . date('Ymd-His');
        $dir = $this->folder() . $nama . DIRECTORY_SEPARATOR;
        $zipfile = $this->folder() . $nama . '.zip';
        mkdir($dir, 0755, true);
        $sql = $dir . 'database.sql';
        $bin = $this->biner();
        putenv('PGPASSWORD=' . $this->db->password);
        // --inserts: baris data jadi INSERT biasa (bukan COPY ... FROM stdin)
        // supaya bisa direstore via pgAdmin Query Tool maupun psql v14–18.
        $cmd = '"' . $bin . '"'
            . ' -h ' . escapeshellarg($this->db->hostname)
            . ' -U ' . escapeshellarg($this->db->username)
            . ' -d ' . escapeshellarg($this->db->database)
            . ' -F p --no-owner --no-privileges --inserts'
            . ' -f ' . escapeshellarg($sql)
            . ' 2>&1';
        exec($cmd, $keluar, $kode);
        putenv('PGPASSWORD');
        $ok = ($kode === 0 && is_file($sql) && filesize($sql) > 0);
        if ($ok) {
            // Strip meta-command psql 17+ (\restrict/\unrestrict) agar dump
            // bisa direstore di psql/pgAdmin v14–18. ("\." COPY tetap dipertahankan.)
            $isi = file($sql, FILE_IGNORE_NEW_LINES);
            $bersih = array();
            foreach ($isi as $baris) {
                if (preg_match('/^\\\\(restrict|unrestrict)\b/', $baris)) {
                    continue;
                }
                $bersih[] = $baris;
            }
            if (count($bersih) !== count($isi)) {
                file_put_contents($sql, implode("\n", $bersih) . "\n");
            }
            $ok = (filesize($sql) > 0);
        }
        if ($ok) {
            $user = $this->auth->user();
            file_put_contents($dir . 'info.txt', 'Backup database ' . $this->db->database . "\n"
                . 'Tanggal: ' . date('Y-m-d H:i:s') . "\n"
                . 'Dibuat oleh: ' . ($user ? $user->username : '-') . "\n");
            $zip = new ZipArchive();
            $ok = ($zip->open($zipfile, ZipArchive::CREATE) === true);
            if ($ok) {
                $ok = $zip->addFile($sql, $nama . '/database.sql')
                    && $zip->addFile($dir . 'info.txt', $nama . '/info.txt');
                $zip->close();
                $ok = $ok && is_file($zipfile) && filesize($zipfile) > 0;
            }
        }
        if (is_file($sql)) {
            unlink($sql);
        }
        if (is_file($dir . 'info.txt')) {
            unlink($dir . 'info.txt');
        }
        if (is_dir($dir)) {
            rmdir($dir);
        }
        if ($ok) {
            $this->audit_log_model->catat($this->auth->user_id(), 'create', 'backup_database', 0, $nama . '.zip');
            Template::set_message('Backup ' . $nama . '.zip berhasil dibuat.', 'success');
        } else {
            if (is_file($zipfile)) {
                unlink($zipfile);
            }
            log_message('error', 'pg_dump gagal: ' . implode("\n", (array) $keluar));
            Template::set_message('Backup gagal dibuat. Periksa log server.', 'error');
        }
        redirect(SITE_AREA . '/settings/backup');
    }

    /** Unduh file backup (nama divalidasi ketat). */
    public function unduh($nama = null)
    {
        $nama = is_string($nama) ? basename($nama) : '';
        $file = $this->folder() . $nama;
        if (! preg_match('/^backup-\d{8}-\d{6}\.zip$/', $nama) || ! is_file($file)) {
            show_404();
        }
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $nama . '"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }

    /** Hapus file backup (konfirmasi di view). */
    public function hapus($nama = null)
    {
        $nama = is_string($nama) ? basename($nama) : '';
        $file = $this->folder() . $nama;
        if (preg_match('/^backup-\d{8}-\d{6}\.zip$/', $nama) && is_file($file)) {
            unlink($file);
            $this->audit_log_model->catat($this->auth->user_id(), 'delete', 'backup_database', 0, $nama);
            Template::set_message('Backup ' . $nama . ' dihapus.', 'success');
        } else {
            Template::set_message('File backup tidak ditemukan.', 'error');
        }
        redirect(SITE_AREA . '/settings/backup');
    }
}
