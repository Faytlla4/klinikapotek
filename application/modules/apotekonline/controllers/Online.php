<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Apotek Online sisi PASIEN (context online). Orchestration di atas
 * model existing (obat/stok/resep/penjualan/tagihan); cart = session.
 */
class Online extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->auth->restrict('belanja_online');
        $this->load->model('apotekonline/pesanan_model');
        $this->load->model('audit/audit_log_model');
        Assets::add_module_js('apotekonline', 'apotekonline.js');
    }

    /** Pasien dari user login (false bila belum dipetakan). */
    private function pasien_saya()
    {
        $row = $this->db->where('id_user', $this->auth->user_id())->get('pasien')->row();
        return $row && $row->status === 'AKTIF' ? $row : false;
    }

    private function baca_keranjang()
    {
        $k = $this->session->userdata('keranjang_online');
        return is_array($k) ? $k : array();
    }

    private function simpan_keranjang($k)
    {
        $this->session->set_userdata('keranjang_online', $k);
    }

    /** Keranjang diperkaya dari DB (harga/stok/status selalu fresh). */
    private function isi_keranjang($id_pasien)
    {
        $out = array();
        foreach ($this->baca_keranjang() as $id_obat => $it) {
            $obat = $this->db->where('id_obat', $id_obat)->get('obat')->row();
            if (! $obat) {
                continue;
            }
            $stok = $this->db->where('id_obat', $id_obat)->get('stok_obat')->row();
            $out[] = array(
                'id_obat' => (int) $id_obat,
                'nama_obat' => $obat->nama_obat, 'satuan' => $obat->satuan,
                'harga' => (float) $obat->harga, 'aktif' => $obat->status === 'AKTIF',
                'wajib_resep' => in_array($obat->wajib_resep, array(true, 1, '1', 't', 'T'), true),
                'stok' => $stok ? (int) $stok->jumlah_stok : 0,
                'jumlah' => (int) $it['jumlah'],
                'id_resep' => isset($it['id_resep']) ? (int) $it['id_resep'] : null,
                'subtotal' => (float) $obat->harga * (int) $it['jumlah'],
            );
        }
        return $out;
    }

    /** GET Daftar Obat (q cari) + POST tambah keranjang. */
    public function obat()
    {
        $pasien = $this->pasien_saya();
        if (! $pasien) {
            Template::set_message('Akun belum terhubung ke data pasien.', 'error');
            redirect('dashboard/pasien');
        }
        if ($this->input->post('tambah')) {
            $id_obat = (int) $this->input->post('id_obat');
            $jumlah = $this->input->post('jumlah');
            $id_resep = $this->input->post('id_resep') ? (int) $this->input->post('id_resep') : null;
            $k = $this->baca_keranjang();
            $ada = isset($k[$id_obat]) ? (int) $k[$id_obat]['jumlah'] : 0;
            $v = $this->pesanan_model->validasi_item($pasien->id_pasien, $id_obat, $ada + $jumlah, $id_resep ?: (isset($k[$id_obat]['id_resep']) ? $k[$id_obat]['id_resep'] : null));
            if (! $v) {
                Template::set_message($this->pesanan_model->error, 'error');
            } else {
                $k[$id_obat] = array('jumlah' => $ada + $jumlah, 'id_resep' => $v['id_resep']);
                $this->simpan_keranjang($k);
                Template::set_message('Obat masuk keranjang.', 'success');
                redirect(SITE_AREA . '/online/keranjang');
            }
        }
        $q = trim($this->input->get('q') ?: '');
        $this->db->select('obat.*, COALESCE(stok_obat.jumlah_stok, 0) AS stok', false)
            ->join('stok_obat', 'stok_obat.id_obat = obat.id_obat', 'left')
            ->where('obat.status', 'AKTIF')
            ->order_by('obat.nama_obat', 'ASC');
        if ($q !== '') {
            $this->db->group_start()
                ->where("obat.nama_obat ILIKE '%" . $this->db->escape_like_str($q) . "%'", NULL, FALSE)
                ->or_where("obat.jenis_obat ILIKE '%" . $this->db->escape_like_str($q) . "%'", NULL, FALSE)
                ->group_end();
        }
        $obats = $this->db->get('obat')->result();
        $resep_map = array();
        foreach ($obats as $o) {
            if (in_array($o->wajib_resep, array(true, 1, '1', 't', 'T'), true)) {
                $resep_map[$o->id_obat] = $this->pesanan_model->daftar_resep_obat($pasien->id_pasien, $o->id_obat);
            }
        }
        Template::set(array('obat_list' => $obats, 'resep_map' => $resep_map, 'q' => $q));
        Template::set('toolbar_title', 'Daftar Obat');
        Template::set_view('online/obat');
        Template::render();
    }

    /** GET + POST ubah/hapus item keranjang. */
    public function keranjang()
    {
        $pasien = $this->pasien_saya();
        if (! $pasien) {
            redirect('dashboard/pasien');
        }
        $k = $this->baca_keranjang();
        $aksi = $this->input->post('aksi');
        $id_obat = (int) $this->input->post('id_obat');
        if ($aksi && $id_obat) {
            if ($aksi === 'hapus') {
                unset($k[$id_obat]);
            } elseif (isset($k[$id_obat])) {
                $baru = (int) $k[$id_obat]['jumlah'] + ($aksi === 'tambah' ? 1 : -1);
                if ($baru <= 0) {
                    unset($k[$id_obat]);
                } else {
                    $v = $this->pesanan_model->validasi_item($pasien->id_pasien, $id_obat, $baru, $k[$id_obat]['id_resep']);
                    if (! $v) {
                        Template::set_message($this->pesanan_model->error, 'error');
                    } else {
                        $k[$id_obat]['jumlah'] = $baru;
                    }
                }
            }
            $this->simpan_keranjang($k);
            redirect(SITE_AREA . '/online/keranjang');
        }
        $items = $this->isi_keranjang($pasien->id_pasien);
        $total = 0;
        foreach ($items as $it) {
            $total += $it['subtotal'];
        }
        Template::set(array('items' => $items, 'total' => $total));
        Template::set('toolbar_title', 'Keranjang');
        Template::set_view('online/keranjang');
        Template::render();
    }

    /** GET ringkasan + POST buat pesanan (validasi ulang penuh). */
    public function checkout()
    {
        $pasien = $this->pasien_saya();
        if (! $pasien) {
            redirect('dashboard/pasien');
        }
        $k = $this->baca_keranjang();
        if (empty($k)) {
            Template::set_message('Keranjang masih kosong.', 'attention');
            redirect(SITE_AREA . '/online/obat');
        }
        if ($this->input->post('checkout')) {
            $items = array();
            foreach ($k as $id_obat => $it) {
                $items[] = array('id_obat' => (int) $id_obat, 'jumlah' => (int) $it['jumlah'], 'id_resep' => isset($it['id_resep']) ? $it['id_resep'] : null);
            }
            $id = $this->pesanan_model->buat($pasien->id_pasien, $this->input->post('alamat'), $items);
            if (! $id) {
                Template::set_message($this->pesanan_model->error, 'error');
            } else {
                $this->audit_log_model->catat($this->auth->user_id(), 'create', 'pesanan_online', $id, '');
                $this->simpan_keranjang(array());
                Template::set_message('Pesanan berhasil dibuat.', 'success');
                redirect(SITE_AREA . '/online/pesanan/detail/' . $id);
            }
        }
        $items = $this->isi_keranjang($pasien->id_pasien);
        $total = 0;
        foreach ($items as $it) {
            $total += $it['subtotal'];
        }
        Template::set(array('items' => $items, 'total' => $total, 'pasien' => $pasien));
        Template::set('toolbar_title', 'Checkout');
        Template::set_view('online/checkout');
        Template::render();
    }

    public function pesanan()
    {
        $pasien = $this->pasien_saya();
        if (! $pasien) {
            redirect('dashboard/pasien');
        }
        Template::set('pesanan_list', $this->pesanan_model->daftar_pasien($pasien->id_pasien));
        Template::set('toolbar_title', 'Pesanan Saya');
        Template::set_view('online/pesanan');
        Template::render();
    }

    /** Detail milik sendiri + batal (hanya MENUNGGU). IDOR via milik_pasien. */
    public function pesanan_detail($id)
    {
        $pasien = $this->pasien_saya();
        if (! $pasien) {
            redirect('dashboard/pasien');
        }
        $id = (int) $id > 0 ? (int) $id : 0;
        if ($this->input->post('batalkan')) {
            if ($this->pesanan_model->batal_pasien($id, $pasien->id_pasien)) {
                $this->audit_log_model->catat($this->auth->user_id(), 'update', 'pesanan_online', $id, 'Batal oleh pasien');
                Template::set_message('Pesanan dibatalkan.', 'success');
            } else {
                Template::set_message($this->pesanan_model->error, 'error');
            }
            redirect(SITE_AREA . '/online/pesanan/detail/' . $id);
        }
        $row = $this->pesanan_model->detail_pasien($id, $pasien->id_pasien);
        if (! $row) {
            show_404();
        }
        Template::set('pesanan', $row);
        Template::set('toolbar_title', 'Detail Pesanan');
        Template::set_view('online/pesanan_detail');
        Template::render();
    }
}

