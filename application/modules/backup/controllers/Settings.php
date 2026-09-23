<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Backup database PostgreSQL dalam context MANAJEMEN SISTEM.
 */
class Settings extends App_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->auth->restrict('Backup.Settings.View');
        $this->load->model('audit/audit_log_model');
    }

    /**
     * Folder backup di luar public web root.
     *
     * @return string|false
     */
    private function folder()
    {
        $dir = APPPATH . 'archives' . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR;

        if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
            return false;
        }

        if (!is_writable($dir)) {
            return false;
        }

        return $dir;
    }

    /**
     * Mencari pg_dump dari PostgreSQL Windows/Laragon.
     *
     * @return string|false
     */
    private function biner()
    {
        $candidates = array(
            'C:\\Program Files\\PostgreSQL\\18\\bin\\pg_dump.exe',
            'C:\\Program Files\\PostgreSQL\\17\\bin\\pg_dump.exe',
            'C:\\Program Files\\PostgreSQL\\16\\bin\\pg_dump.exe',
            'C:\\Program Files\\PostgreSQL\\15\\bin\\pg_dump.exe',
            'C:\\Program Files\\PostgreSQL\\14\\bin\\pg_dump.exe',

            'C:\\laragon\\bin\\postgresql\\18\\bin\\pg_dump.exe',
            'C:\\laragon\\bin\\postgresql\\17\\bin\\pg_dump.exe',
            'C:\\laragon\\bin\\postgresql\\16\\bin\\pg_dump.exe',
            'C:\\laragon\\bin\\postgresql\\15\\bin\\pg_dump.exe',
            'C:\\laragon\\bin\\postgresql\\14\\bin\\pg_dump.exe'
        );

        $windows_paths = glob(
            'C:\\Program Files\\PostgreSQL\\*\\bin\\pg_dump.exe'
        );

        $laragon_paths = glob(
            'C:\\laragon\\bin\\postgresql\\*\\bin\\pg_dump.exe'
        );

        if (is_array($windows_paths)) {
            $candidates = array_merge($candidates, $windows_paths);
        }

        if (is_array($laragon_paths)) {
            $candidates = array_merge($candidates, $laragon_paths);
        }

        $candidates = array_unique($candidates);

        foreach ($candidates as $candidate) {
            if (is_file($candidate) && is_readable($candidate)) {
                return $candidate;
            }
        }

        return false;
    }

    /**
     * Hapus folder kerja temporary backup.
     *
     * @param string $dir
     */
    private function bersihkan_folder_temporary($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = glob($dir . '*');

        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }

        @rmdir($dir);
    }

    public function index()
    {
        $dari = $this->input->get('dari') ? $this->input->get('dari') : '';
        $sampai = $this->input->get('sampai') ? $this->input->get('sampai') : '';

        $dari = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dari) ? $dari : '';
        $sampai = preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampai) ? $sampai : '';

        $daftar = array();
        $folder = $this->folder();

        if ($folder !== false) {
            $files = glob($folder . 'backup-*.zip');

            if (!is_array($files)) {
                $files = array();
            }

            foreach ($files as $file) {
                if (!is_file($file)) {
                    continue;
                }

                $tanggal_file = date('Y-m-d', filemtime($file));

                if (preg_match(
                    '/^backup-(\d{4})(\d{2})(\d{2})-\d{6}\.zip$/',
                    basename($file),
                    $matches
                )) {
                    $tanggal_file = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
                }

                if ($dari !== '' && $tanggal_file < $dari) {
                    continue;
                }

                if ($sampai !== '' && $tanggal_file > $sampai) {
                    continue;
                }

                $daftar[] = array(
                    'nama' => basename($file),
                    'ukuran' => filesize($file),
                    'tanggal' => date('Y-m-d H:i:s', filemtime($file))
                );
            }
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

    /**
     * Buat ZIP berisi database.sql dan info.txt.
     */
    public function buat()
    {
        if ($this->input->method() !== 'post') {
            redirect('admin/settings/backup');
            return;
        }

        if (!function_exists('exec')) {
            $this->session->set_flashdata(
                'backup_error',
                'Backup gagal: fungsi exec() dinonaktifkan pada PHP.'
            );

            redirect('admin/settings/backup');
            return;
        }

        if (!class_exists('ZipArchive')) {
            $this->session->set_flashdata(
                'backup_error',
                'Backup gagal: ekstensi PHP ZipArchive belum aktif.'
            );

            redirect('admin/settings/backup');
            return;
        }

        if (function_exists('set_time_limit')) {
            set_time_limit(180);
        }

        $folder = $this->folder();

        if ($folder === false) {
            $this->session->set_flashdata(
                'backup_error',
                'Backup gagal: folder application/archives/db tidak dapat dibuat atau tidak dapat ditulis.'
            );

            redirect('admin/settings/backup');
            return;
        }

        $binary = $this->biner();

        if ($binary === false) {
            $this->session->set_flashdata(
                'backup_error',
                'Backup gagal: pg_dump.exe tidak ditemukan. Instal PostgreSQL client atau periksa lokasi pg_dump.'
            );

            redirect('admin/settings/backup');
            return;
        }

        $nama = 'backup-' . date('Ymd-His');

        $temporary_dir = $folder
            . $nama
            . DIRECTORY_SEPARATOR;

        $sql_file = $temporary_dir . 'database.sql';
        $info_file = $temporary_dir . 'info.txt';
        $zip_file = $folder . $nama . '.zip';

        if (file_exists($zip_file)) {
            @unlink($zip_file);
        }

        if (
            !@mkdir($temporary_dir, 0755, true)
            || !is_writable($temporary_dir)
        ) {
            $this->session->set_flashdata(
                'backup_error',
                'Backup gagal: folder kerja temporary tidak dapat dibuat.'
            );

            redirect('admin/settings/backup');
            return;
        }

        $old_password = getenv('PGPASSWORD');

        /*
         * Password diambil dari koneksi CI aktif;
         * tidak pernah ditulis ke log.
         */
        putenv('PGPASSWORD=' . $this->db->password);

        $command = escapeshellarg($binary)
            . ' -h ' . escapeshellarg($this->db->hostname)
            . ' -p ' . escapeshellarg($this->db->port)
            . ' -U ' . escapeshellarg($this->db->username)
            . ' -d ' . escapeshellarg($this->db->database)
            . ' -F p --no-owner --no-privileges --inserts'
            . ' -f ' . escapeshellarg($sql_file)
            . ' 2>&1';

        $output = array();
        $exit_code = 1;

        exec($command, $output, $exit_code);

        if ($old_password === false) {
            putenv('PGPASSWORD');
        } else {
            putenv('PGPASSWORD=' . $old_password);
        }

        $success = (
            $exit_code === 0
            && is_file($sql_file)
            && filesize($sql_file) > 0
        );

        if ($success) {
            $content = file(
                $sql_file,
                FILE_IGNORE_NEW_LINES
            );

            if (is_array($content)) {
                $clean_content = array();

                foreach ($content as $line) {
                    if (preg_match('/^\\\\(restrict|unrestrict)\b/', $line)) {
                        continue;
                    }

                    $clean_content[] = $line;
                }

                if (count($clean_content) !== count($content)) {
                    file_put_contents(
                        $sql_file,
                        implode("\n", $clean_content) . "\n"
                    );
                }
            }

            $success = filesize($sql_file) > 0;
        }

        if ($success) {
            $current_user = $this->auth->user();

            file_put_contents(
                $info_file,
                'Backup database: ' . $this->db->database . "\n"
                . 'Tanggal: ' . date('Y-m-d H:i:s') . "\n"
                . 'Dibuat oleh: '
                . ($current_user ? $current_user->username : '-')
                . "\n"
            );

            $zip = new ZipArchive();

            $opened = $zip->open(
                $zip_file,
                ZipArchive::CREATE | ZipArchive::OVERWRITE
            );

            if ($opened === true) {
                $success = $zip->addFile(
                    $sql_file,
                    $nama . '/database.sql'
                );

                $success = $success && $zip->addFile(
                    $info_file,
                    $nama . '/info.txt'
                );

                $success = $success && $zip->close();

                $success = $success
                    && is_file($zip_file)
                    && filesize($zip_file) > 0;
            } else {
                $success = false;
            }
        }

        $this->bersihkan_folder_temporary($temporary_dir);

        if ($success) {
            $this->audit_log_model->catat(
                $this->auth->user_id(),
                'create',
                'backup_database',
                0,
                $nama . '.zip'
            );

            $this->session->set_flashdata(
                'backup_success',
                'Backup berhasil dibuat: ' . $nama . '.zip'
            );
        } else {
            if (is_file($zip_file)) {
                @unlink($zip_file);
            }

            $detail = implode(
                ' | ',
                array_slice($output, -5)
            );

            log_message(
                'error',
                'Backup PostgreSQL gagal. Exit code: '
                . $exit_code
                . '. Detail: '
                . $detail
            );

            $this->session->set_flashdata(
                'backup_error',
                'Backup gagal dibuat. Detail teknis telah dicatat ke log server.'
            );
        }

        redirect('admin/settings/backup');
    }

    public function unduh($nama = null)
    {
        $nama = is_string($nama) ? basename($nama) : '';

        $folder = $this->folder();
        $file = $folder ? $folder . $nama : '';

        if (
            !preg_match('/^backup-\d{8}-\d{6}\.zip$/', $nama)
            || !is_file($file)
        ) {
            show_404();
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/zip');
        header(
            'Content-Disposition: attachment; filename="' . $nama . '"'
        );
        header('Content-Length: ' . filesize($file));

        readfile($file);
        exit;
    }

    public function hapus($nama = null)
    {
        $nama = is_string($nama) ? basename($nama) : '';

        $folder = $this->folder();
        $file = $folder ? $folder . $nama : '';

        if (
            preg_match('/^backup-\d{8}-\d{6}\.zip$/', $nama)
            && is_file($file)
        ) {
            @unlink($file);

            $this->audit_log_model->catat(
                $this->auth->user_id(),
                'delete',
                'backup_database',
                0,
                $nama
            );

            $this->session->set_flashdata(
                'backup_success',
                'Backup ' . $nama . ' berhasil dihapus.'
            );
        } else {
            $this->session->set_flashdata(
                'backup_error',
                'File backup tidak ditemukan.'
            );
        }

        redirect('admin/settings/backup');
    }
}