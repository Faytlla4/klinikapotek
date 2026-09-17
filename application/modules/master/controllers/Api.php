<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * API Master (§10): pelayanan, poli, spesialis, ruangan, dokter, obat.
 * Guard: kelola_master_data. Sumber data untuk dropdown, bukan hardcode.
 */
class Api extends Authenticated_Controller
{
    private $map = array(
        'pelayanan' => 'pelayanan_model',
        'poli'      => 'poli_model',
        'spesialis' => 'spesialis_model',
        'ruangan'   => 'ruangan_model',
        'dokter'    => 'dokter_model',
        'obat'      => 'obat_model',
    );

    public function __construct()
    {
        parent::__construct();
        foreach ($this->map as $model) {
            $this->load->model('master/' . $model);
        }
        $this->load->model('audit/audit_log_model');
    }

    private function json($data, $code = 200)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    private function model($entitas)
    {
        if (! isset($this->map[$entitas])) {
            return null;
        }
        return $this->{$this->map[$entitas]};
    }

    /** GET /master/api/list/{pelayanan|poli|spesialis|ruangan|dokter|obat} */
    public function list($entitas)
    {
        $this->auth->restrict('kelola_master_data');
        $m = $this->model($entitas);
        if (! $m) {
            $this->json(array('success' => false, 'error' => 'Entitas tidak dikenal.'), 404);
            return;
        }
        // Relasi: ruangan per poli (?id_poli=), dokter+spesialis, obat+stok.
        if ($entitas === 'ruangan' && $this->input->get('id_poli')) {
            $this->json(array('success' => true, 'data' => $m->per_poli($this->input->get('id_poli'))));
            return;
        }
        if ($entitas === 'dokter') {
            $this->json(array('success' => true, 'data' => $m->dengan_spesialis()));
            return;
        }
        if ($entitas === 'obat') {
            $this->json(array('success' => true, 'data' => $m->dengan_stok()));
            return;
        }
        $this->json(array('success' => true, 'data' => $m->find_all() ?: array()));
    }

    /** POST /master/api/simpan/{entitas} (create bila tanpa id, update bila ada id_*) */
    public function simpan($entitas)
    {
        $this->auth->restrict('kelola_master_data');
        $m = $this->model($entitas);
        if (! $m) {
            $this->json(array('success' => false, 'error' => 'Entitas tidak dikenal.'), 404);
            return;
        }
        $data = $this->input->post();
        $key = $this->key_of($entitas);
        if (! empty($data[$key])) {
            $id = $data[$key];
            unset($data[$key]);
            // ponytail: kode identifier (kode_obat/kode_supplier) tidak boleh berubah.
            unset($data['kode_obat'], $data['kode_supplier']);
            $ok = $m->update($id, $data);
            $aksi = 'update';
        } else {
            if (! isset($data['status'])) {
                $data['status'] = 'AKTIF';
            }
            $id = $m->insert($data);
            $ok = (bool) $id;
            $aksi = 'create';
        }
        if (! $ok) {
            $this->json(array('success' => false, 'error' => $m->error ?: 'Gagal menyimpan.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), $aksi, 'master_' . $entitas, $id, '');
        $this->json(array('success' => true, 'data' => $m->find($id)));
    }

    private function key_of($entitas)
    {
        $keys = array(
            'pelayanan' => 'id_pelayanan', 'poli' => 'id_poli', 'spesialis' => 'id_spesialis',
            'ruangan' => 'id_ruangan', 'dokter' => 'id_dokter', 'obat' => 'id_obat',
        );
        return $keys[$entitas];
    }
}
