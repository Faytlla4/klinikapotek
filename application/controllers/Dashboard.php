<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Dashboard controller (router per-role, tanpa view baru).
 *
 * LOGIN -> CEK ROLE -> dashboard/{pelayanan|dokter|apoteker|pasien}.
 * Setiap method memakai dashboard yang sudah tersedia di template sebagai
 * placeholder; yang disiapkan di tahap ini adalah LOGIC dan ROUTE-nya.
 */
class Dashboard extends App_Controller
{
    /** @var array Peta nama role -> segmen dashboard. */
    private $role_map = array(
        'ADMIN_PELAYANAN' => 'pelayanan',
        'DOKTER'          => 'dokter',
        'APOTEKER'        => 'apoteker',
        'PASIEN'          => 'pasien',
    );

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Cek role user lalu arahkan ke dashboard sesuai role.
     *
     * @return void
     */
    public function index()
    {
        $role = $this->current_role();
        if ($role && isset($this->role_map[$role])) {
            redirect('dashboard/' . $this->role_map[$role]);
            return;
        }

        redirect('/');
    }

    /** Dashboard Pelayanan → halaman Pendaftaran Pasien. */
    public function pelayanan()
    {
        $this->require_role('ADMIN_PELAYANAN');
        redirect(SITE_AREA . '/content/pasien');
    }

    /** Dashboard Dokter → ringkasan kerja dokter yang login. */
    public function dokter()
    {
        $this->require_role('DOKTER');
        $this->load->model('master/dokter_model');
        $this->load->model('antrian/antrian_model');
        $dokter = $this->dokter_model->dari_user($this->auth->user_id());
        $id_dokter = $dokter ? (int) $dokter->id_dokter : null;
        if (! $dokter) {
            Template::set_message('Akun belum dipetakan ke data dokter.', 'attention');
        }
        $antrian = $id_dokter ? $this->antrian_model->untuk_dokter($id_dokter) : array();
        $menunggu = 0;
        foreach ($antrian as $a) {
            if ($a->status === 'MENUNGGU') {
                $menunggu++;
            }
        }
        $hari_ini = date('Y-m-d');
        if ($id_dokter) {
            $besok = date('Y-m-d', strtotime($hari_ini . ' +1 day'));
            $stat = $this->db->select("COUNT(*) AS total, COUNT(*) FILTER (WHERE status = 'SELESAI') AS selesai", false)
                ->where('id_dokter', $id_dokter)
                ->where('tanggal_pemeriksaan >=', $hari_ini . ' 00:00:00')
                ->where('tanggal_pemeriksaan <', $besok . ' 00:00:00')
                ->get('pemeriksaan')->row();
            $resep_hari_ini = $this->db->where('id_dokter', $id_dokter)
                ->where('tanggal_resep >=', $hari_ini . ' 00:00:00')
                ->where('tanggal_resep <', $besok . ' 00:00:00')
                ->count_all_results('resep');
        } else {
            $stat = (object) array('total' => 0, 'selesai' => 0);
            $resep_hari_ini = 0;
        }
        Template::set(array(
            'dokter' => $dokter,
            'antrian' => $antrian,
            'antrian_total' => count($antrian),
            'antrian_menunggu' => $menunggu,
            'periksa_hari_ini' => (int) $stat->total,
            'periksa_selesai' => (int) $stat->selesai,
            'resep_hari_ini' => (int) $resep_hari_ini,
        ));
        Template::set('toolbar_title', 'Dashboard Dokter');
        Template::render();
    }

    /** Dashboard Apoteker → halaman Obat. */
    public function apoteker()
    {
        $this->require_role('APOTEKER');
        redirect(SITE_AREA . '/master/obat');
    }

    /** Area Pasien → halaman depan. */
    public function pasien()
    {
        $this->require_role('PASIEN');
        redirect('/');
    }

    /**
     * Nama role user saat ini dari database (users -> user_roles -> roles).
     *
     * @return string|bool Nama role atau false bila tidak ada.
     */
    private function current_role()
    {
        $role_id = $this->auth->role_id();
        if (empty($role_id)) {
            return false;
        }
        $role = $this->db->select('nama_role')
            ->where('id_role', $role_id)
            ->get('roles')
            ->row();
        return $role ? $role->nama_role : false;
    }

    /**
     * Pastikan user ber-role sesuai; bila tidak, kembalikan ke router.
     *
     * @param string $expected Nama role yang diharapkan.
     * @return void
     */
    private function require_role($expected)
    {
        if ($this->current_role() !== $expected) {
            Template::set_message('Anda tidak memiliki akses ke halaman ini.', 'attention');
            redirect('dashboard');
        }
    }
}
