<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * API Resep (§13, §14). Guard: kelola_resep (DOKTER membuat, APOTEKER memproses).
 * Body detail: items[0][id_obat], items[0][jumlah], items[0][dosis], items[0][aturan_pakai].
 */
class Api extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('resep/resep_model');
        $this->load->model('master/dokter_model');
        $this->load->model('audit/audit_log_model');
    }

    private function json($data, $code = 200)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /** ID -> int positif; 0 bila tidak valid (hindari SQL error). */
    private function as_id($v)
    {
        $id = (int) $v;
        return $id > 0 ? $id : 0;
    }

    /** POST: id_pemeriksaan, id_pasien, id_dokter, catatan?, items[] */
    public function buat()
    {
        $this->auth->restrict('kelola_resep');
        $sendiri = $this->dokter_sendiri();
        if ($sendiri === false) {
            $this->json(array('success' => false, 'error' => 'Akun belum dipetakan ke data dokter.'), 403);
            return;
        }
        $post = $this->input->post();
        if ($sendiri) {
            $post['id_dokter'] = $sendiri;
        }
        foreach (array('id_pemeriksaan', 'id_pasien', 'id_dokter') as $k) {
            if (isset($post[$k])) {
                $post[$k] = $this->as_id($post[$k]);
            }
        }
        $items = isset($post['items']) && is_array($post['items']) ? $post['items'] : null;
        unset($post['items']);
        if (is_array($items)) {
            // id_* dinormalisasi; jumlah DIBIARKAN mentah agar validasi
            // bilangan bulat di model dapat menolak desimal.
            foreach ($items as &$it) {
                if (isset($it['id_obat'])) {
                    $it['id_obat'] = $this->as_id($it['id_obat']);
                }
            }
            unset($it);
        }
        $hasil = $this->resep_model->buat($post, $items);
        if (! $hasil) {
            $this->json(array('success' => false, 'error' => $this->resep_model->error ?: 'Gagal.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'create', 'resep', $hasil['id_resep'], $hasil['nomor_resep']);
        $this->json(array('success' => true, 'data' => $hasil), 201);
    }

    /** GET daftar resep menunggu (+stok per item) */
    public function menunggu()
    {
        $this->auth->restrict('kelola_resep');
        $sendiri = $this->dokter_sendiri();
        $rows = $sendiri === false ? array() : $this->resep_model->menunggu($sendiri);
        $this->json(array('success' => true, 'data' => $rows));
    }

    /** GET /resep/api/detail/{id} */
    public function detail($id)
    {
        $this->auth->restrict('kelola_resep');
        $id = $this->as_id($id);
        $row = $id ? $this->resep_model->detail($id) : false;
        if (! $row || ! $this->boleh_akses($row->id_dokter)) {
            $this->json(array('success' => false, 'error' => 'Resep tidak ditemukan.'), 404);
            return;
        }
        $this->json(array('success' => true, 'data' => $row));
    }

    /** POST /resep/api/status/{id}: DIPROSES|SIAP|BATAL.
     * DISERAHKAN hanya boleh terjadi dari Penjualan_model::jual(), yang
     * mengurangi stok dan mencatat mutasi dalam transaksi yang sama.
     */
    public function status($id)
    {
        $this->auth->restrict('kelola_resep');
        $id = $this->as_id($id);
        $row = $id ? $this->db->where('id_resep', $id)->get('resep')->row() : false;
        if (! $row || ! $this->boleh_akses($row->id_dokter)) {
            $this->json(array('success' => false, 'error' => 'Resep tidak ditemukan.'), 404);
            return;
        }
        $status = $this->input->post('status');
        if (! in_array($status, array('DIPROSES', 'SIAP', 'BATAL'))) {
            $this->json(array('success' => false, 'error' => 'Status tidak valid.'), 422);
            return;
        }
        $this->db->where('id_resep', $id)->update('resep', array('status' => $status));
        $this->audit_log_model->catat($this->auth->user_id(), 'update', 'resep', $id, 'Status -> ' . $status);
        $this->json(array('success' => true));
    }

    /** True bila user dokter murni (apoteker/admin tanpa filter). */
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
        $dokter = $this->dokter_aktif();
        return $dokter ? (int) $dokter->id_dokter : false;
    }

    private function boleh_akses($id_dokter)
    {
        $sendiri = $this->dokter_sendiri();
        return $sendiri === null || ($sendiri !== false && (int) $id_dokter === (int) $sendiri);
    }
}
