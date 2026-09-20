<?php defined('BASEPATH') || exit('No direct script access allowed');
class Content extends App_Controller
{
    public function __construct()
    {
        parent::__construct(); $this->auth->restrict('kelola_resep');
        $this->load->model('resep/resep_model'); $this->load->model('master/dokter_model'); Template::set_block('sub_nav', 'content/_sub_nav'); Assets::add_module_js('resep', 'resep.js');
    }
    public function index() { Template::set('toolbar_title', 'Resep Obat'); Template::render(); }
    public function get_data() { $sendiri = $this->dokter_sendiri(); $rows = $sendiri === false ? array() : $this->resep_model->menunggu($sendiri); foreach ($rows as $row) { $row->id = (int) $row->id_resep; } echo json_encode(array('draw' => (int)($this->input->post('draw') ?: 1), 'recordsTotal' => count($rows), 'recordsFiltered' => count($rows), 'data' => $rows)); }
    public function detail($id) { $row = $this->resep_model->detail((int) $id); if (!$row || !$this->boleh_akses($row->id_dokter)) { show_404(); } Template::set('resep', $row); Template::set('toolbar_title', 'Detail Resep'); Template::render(); }

    public function delete($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            echo json_encode(array('success' => false, 'message' => 'ID tidak valid.'));
            return;
        }

        $resep = $this->db->where('id_resep', $id)->get('resep')->row();
        if (!$resep) {
            echo json_encode(array('success' => false, 'message' => 'Resep tidak ditemukan.'));
            return;
        }
        if (!$this->boleh_akses($resep->id_dokter)) {
            echo json_encode(array('success' => false, 'message' => 'Anda tidak memiliki akses ke resep ini.'));
            return;
        }

        $guna = array();
        $n = $this->db->where('id_resep', $id)->count_all_results('penjualan_obat');
        if ($n > 0) $guna[] = $n . ' penjualan obat';

        if (!empty($guna)) {
            echo json_encode(array('success' => false, 'message' => 'Tidak bisa menghapus resep karena masih memiliki ' . implode(', ', $guna) . '.'));
            return;
        }

        $this->db->where('id_resep', $id)->delete('resep_detail');
        if ($this->db->where('id_resep', $id)->delete('resep')) {
            echo json_encode(array('success' => true, 'message' => 'Resep berhasil dihapus.'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Gagal menghapus resep.'));
        }
    }

    /** True bila user dokter murni (apoteker memproses semua resep, admin melihat semua). */
    private function hanya_dokter()
    {
        return $this->auth->has_permission('kelola_resep')
            && ! $this->auth->has_permission('kelola_penjualan_obat')
            && ! $this->auth->has_permission('kelola_pendaftaran');
    }

    /** id_dokter milik user login; null = tanpa filter; false = belum dipetakan. */
    private function dokter_sendiri()
    {
        if (! $this->hanya_dokter()) {
            return null;
        }
        $dokter = $this->dokter_model->dari_user($this->auth->user_id());
        return $dokter ? (int) $dokter->id_dokter : false;
    }

    private function boleh_akses($id_dokter)
    {
        $sendiri = $this->dokter_sendiri();
        return $sendiri === null || ($sendiri !== false && (int) $id_dokter === (int) $sendiri);
    }
}

