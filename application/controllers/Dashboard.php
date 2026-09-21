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
        if ($role === 'DOKTER' && ! $this->dokter_aktif()) {
            redirect('dokter-bertugas');
            return;
        }
        if ($role && isset($this->role_map[$role])) {
            redirect('dashboard/' . $this->role_map[$role]);
            return;
        }

        redirect('/');
    }

    /** Dashboard Pelayanan → ringkasan pendaftaran/kunjungan/antrian/tagihan. */
    public function pelayanan()
    {
        $this->require_role('ADMIN_PELAYANAN');
        $this->load->model('antrian/antrian_model');
        $hari_ini = date('Y-m-d');
        $besok = date('Y-m-d', strtotime($hari_ini . ' +1 day'));
        $antrian = $this->antrian_model->hari_ini();
        $menunggu = 0;
        foreach ($antrian as $a) {
            if ($a->status === 'MENUNGGU') {
                $menunggu++;
            }
        }
        Template::set(array(
            'pasien_total' => $this->db->where('status', 'AKTIF')->count_all_results('pasien'),
            'kunjungan_hari_ini' => $this->db->where('tanggal_kunjungan >=', $hari_ini . ' 00:00:00')->where('tanggal_kunjungan <', $besok . ' 00:00:00')->count_all_results('kunjungan'),
            'antrian_total' => count($antrian),
            'antrian_menunggu' => $menunggu,
            'tagihan_belum' => $this->db->where('status', 'BELUM_DIBAYAR')->count_all_results('tagihan'),
            'antrian' => $antrian,
        ));
        Template::set('toolbar_title', 'Dashboard Pelayanan');
        Template::render();
    }

    /** Dashboard Dokter → ringkasan kerja dokter yang login. */
    public function dokter()
    {
        $this->require_role('DOKTER');
        $this->load->model('master/dokter_model');
        $this->load->model('antrian/antrian_model');
        $dokter = $this->dokter_aktif();
        if (! $dokter) {
            redirect('dokter-bertugas');
            return;
        }
        $id_dokter = (int) $dokter->id_dokter;
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

    /** Dashboard Apoteker → ringkasan resep/stok/penjualan/pengadaan. */
    public function apoteker()
    {
        $this->require_role('APOTEKER');
        $this->load->model('resep/resep_model');
        $this->load->model('stok/stok_model');
        $hari_ini = date('Y-m-d');
        $besok = date('Y-m-d', strtotime($hari_ini . ' +1 day'));
        Template::set(array(
            'resep_menunggu' => count($this->resep_model->menunggu()),
            'stok_menipis' => count($this->stok_model->di_bawah_minimum()),
            'penjualan_hari_ini' => $this->db->where('tanggal_penjualan >=', $hari_ini . ' 00:00:00')->where('tanggal_penjualan <', $besok . ' 00:00:00')->count_all_results('penjualan_obat'),
            'pengadaan_aktif' => $this->db->where_in('status', array('DIPESAN', 'DIPROSES'))->count_all_results('pengadaan_obat'),
            'resep_list' => $this->resep_model->menunggu(),
        ));
        Template::set('toolbar_title', 'Dashboard Apoteker');
        Template::render();
    }

    /** Portal Pasien → data milik pasien yang login. */
    public function pasien()
    {
        $this->require_role('PASIEN');
        $pasien = $this->db->where('id_user', $this->auth->user_id())->get('pasien')->row();
        $kunjungan = array();
        $antrian = array();
        $riwayat = array();
        if ($pasien) {
            $kunjungan = $this->db->select('kunjungan.*, pelayanan.nama_pelayanan, poli.nama_poli, dokter.nama_dokter')
                ->join('pelayanan', 'pelayanan.id_pelayanan = kunjungan.id_pelayanan')
                ->join('poli', 'poli.id_poli = kunjungan.id_poli')
                ->join('dokter', 'dokter.id_dokter = kunjungan.id_dokter')
                ->where('kunjungan.id_pasien', $pasien->id_pasien)
                ->order_by('kunjungan.tanggal_kunjungan', 'DESC')
                ->get('kunjungan')->result();
            $this->load->model('antrian/antrian_model');
            $antrian = $this->antrian_model->untuk_pasien($pasien->id_pasien);
            $ids = array();
            foreach ($kunjungan as $k) {
                $ids[] = $k->id_kunjungan;
            }
            if ($ids) {
                $riwayat = $this->db->select('pemeriksaan.*, kunjungan.tanggal_kunjungan, dokter.nama_dokter')
                    ->join('kunjungan', 'kunjungan.id_kunjungan = pemeriksaan.id_kunjungan')
                    ->join('dokter', 'dokter.id_dokter = pemeriksaan.id_dokter')
                    ->where_in('pemeriksaan.id_kunjungan', $ids)
                    ->order_by('pemeriksaan.tanggal_pemeriksaan', 'DESC')
                    ->get('pemeriksaan')->result();
            }
        }
        Template::set(array(
            'pasien' => $pasien,
            'kunjungan' => $kunjungan,
            'antrian' => $antrian,
            'riwayat' => $riwayat,
            'keranjang_jml' => $pasien ? count((array) $this->session->userdata('keranjang_online')) : 0,
            'pesanan_aktif' => $pasien ? $this->db->where('id_pasien', $pasien->id_pasien)->where_not_in('status', array('SELESAI', 'BATAL'))->count_all_results('pesanan_online') : 0,
            'pesanan_terakhir' => $pasien ? $this->db->where('id_pasien', $pasien->id_pasien)->order_by('id_pesanan', 'DESC')->limit(1)->get('pesanan_online')->row() : null,
        ));
        Template::set('toolbar_title', 'Dashboard Pasien');
        Template::render();
    }

    /** Edit data pribadi pasien yang login. */
    public function edit_pribadi()
    {
        $this->require_role('PASIEN');
        $this->load->model('pasien/pasien_model');
        $pasien = $this->db->where('id_user', $this->auth->user_id())->get('pasien')->row();
        if (! $pasien) {
            Template::set_message('Data pasien tidak ditemukan.', 'error');
            redirect('dashboard/pasien');
        }

        if (isset($_POST['save'])) {
            $nik = trim($this->input->post('nik'));
            if (! $this->pasien_model->nik_valid($nik)) {
                Template::set_message($this->pasien_model->error, 'error');
            } elseif ($nik !== '' && ! $this->pasien_model->nik_tersedia($nik, $pasien->id_pasien)) {
                Template::set_message('NIK sudah digunakan pasien lain.', 'error');
            } else {
                $data = array(
                    'nama'   => trim($this->input->post('nama')),
                    'nik'    => $nik !== '' ? $nik : null,
                    'no_hp'  => trim($this->input->post('no_hp')),
                    'alamat' => trim($this->input->post('alamat')),
                );
                if ($this->pasien_model->update($pasien->id_pasien, $data)) {
                    Template::set_message('Data pribadi berhasil diperbarui.', 'success');
                    redirect('dashboard/pasien');
                }
                Template::set_message($this->pasien_model->error ?: 'Gagal memperbarui data pribadi.', 'error');
            }
        }

        Template::set('pasien', $pasien);
        Template::set('toolbar_title', 'Edit Data Pribadi');
        Template::set_view('dashboard/edit_pribadi');
        Template::render();
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
