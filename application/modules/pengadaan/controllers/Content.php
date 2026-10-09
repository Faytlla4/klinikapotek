<?php defined('BASEPATH') || exit('No direct script access allowed');

// Belanja obat ke supplier: index = form pesan cepat; create/detail = pesan, terima (stok +,
// batch + kadaluarsa), retur bila tak sesuai. Beratnya di Pengadaan_model.

class Content extends App_Controller
{
    /** Context aktif (sidebar/redirect). Child per-context meng-override. */
    protected $ctx = 'content';
    public function __construct()
    {
        parent::__construct(); $this->auth->restrict('kelola_pengadaan');
        $this->load->model('pengadaan/pengadaan_model'); $this->load->model('pengadaan/supplier_model');
        $this->load->model('master/obat_model'); Assets::add_module_js('pengadaan', 'pengadaan.js');
    }

    public function index()
    {
        if (isset($_POST['save'])) {
            $items = array(array('id_obat' => $this->input->post('id_obat'), 'jumlah_pesan' => $this->input->post('jumlah_pesan'), 'harga' => $this->input->post('harga')));
            $result = $this->pengadaan_model->pesan($this->input->post('id_supplier'), $items);
            if ($result) { Template::set_message('Pengadaan berhasil dibuat.', 'success'); redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan'); }
            Template::set_message($this->pengadaan_model->error ?: 'Pengadaan gagal.', 'error');
        }
        Template::set('supplier_list', $this->supplier_model->aktif());
        Template::set('obat_list', $this->obat_model->aktif());
        Template::set('toolbar_title', 'Pengadaan Obat');
        Template::render();
    }
    public function get_data()
    {
        $rows = $this->db->select('pengadaan_obat.*, supplier.nama_supplier')
            ->join('supplier', 'supplier.id_supplier = pengadaan_obat.id_supplier')
            ->order_by('id_pengadaan', 'DESC')
            ->get('pengadaan_obat')
            ->result();
        foreach ($rows as $row) {
            $row->id = (int) $row->id_pengadaan;
        }
        echo json_encode(array(
            'draw'            => (int) ($this->input->post('draw') ?: 1),
            'recordsTotal'    => count($rows),
            'recordsFiltered' => count($rows),
            'data'            => $rows
        ));
    }

    public function detail($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            Template::set_message('ID pengadaan tidak valid.', 'error');
            redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan');
        }

        $pengadaan = $this->db->select('pengadaan_obat.*, supplier.nama_supplier, supplier.kode_supplier, supplier.alamat, supplier.no_hp, permintaan_pengadaan.nomor_permintaan')
            ->join('supplier', 'supplier.id_supplier = pengadaan_obat.id_supplier')
            ->join('permintaan_pengadaan', 'permintaan_pengadaan.id_permintaan = pengadaan_obat.id_permintaan', 'left')
            ->where('pengadaan_obat.id_pengadaan', $id)
            ->get('pengadaan_obat')
            ->row();

        if (!$pengadaan) {
            Template::set_message('Data pengadaan tidak ditemukan.', 'error');
            redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan');
        }

        // Ambil detail item pesanan
        $details = $this->db->select('pengadaan_obat_detail.*, obat.nama_obat, obat.kode_obat, obat.satuan')
            ->join('obat', 'obat.id_obat = pengadaan_obat_detail.id_obat')
            ->where('pengadaan_obat_detail.id_pengadaan', $id)
            ->get('pengadaan_obat_detail')
            ->result();

        // Ambil akumulasi penerimaan DIKONFIRMASI per item (draft tidak dihitung).
        $terima_map = array();
        $terima_rows = $this->db->select('penerimaan_obat_detail.id_obat, SUM(penerimaan_obat_detail.jumlah_terima) AS total_terima')
            ->join('penerimaan_obat', 'penerimaan_obat.id_penerimaan = penerimaan_obat_detail.id_penerimaan')
            ->where('penerimaan_obat.id_pengadaan', $id)
            ->where('penerimaan_obat.status_konfirmasi', 'DIKONFIRMASI')
            ->group_by('penerimaan_obat_detail.id_obat')
            ->get('penerimaan_obat_detail')
            ->result();

        foreach ($terima_rows as $tr) {
            $terima_map[(int)$tr->id_obat] = (int)$tr->total_terima;
        }

        foreach ($details as $d) {
            $d->jumlah_sudah_terima = isset($terima_map[(int)$d->id_obat]) ? $terima_map[(int)$d->id_obat] : 0;
            $d->sisa_pesanan = max(0, (int)$d->jumlah_pesan - $d->jumlah_sudah_terima);
        }

        // Ambil riwayat penerimaan
        $expiry_columns = $this->db->field_exists('nomor_batch', 'penerimaan_obat_detail')
            && $this->db->field_exists('tanggal_kadaluarsa', 'penerimaan_obat_detail');
        $riwayat_select = 'penerimaan_obat.*, penerimaan_obat_detail.id_obat, penerimaan_obat_detail.jumlah_terima, penerimaan_obat_detail.kondisi, obat.nama_obat';
        if ($expiry_columns) {
            $riwayat_select .= ', penerimaan_obat_detail.nomor_batch, penerimaan_obat_detail.tanggal_kadaluarsa';
        }
        $riwayat_penerimaan = $this->db->select($riwayat_select)
            ->join('penerimaan_obat_detail', 'penerimaan_obat_detail.id_penerimaan = penerimaan_obat.id_penerimaan')
            ->join('obat', 'obat.id_obat = penerimaan_obat_detail.id_obat')
            ->where('penerimaan_obat.id_pengadaan', $id)
            ->order_by('penerimaan_obat.id_penerimaan', 'DESC')
            ->get('penerimaan_obat')
            ->result();

        if (! $expiry_columns) {
            foreach ($riwayat_penerimaan as $riwayat) {
                $riwayat->nomor_batch = null;
                $riwayat->tanggal_kadaluarsa = null;
            }
        }

        Template::set('pengadaan', $pengadaan);
        Template::set('details', $details);
        Template::set('riwayat_penerimaan', $riwayat_penerimaan);
        Template::set('toolbar_title', 'Detail Pengadaan #' . $pengadaan->nomor_pengadaan);
        Template::set_view('content/detail');
        Template::render();
    }

    public function terima($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            Template::set_message('ID pengadaan tidak valid.', 'error');
            redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan');
        }

        if ($this->input->post('save_terima') || $this->input->post('save_draft')) {
            $items_input = $this->input->post('items');
            $items = array();

            if (is_array($items_input)) {
                foreach ($items_input as $id_obat => $row) {
                    $qty = isset($row['jumlah_terima']) ? trim($row['jumlah_terima']) : 0;
                    if ($qty !== '' && (int)$qty > 0) {
                        $tanggal_kadaluarsa = isset($row['tanggal_kadaluarsa']) ? trim($row['tanggal_kadaluarsa']) : '';
                        if (empty($tanggal_kadaluarsa) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_kadaluarsa)) {
                            Template::set_message('Expired Date (ED) obat wajib diisi dengan tanggal yang valid.', 'error');
                            redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/detail/' . $id);
                        }
                        if ($tanggal_kadaluarsa < date('Y-m-d')) {
                            Template::set_message('Expired Date (ED) tidak boleh lebih kecil dari tanggal penerimaan.', 'error');
                            redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/detail/' . $id);
                        }
                        $items[] = array(
                            'id_obat'       => (int) $id_obat,
                            'jumlah_terima' => (int) $qty,
                            'kondisi'       => isset($row['kondisi']) ? $row['kondisi'] : 'Baik',
                            'nomor_batch' => isset($row['nomor_batch']) ? trim($row['nomor_batch']) : '',
                            'tanggal_kadaluarsa' => $tanggal_kadaluarsa,
                        );
                    }
                }
            }

            if (empty($items)) {
                Template::set_message('Masukkan setidaknya satu jumlah penerimaan obat yang valid.', 'error');
                redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/detail/' . $id);
            }

            // ponytail: satu form, dua tombol — draft (tanpa stok) atau langsung konfirmasi.
            if ($this->input->post('save_draft')) {
                $hasil = $this->pengadaan_model->terima_draft($id, $items, array(
                    'no_surat_jalan' => $this->input->post('no_surat_jalan'),
                    'no_faktur' => $this->input->post('no_faktur'),
                    'catatan' => $this->input->post('catatan_terima'),
                    'id_penerima' => $this->auth->user_id(),
                ));
                if ($hasil) {
                    $this->catat_audit('create', 'penerimaan_obat', $hasil['id_penerimaan'], 'Draft ' . $hasil['nomor_penerimaan']);
                    Template::set_message('Draft ' . $hasil['nomor_penerimaan'] . ' tersimpan, periksa lalu konfirmasi.', 'success');
                    redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/penerimaan_detail/' . $hasil['id_penerimaan']);
                }
                Template::set_message($this->pengadaan_model->error ?: 'Gagal menyimpan draft.', 'error');
                redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/detail/' . $id);
            }

            $result = $this->pengadaan_model->terima($id, $items);
            if ($result) {
                $this->load->model('audit/audit_log_model');
                $this->audit_log_model->catat($this->auth->user_id(), 'stok', 'penerimaan_obat', $result['id_penerimaan'], $result['nomor_penerimaan']);
                Template::set_message('Penerimaan obat berhasil disimpan dan stok telah diperbarui.', 'success');
                redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/detail/' . $id);
            } else {
                Template::set_message($this->pengadaan_model->error ?: 'Gagal menyimpan penerimaan obat.', 'error');
                redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/detail/' . $id);
            }
        }

        redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/detail/' . $id);
    }

    /** Daftar permintaan pengadaan (filter ?status=). */
    public function permintaan()
    {
        $this->load->model('pengadaan/permintaan_model');
        $status = $this->input->get('status');
        $valid = array('DRAFT', 'DIAJUKAN', 'DISETUJUI', 'DIPROSES', 'SELESAI', 'DITOLAK', 'DIBATALKAN');
        Template::set(array(
            'minta_list' => $this->permintaan_model->daftar(in_array($status, $valid) ? $status : null),
            'f_status' => $status,
        ));
        Template::set('toolbar_title', 'Permintaan Pengadaan');
        Template::set_view('content/permintaan');
        Template::render();
    }

    /** Form permintaan baru (POST -> DRAFT). */
    public function permintaan_buat()
    {
        $this->load->model('pengadaan/permintaan_model');
        if ($this->input->post('save')) {
            $items = $this->baca_item_minta();
            $id = $items !== false
                ? $this->permintaan_model->buat($this->auth->user_id(), $items, $this->input->post('catatan'), $this->input->post('unit'))
                : false;
            if ($id) {
                $this->catat_audit('create', 'permintaan_pengadaan', $id, '');
                Template::set_message('Permintaan tersimpan sebagai draft.', 'success');
                redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/permintaan_detail/' . $id);
            }
            Template::set_message($this->permintaan_model->error ?: 'Gagal menyimpan.', 'error');
        }
        Template::set('obat_list', $this->obat_model->aktif());
        Template::set('toolbar_title', 'Buat Permintaan');
        Template::set_view('content/permintaan_buat');
        Template::render();
    }

    /** Baca item permintaan dari POST: items[*][id_obat,jumlah_minta,catatan]. */
    private function baca_item_minta()
    {
        $in = $this->input->post('items');
        if (! is_array($in)) {
            return false;
        }
        $items = array();
        foreach ($in as $r) {
            if (! is_array($r)) {
                continue;
            }
            // ponytail: id_obat dibaca dari isi baris (form buat pakai indeks angka,
            // form ubah pakai id obat sebagai key + hidden field) — bukan dari key.
            $id_obat = isset($r['id_obat']) ? (int) $r['id_obat'] : 0;
            $jml = isset($r['jumlah_minta']) ? trim($r['jumlah_minta']) : '';
            if ($id_obat <= 0 && ($jml === '' || (int) $jml <= 0)) {
                continue;
            }
            $items[] = array(
                'id_obat' => $id_obat,
                'jumlah_minta' => $jml === '' ? 0 : (int) $jml,
                'catatan' => isset($r['catatan']) ? trim($r['catatan']) : '',
            );
        }
        return $items;
    }

    /** Detail + alur permintaan (ajukan/setujui/tolak/batal/ubah/PO). */
    public function permintaan_detail($id = null)
    {
        $this->load->model('pengadaan/permintaan_model');
        $id = (int) $id;
        $aksi = $this->input->post('aksi');
        $uid = $this->auth->user_id();
        if ($aksi === 'simpan_draft') {
            $items = $this->baca_item_minta();
            $ok = $items !== false
                ? $this->permintaan_model->ubah_draft($id, $items, $this->input->post('catatan'), $uid)
                : false;
            if ($items === false) {
                $this->permintaan_model->error = 'Item permintaan kosong.';
            }
            $this->pesan_hasil($ok, $this->permintaan_model, 'Draft diperbarui.', 'update', 'permintaan_pengadaan', $id, 'Ubah draft');
        } elseif ($aksi === 'ajukan') {
            $ok = $this->permintaan_model->ubah_status($id, 'DIAJUKAN', $uid, $this->input->post('catatan'));
            $this->pesan_hasil($ok, $this->permintaan_model, 'Permintaan diajukan.', 'update', 'permintaan_pengadaan', $id, 'Ajukan');
        } elseif ($aksi === 'setujui') {
            $this->auth->restrict('setujui_permintaan');
            $ok = $this->permintaan_model->ubah_status($id, 'DISETUJUI', $uid, $this->input->post('catatan'));
            $this->pesan_hasil($ok, $this->permintaan_model, 'Permintaan disetujui.', 'update', 'permintaan_pengadaan', $id, 'Setujui');
        } elseif ($aksi === 'tolak') {
            $this->auth->restrict('setujui_permintaan');
            $ok = $this->permintaan_model->ubah_status($id, 'DITOLAK', $uid, $this->input->post('catatan'));
            $this->pesan_hasil($ok, $this->permintaan_model, 'Permintaan ditolak.', 'update', 'permintaan_pengadaan', $id, 'Tolak');
        } elseif ($aksi === 'batalkan') {
            $ok = $this->permintaan_model->ubah_status($id, 'DIBATALKAN', $uid, $this->input->post('catatan'));
            $this->pesan_hasil($ok, $this->permintaan_model, 'Permintaan dibatalkan.', 'update', 'permintaan_pengadaan', $id, 'Batalkan');
        } elseif ($aksi === 'buat_po') {
            $over = array();
            foreach ((array) $this->input->post('harga') as $id_obat => $h) {
                if (is_numeric($h) && (float) $h >= 0) {
                    $over[(int) $id_obat] = (float) $h;
                }
            }
            $po = $this->permintaan_model->buat_po($id, $this->input->post('id_supplier'), $over, $uid);
            if ($po) {
                $this->catat_audit('create', 'pengadaan_obat', $po['id_pengadaan'], 'PO dari permintaan ' . $id);
                Template::set_message('PO ' . $po['nomor_pengadaan'] . ' dibuat.', 'success');
                redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/detail/' . $po['id_pengadaan']);
            }
            Template::set_message($this->permintaan_model->error ?: 'Gagal membuat PO.', 'error');
            redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/permintaan_detail/' . $id);
        }
        if ($aksi) {
            redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/permintaan_detail/' . $id);
        }
        $row = $this->permintaan_model->detail($id);
        if (! $row) {
            show_404();
        }
        Template::set('minta', $row);
        Template::set('supplier_list', $this->supplier_model->aktif());
        Template::set('obat_list', $this->obat_model->aktif());
        Template::set('toolbar_title', 'Permintaan ' . $row->nomor_permintaan);
        Template::set_view('content/permintaan_detail');
        Template::render();
    }

    /** Simpan penerimaan sebagai DRAFT (POST dari detail PO, tanpa gerak stok). */
    public function terima_draft($id = null)
    {
        $id = (int) $id;
        $items = $this->baca_item_terima();
        if (! is_array($items) || empty($items)) {
            Template::set_message($this->pengadaan_model->error ?: 'Masukkan setidaknya satu jumlah penerimaan yang valid.', 'error');
            redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/detail/' . $id);
        }
        $hasil = $this->pengadaan_model->terima_draft($id, $items, array(
            'no_surat_jalan' => $this->input->post('no_surat_jalan'),
            'no_faktur' => $this->input->post('no_faktur'),
            'catatan' => $this->input->post('catatan_terima'),
            'id_penerima' => $this->auth->user_id(),
        ));
        if ($hasil) {
            $this->catat_audit('create', 'penerimaan_obat', $hasil['id_penerimaan'], 'Draft ' . $hasil['nomor_penerimaan']);
            Template::set_message('Draft penerimaan ' . $hasil['nomor_penerimaan'] . ' tersimpan (stok belum berubah).', 'success');
            redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/penerimaan_detail/' . $hasil['id_penerimaan']);
        }
        Template::set_message($this->pengadaan_model->error ?: 'Gagal menyimpan draft.', 'error');
        redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/detail/' . $id);
    }

    /** Baca item terima dari POST detail PO (format sama dengan terima langsung). */
    private function baca_item_terima()
    {
        $in = $this->input->post('items');
        if (! is_array($in)) {
            return false;
        }
        $items = array();
        foreach ($in as $id_obat => $r) {
            $qty = isset($r['jumlah_terima']) ? trim($r['jumlah_terima']) : '';
            if ($qty === '' || (int) $qty <= 0) {
                continue;
            }
            $ed = isset($r['tanggal_kadaluarsa']) ? trim($r['tanggal_kadaluarsa']) : '';
            if ($ed === '' || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $ed) || $ed < date('Y-m-d')) {
                $this->pengadaan_model->error = 'Expired Date wajib valid dan tidak boleh lewat hari ini.';
                return 'ED_INVALID';
            }
            $items[] = array(
                'id_obat' => (int) $id_obat,
                'jumlah_terima' => (int) $qty,
                'kondisi' => isset($r['kondisi']) ? $r['kondisi'] : 'Baik',
                'nomor_batch' => isset($r['nomor_batch']) ? trim($r['nomor_batch']) : '',
                'tanggal_kadaluarsa' => $ed,
            );
        }
        return empty($items) ? false : $items;
    }

    /** Detail penerimaan + periksa/konfirmasi. */
    public function penerimaan_detail($id = null)
    {
        $id = (int) $id;
        $aksi = $this->input->post('aksi');
        $uid = $this->auth->user_id();
        if ($aksi === 'periksa') {
            $ok = $this->pengadaan_model->periksa_penerimaan($id, $uid, $this->input->post('catatan'));
            $this->pesan_hasil($ok, $this->pengadaan_model, 'Penerimaan ditandai DIPERIKSA.', 'update', 'penerimaan_obat', $id, 'Periksa');
            redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/penerimaan_detail/' . $id);
        } elseif ($aksi === 'konfirmasi') {
            $this->auth->restrict('konfirmasi_penerimaan');
            $ok = $this->pengadaan_model->konfirmasi_penerimaan($id, $uid, $this->input->post('catatan'));
            $this->pesan_hasil($ok, $this->pengadaan_model, 'Penerimaan dikonfirmasi, stok bertambah.', 'stok', 'penerimaan_obat', $id, 'Konfirmasi');
            redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/penerimaan_detail/' . $id);
        }
        $row = $this->db->select('penerimaan_obat.*, pengadaan_obat.nomor_pengadaan, supplier.nama_supplier')
            ->join('pengadaan_obat', 'pengadaan_obat.id_pengadaan = penerimaan_obat.id_pengadaan')
            ->join('supplier', 'supplier.id_supplier = pengadaan_obat.id_supplier')
            ->where('penerimaan_obat.id_penerimaan', $id)->get('penerimaan_obat')->row();
        if (! $row) {
            show_404();
        }
        $row->items = $this->db->select('penerimaan_obat_detail.*, obat.nama_obat, obat.satuan')
            ->join('obat', 'obat.id_obat = penerimaan_obat_detail.id_obat')
            ->where('id_penerimaan', $id)->get('penerimaan_obat_detail')->result();
        Template::set('terima', $row);
        Template::set('toolbar_title', 'Penerimaan ' . $row->nomor_penerimaan);
        Template::set_view('content/penerimaan_detail');
        Template::render();
    }

    /** Daftar retur pembelian (filter ?status=). */
    public function retur()
    {
        $status = $this->input->get('status');
        $this->db->select('retur_pengadaan.*, penerimaan_obat.nomor_penerimaan, pengadaan_obat.nomor_pengadaan, supplier.nama_supplier')
            ->join('penerimaan_obat', 'penerimaan_obat.id_penerimaan = retur_pengadaan.id_penerimaan')
            ->join('pengadaan_obat', 'pengadaan_obat.id_pengadaan = penerimaan_obat.id_pengadaan')
            ->join('supplier', 'supplier.id_supplier = pengadaan_obat.id_supplier')
            ->order_by('retur_pengadaan.id_retur', 'DESC');
        if ($status) {
            $this->db->where('retur_pengadaan.status', $status);
        }
        Template::set(array('retur_list' => $this->db->get('retur_pengadaan')->result(), 'f_status' => $status));
        // ponytail: picker penerimaan DIKONFIRMASI agar retur bisa dimulai dari halaman ini.
        Template::set('terima_list', $this->db->select('penerimaan_obat.id_penerimaan, penerimaan_obat.nomor_penerimaan, penerimaan_obat.tanggal_terima, pengadaan_obat.nomor_pengadaan, supplier.nama_supplier')
            ->join('pengadaan_obat', 'pengadaan_obat.id_pengadaan = penerimaan_obat.id_pengadaan')
            ->join('supplier', 'supplier.id_supplier = pengadaan_obat.id_supplier')
            ->where('penerimaan_obat.status_konfirmasi', 'DIKONFIRMASI')
            ->order_by('penerimaan_obat.id_penerimaan', 'DESC')->limit(50)
            ->get('penerimaan_obat')->result());
        Template::set('toolbar_title', 'Retur Pembelian');
        Template::set_view('content/retur');
        Template::render();
    }

    /** Form retur manual atas satu penerimaan. */
    public function retur_buat($id_penerimaan = null)
    {
        $this->auth->restrict('kelola_retur_pembelian');
        $id_penerimaan = (int) $id_penerimaan;
        if ($this->input->post('save')) {
            $items = array();
            foreach ((array) $this->input->post('items') as $id_obat => $r) {
                $jml = isset($r['jumlah']) ? trim($r['jumlah']) : '';
                if ($jml === '' || (int) $jml <= 0) {
                    continue;
                }
                $items[] = array(
                    'id_obat' => (int) $id_obat, 'jumlah' => (int) $jml,
                    'alasan' => isset($r['alasan']) ? trim($r['alasan']) : '',
                    'nomor_batch' => isset($r['nomor_batch']) ? trim($r['nomor_batch']) : '',
                    'tanggal_kadaluarsa' => isset($r['tanggal_kadaluarsa']) ? trim($r['tanggal_kadaluarsa']) : '',
                );
            }
            $id_retur = empty($items) ? false : $this->pengadaan_model->buat_retur_manual(
                $id_penerimaan, $items, $this->input->post('keterangan'), $this->auth->user_id());
            if ($id_retur) {
                $this->catat_audit('create', 'retur_pengadaan', $id_retur, '');
                Template::set_message('Retur diajukan, menunggu konfirmasi.', 'success');
                redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/retur_detail/' . $id_retur);
            }
            Template::set_message($this->pengadaan_model->error ?: 'Gagal menyimpan retur.', 'error');
        }
        $penerimaan = $this->db->select('penerimaan_obat.*, pengadaan_obat.nomor_pengadaan, supplier.nama_supplier')
            ->join('pengadaan_obat', 'pengadaan_obat.id_pengadaan = penerimaan_obat.id_pengadaan')
            ->join('supplier', 'supplier.id_supplier = pengadaan_obat.id_supplier')
            ->where('penerimaan_obat.id_penerimaan', $id_penerimaan)->get('penerimaan_obat')->row();
        if (! $penerimaan) {
            show_404();
        }
        $penerimaan->items = $this->db->select('penerimaan_obat_detail.*, obat.nama_obat, obat.satuan')
            ->join('obat', 'obat.id_obat = penerimaan_obat_detail.id_obat')
            ->where('id_penerimaan', $id_penerimaan)->get('penerimaan_obat_detail')->result();
        Template::set('penerimaan', $penerimaan);
        Template::set('toolbar_title', 'Buat Retur');
        Template::set_view('content/retur_buat');
        Template::render();
    }

    /** Detail retur + konfirmasi/tolak. */
    public function retur_detail($id = null)
    {
        $id = (int) $id;
        $aksi = $this->input->post('aksi');
        if ($aksi === 'setujui' || $aksi === 'tolak') {
            $this->auth->restrict('kelola_retur_pembelian');
            $ok = $this->pengadaan_model->konfirmasi_retur($id, $this->auth->user_id(), $aksi === 'setujui', $this->input->post('catatan'));
            $this->pesan_hasil($ok, $this->pengadaan_model,
                $aksi === 'setujui' ? 'Retur dikonfirmasi, stok berkurang.' : 'Retur ditolak.',
                'stok', 'retur_pengadaan', $id, $aksi === 'setujui' ? 'Konfirmasi' : 'Tolak');
            redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan/retur_detail/' . $id);
        }
        $row = $this->db->select('retur_pengadaan.*, penerimaan_obat.nomor_penerimaan, pengadaan_obat.nomor_pengadaan, supplier.nama_supplier')
            ->join('penerimaan_obat', 'penerimaan_obat.id_penerimaan = retur_pengadaan.id_penerimaan')
            ->join('pengadaan_obat', 'pengadaan_obat.id_pengadaan = penerimaan_obat.id_pengadaan')
            ->join('supplier', 'supplier.id_supplier = pengadaan_obat.id_supplier')
            ->where('retur_pengadaan.id_retur', $id)->get('retur_pengadaan')->row();
        if (! $row) {
            show_404();
        }
        $row->items = $this->db->select('retur_pengadaan_detail.*, obat.nama_obat, obat.satuan')
            ->join('obat', 'obat.id_obat = retur_pengadaan_detail.id_obat')
            ->where('id_retur', $id)->get('retur_pengadaan_detail')->result();
        Template::set('retur', $row);
        Template::set('toolbar_title', 'Retur ' . $row->nomor_retur);
        Template::set_view('content/retur_detail');
        Template::render();
    }

    /** Pesan + audit log + redirect kembali (pola aksi satu baris). */
    private function pesan_hasil($ok, $model, $pesan_ok, $aksi_audit, $tabel_audit, $id_audit, $catatan_audit)
    {
        if ($ok) {
            $this->load->model('audit/audit_log_model');
            $this->audit_log_model->catat($this->auth->user_id(), $aksi_audit, $tabel_audit, $id_audit, $catatan_audit);
            Template::set_message($pesan_ok, 'success');
        } else {
            Template::set_message($model->error ?: 'Gagal.', 'error');
        }
    }

    /** Catat audit tanpa pesan (untuk create). */
    private function catat_audit($aksi_audit, $tabel_audit, $id_audit, $catatan_audit)
    {
        $this->load->model('audit/audit_log_model');
        $this->audit_log_model->catat($this->auth->user_id(), $aksi_audit, $tabel_audit, $id_audit, $catatan_audit);
    }

    public function delete($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            echo json_encode(array('success' => false, 'message' => 'ID tidak valid.'));
            return;
        }

        $guna = array();
        $n = $this->db->where('id_pengadaan', $id)->count_all_results('penerimaan_obat');
        if ($n > 0) $guna[] = $n . ' penerimaan';

        if (!empty($guna)) {
            echo json_encode(array('success' => false, 'message' => 'Tidak bisa menghapus pengadaan karena masih memiliki ' . implode(', ', $guna) . '.'));
            return;
        }

        $this->db->where('id_pengadaan', $id)->delete('pengadaan_obat_detail');
        if ($this->db->where('id_pengadaan', $id)->delete('pengadaan_obat')) {
            echo json_encode(array('success' => true, 'message' => 'Pengadaan berhasil dihapus.'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Gagal menghapus pengadaan.'));
        }
    }
}
