<?php defined('BASEPATH') || exit('No direct script access allowed');

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

        $pengadaan = $this->db->select('pengadaan_obat.*, supplier.nama_supplier, supplier.kode_supplier, supplier.alamat, supplier.no_hp')
            ->join('supplier', 'supplier.id_supplier = pengadaan_obat.id_supplier')
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

        // Ambil akumulasi penerimaan per item obat
        $terima_map = array();
        $terima_rows = $this->db->select('penerimaan_obat_detail.id_obat, SUM(penerimaan_obat_detail.jumlah_terima) AS total_terima')
            ->join('penerimaan_obat', 'penerimaan_obat.id_penerimaan = penerimaan_obat_detail.id_penerimaan')
            ->where('penerimaan_obat.id_pengadaan', $id)
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

        if ($this->input->post('save_terima')) {
            $items_input = $this->input->post('items');
            $items = array();

            if (is_array($items_input)) {
                foreach ($items_input as $id_obat => $row) {
                    $qty = isset($row['jumlah_terima']) ? trim($row['jumlah_terima']) : 0;
                    if ($qty !== '' && (int)$qty > 0) {
                        $masa_simpan = isset($row['masa_simpan']) ? trim($row['masa_simpan']) : '';
                        $satuan_masa_simpan = isset($row['satuan_masa_simpan']) ? strtoupper(trim($row['satuan_masa_simpan'])) : '';
                        $tanggal_kadaluarsa = null;
                        if ($satuan_masa_simpan !== '' && preg_match('/^\d+$/', $masa_simpan)
                            && (int) $masa_simpan > 0 && in_array($satuan_masa_simpan, array('HARI', 'BULAN', 'TAHUN'), true)) {
                            $interval_unit = ' days';
                            if ($satuan_masa_simpan === 'BULAN') {
                                $interval_unit = ' months';
                            } elseif ($satuan_masa_simpan === 'TAHUN') {
                                $interval_unit = ' years';
                            }
                            $interval = (int) $masa_simpan . $interval_unit;
                            $tanggal_kadaluarsa = (new DateTimeImmutable(date('Y-m-d')))->modify('+' . $interval)->format('Y-m-d');
                        }
                        if ($tanggal_kadaluarsa === null
                            && (! isset($row['kondisi']) || strtoupper($row['kondisi']) === 'BAIK')) {
                            Template::set_message('Masa simpan obat harus diisi dalam hari, bulan, atau tahun.', 'error');
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
