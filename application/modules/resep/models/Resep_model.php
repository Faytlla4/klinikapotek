<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Resep Model (§13, §14).
 *
 * Relasi: resep -> pemeriksaan, pasien, dokter; resep_detail -> resep, obat.
 * Status: DIBUAT -> DIPROSES -> SIAP -> DISERAHKAN (+BATAL).
 */
class Resep_model extends BF_Model
{
    protected $table_name = 'resep';
    protected $key = 'id_resep';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array('field' => 'id_pemeriksaan', 'label' => 'Pemeriksaan', 'rules' => 'integer'),
        array('field' => 'id_pasien', 'label' => 'Pasien', 'rules' => 'integer'),
        array('field' => 'id_dokter', 'label' => 'Dokter', 'rules' => 'integer'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'id_pemeriksaan', 'label' => 'Pemeriksaan', 'rules' => 'required'),
        array('field' => 'id_pasien', 'label' => 'Pasien', 'rules' => 'required'),
        array('field' => 'id_dokter', 'label' => 'Dokter', 'rules' => 'required'),
    );
    protected $skip_validation = false;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Buat resep + detail (obat, jumlah, dosis, aturan pakai).
     *
     * @param array $data   id_pemeriksaan, id_pasien, id_dokter, catatan?
     * @param array $detail list array(id_obat, jumlah, dosis?, aturan_pakai?, keterangan?)
     * @return array|bool array(id_resep, nomor_resep) atau false.
     */
    public function buat($data, $detail)
    {
        if (empty($detail)) {
            $this->error = 'Detail resep kosong.';
            return false;
        }
        $pemeriksaan = $this->db->where('id_pemeriksaan', $data['id_pemeriksaan'])->get('pemeriksaan')->row();
        if (! $pemeriksaan || (int) $pemeriksaan->id_dokter !== (int) $data['id_dokter']) {
            $this->error = 'Pemeriksaan dan dokter tidak sesuai.';
            return false;
        }
        if ($this->db->where('id_pemeriksaan', $data['id_pemeriksaan'])->get('resep')->row()) {
            $this->error = 'Pemeriksaan sudah memiliki resep.';
            return false;
        }
        // Hanya kolom tabel yang boleh masuk insert.
        $data = array_intersect_key($data, array_flip(array(
            'id_pemeriksaan', 'id_pasien', 'id_dokter', 'nomor_resep', 'tanggal_resep', 'status', 'catatan',
        )));
        $this->db->trans_start();
        $data['nomor_resep'] = nomor_baru('RS', 'resep', 'nomor_resep');
        $data['tanggal_resep'] = date('Y-m-d H:i:s');
        $data['status'] = 'DIBUAT';
        $id_resep = $this->insert($data);
        if ($id_resep) {
            $rows = array();
            foreach ($detail as $d) {
                $obat = $this->db->where('id_obat', $d['id_obat'])->get('obat')->row();
                if (! $obat || $obat->status !== 'AKTIF' || ! preg_match('/^\d+$/', (string) ($d['jumlah'] ?? '')) || (int) $d['jumlah'] <= 0) {
                    $this->error = 'Jumlah obat harus bilangan bulat positif.';
                    $this->db->trans_complete();
                    return false;
                }
                $rows[] = array(
                    'id_resep'     => $id_resep,
                    'id_obat'      => $d['id_obat'],
                    'jumlah'       => $d['jumlah'],
                    'dosis'        => isset($d['dosis']) ? $d['dosis'] : null,
                    'aturan_pakai' => isset($d['aturan_pakai']) ? $d['aturan_pakai'] : null,
                    'keterangan'   => isset($d['keterangan']) ? $d['keterangan'] : null,
                );
            }
            $this->db->insert_batch('resep_detail', $rows);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false || ! $id_resep) {
            $this->error = 'Gagal membuat resep.';
            return false;
        }
        return array('id_resep' => $id_resep, 'nomor_resep' => $data['nomor_resep']);
    }

    /** Resep + detail + info obat & stok (untuk APOTEKER cek obat/stok). */
    public function detail($id_resep)
    {
        $row = $this->find($id_resep);
        if (! $row) {
            return false;
        }
        $row->detail = $this->db->select('resep_detail.*, obat.kode_obat, obat.nama_obat,
                obat.satuan, obat.harga, COALESCE(stok_obat.jumlah_stok, 0) AS stok')
            ->join('obat', 'obat.id_obat = resep_detail.id_obat')
            ->join('stok_obat', 'stok_obat.id_obat = resep_detail.id_obat', 'left')
            ->where('resep_detail.id_resep', $id_resep)
            ->get('resep_detail')
            ->result();
        return $row;
    }

    /** Resep menunggu diproses apoteker (+ info pasien/dokter). */
    public function menunggu($id_dokter = null)
    {
        $this->db->select('resep.*, pasien.nama AS nama_pasien, dokter.nama_dokter')
            ->join('pasien', 'pasien.id_pasien = resep.id_pasien')
            ->join('dokter', 'dokter.id_dokter = resep.id_dokter')
            ->where_in('resep.status', array('DIBUAT', 'DIPROSES', 'SIAP'))
            ->order_by('resep.tanggal_resep', 'ASC');
        if ($id_dokter) {
            $this->db->where('resep.id_dokter', $id_dokter);
        }
        return $this->db->get('resep')->result();
    }
}
