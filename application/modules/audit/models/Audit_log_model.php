<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Audit Log Model
 *
 * Mencatat aktivitas penting (login, logout, create, update, delete,
 * transaksi, perubahan stok) ke tabel audit_logs.
 *
 * Relasi: audit_logs.id_user -> users.id_user
 */
class Audit_log_model extends BF_Model
{
    protected $table_name = 'audit_logs';
    protected $key = 'id_log';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array('field' => 'aksi', 'label' => 'Aksi', 'rules' => 'required|max_length[100]'),
    );
    protected $insert_validation_rules = array();
    // ponytail: data audit dibuat sistem (terpercaya), lewati validasi form.
    protected $skip_validation = true;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Catat satu aktivitas ke audit_logs.
     *
     * @param int|null $id_user    ID user pelaku (null = sistem/guest).
     * @param string   $aksi       Jenis aksi: login, logout, create, update, delete, transaksi, stok, ...
     * @param string   $modul      Nama modul (users, pasien, kunjungan, stok, ...).
     * @param int|null $id_data    ID data terkait (opsional).
     * @param string   $keterangan Keterangan tambahan (opsional).
     * @return int|bool ID log baru atau false bila gagal.
     */
    public function catat($id_user, $aksi, $modul = 'any', $id_data = null, $keterangan = '')
    {
        return $this->insert(array(
            'id_user'    => $id_user,
            'aksi'       => $aksi,
            'modul'      => $modul,
            'id_data'    => $id_data,
            'waktu'      => date('Y-m-d H:i:s'),
            'keterangan' => $keterangan,
        ));
    }
}
