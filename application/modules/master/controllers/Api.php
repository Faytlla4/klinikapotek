<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * API Master: akses list dan simpan dibatasi per entitas.
 */
class Api extends Authenticated_Controller
{
    private $map = array(
        'pelayanan' => array(
            'model' => 'pelayanan_model',
            'permission' => 'Master.Pelayanan.Manage'
        ),
        'poli' => array(
            'model' => 'poli_model',
            'permission' => 'Master.Poli.Manage'
        ),
        'spesialis' => array(
            'model' => 'spesialis_model',
            'permission' => 'Master.Spesialis.Manage'
        ),
        'ruangan' => array(
            'model' => 'ruangan_model',
            'permission' => 'Master.Ruangan.Manage'
        ),
        'dokter' => array(
            'model' => 'dokter_model',
            'permission' => 'Master.Dokter.Manage'
        ),
        'obat' => array(
            'model' => 'obat_model',
            'permission' => 'Master.Obat.Manage'
        )
    );

    public function __construct()
    {
        parent::__construct();

        foreach ($this->map as $item) {
            $this->load->model('master/' . $item['model']);
        }

        $this->load->model('audit/audit_log_model');
    }

    private function json($data, $code = 200)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    private function config_entitas($entitas)
    {
        return isset($this->map[$entitas]) ? $this->map[$entitas] : null;
    }

    private function model($config)
    {
        return $this->{$config['model']};
    }

    private function key_of($entitas)
    {
        $keys = array(
            'pelayanan' => 'id_pelayanan',
            'poli' => 'id_poli',
            'spesialis' => 'id_spesialis',
            'ruangan' => 'id_ruangan',
            'dokter' => 'id_dokter',
            'obat' => 'id_obat'
        );

        return isset($keys[$entitas]) ? $keys[$entitas] : null;
    }

    /** GET /master/api/list/{entitas} */
    public function list($entitas)
    {
        $config = $this->config_entitas($entitas);
        if (!$config) {
            $this->json(array('success' => false, 'error' => 'Entitas tidak dikenal.'), 404);
            return;
        }

        $this->auth->restrict($config['permission']);
        $model = $this->model($config);

        if ($entitas === 'ruangan' && $this->input->get('id_poli')) {
            $this->json(array(
                'success' => true,
                'data' => $model->per_poli($this->input->get('id_poli'))
            ));
            return;
        }

        if ($entitas === 'dokter') {
            $this->json(array('success' => true, 'data' => $model->dengan_spesialis()));
            return;
        }

        if ($entitas === 'obat') {
            $this->json(array('success' => true, 'data' => $model->dengan_stok()));
            return;
        }

        $this->json(array('success' => true, 'data' => $model->find_all() ?: array()));
    }

    /** POST /master/api/simpan/{entitas} */
    public function simpan($entitas)
    {
        $config = $this->config_entitas($entitas);
        $key = $this->key_of($entitas);

        if (!$config || !$key) {
            $this->json(array('success' => false, 'error' => 'Entitas tidak dikenal.'), 404);
            return;
        }

        $this->auth->restrict($config['permission']);
        $model = $this->model($config);
        $data = $this->input->post();

        if (!empty($data[$key])) {
            $id = $data[$key];
            unset($data[$key], $data['kode_obat'], $data['kode_supplier']);
            $ok = $model->update($id, $data);
            $aksi = 'update';
        } else {
            if (!isset($data['status'])) {
                $data['status'] = 'AKTIF';
            }
            $id = $model->insert($data);
            $ok = (bool) $id;
            $aksi = 'create';
        }

        if (!$ok) {
            $this->json(array(
                'success' => false,
                'error' => $model->error ?: 'Gagal menyimpan.'
            ), 422);
            return;
        }

        $this->audit_log_model->catat(
            $this->auth->user_id(),
            $aksi,
            'master_' . $entitas,
            $id,
            ''
        );

        $this->json(array('success' => true, 'data' => $model->find($id)));
    }
}