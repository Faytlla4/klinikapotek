<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Pengadaan Model (§16) + PO dari permintaan + penerimaan bertahap + retur manual.
 *
 * Alur PO: Pesan (DIPESAN, manual atau dari permintaan DISETUJUI) -> Terima
 * (langsung DIKONFIRMASI, atau via DRAFT -> DIPERIKSA -> DIKONFIRMASI) /
 * Tidak sesuai => retur. Status: DIPESAN -> DITERIMA_SEBAGIAN -> SELESAI (+DIBATALKAN).
 * Harga default dari obat.harga_satuan (fallback harga); harga PO tersimpan
 * per transaksi dan tidak ikut berubah bila master berubah.
 */
class Pengadaan_model extends BF_Model
{
    protected $table_name = 'pengadaan_obat';
    protected $key = 'id_pengadaan';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = true;
    protected $set_modified = true;
    protected $created_field = 'created_at';
    protected $modified_field = 'updated_at';
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array('field' => 'id_supplier', 'label' => 'Supplier', 'rules' => 'integer'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'id_supplier', 'label' => 'Supplier', 'rules' => 'required'),
    );
    protected $skip_validation = false;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('nomor');
    }

    /**
     * Buat pesanan pengadaan + detail.
     *
     * @param int      $id_supplier
     * @param array    $items list array(id_obat, jumlah_pesan, harga?)
     * @param int|null $id_permintaan opsional; harus DISETUJUI (PO warisan approvalnya).
     * @return array|bool array(id_pengadaan, nomor_pengadaan, total) atau false.
     */
    public function pesan($id_supplier, $items, $id_permintaan = null)
    {
        if (! $this->db->where('id_supplier', $id_supplier)->get('supplier')->row()) {
            $this->error = 'Supplier tidak ditemukan.';
            return false;
        }
        if (empty($items)) {
            $this->error = 'Item pesanan kosong.';
            return false;
        }
        $this->db->trans_start();
        if ($id_permintaan !== null) {
            $minta = $this->db->where('id_permintaan', (int) $id_permintaan)->get('permintaan_pengadaan')->row();
            if (! $minta || $minta->status !== 'DISETUJUI') {
                $this->db->trans_complete();
                $this->error = 'PO hanya dapat dibuat dari permintaan DISETUJUI.';
                return false;
            }
        }
        $total = 0;
        $rows = array();
        foreach ($items as $item) {
            if (empty($item['id_obat']) || ! preg_match('/^\d+$/', (string) ($item['jumlah_pesan'] ?? '')) || (int) $item['jumlah_pesan'] <= 0) {
                $this->db->trans_complete();
                $this->error = 'Jumlah pesan harus bilangan bulat positif.';
                return false;
            }
            $obat = $this->db->where('id_obat', $item['id_obat'])->get('obat')->row();
            if (! $obat) {
                $this->db->trans_complete();
                $this->error = "Obat ID {$item['id_obat']} tidak ditemukan.";
                return false;
            }
            // ponytail: harga transaksi = input supplier; default = harga_satuan master (fallback harga lama).
            $harga = isset($item['harga']) && is_numeric($item['harga']) && (float) $item['harga'] >= 0
                ? (float) $item['harga']
                : (float) ($obat->harga_satuan > 0 ? $obat->harga_satuan : $obat->harga);
            $subtotal = $harga * (int) $item['jumlah_pesan'];
            $total += $subtotal;
            $rows[] = array(
                'id_obat' => $item['id_obat'], 'jumlah_pesan' => (int) $item['jumlah_pesan'],
                'harga' => $harga, 'subtotal' => $subtotal,
            );
        }
        $nomor = nomor_baru('PO', 'pengadaan_obat', 'nomor_pengadaan');
        $id = $this->insert(array(
            'nomor_pengadaan' => $nomor, 'id_supplier' => $id_supplier,
            'tanggal_pesanan' => date('Y-m-d H:i:s'), 'total' => $total, 'status' => 'DIPESAN',
            'id_permintaan' => $id_permintaan !== null ? (int) $id_permintaan : null,
        ));
        if ($id) {
            foreach ($rows as &$r) {
                $r['id_pengadaan'] = $id;
            }
            $this->db->insert_batch('pengadaan_obat_detail', $rows);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false || ! $id) {
            $this->error = 'Gagal membuat pesanan pengadaan.';
            return false;
        }
        return array('id_pengadaan' => $id, 'nomor_pengadaan' => $nomor, 'total' => $total);
    }

    /**
     * Terima barang: item Sesuai menambah stok (+mutasi MASUK/PENGADAAN),
     * item Tidak sesuai otomatis dibuatkan retur (Diajukan).
     *
     * @param int   $id_pengadaan
     * @param array $items list array(id_obat, jumlah_terima, kondisi, nomor_batch, tanggal_kadaluarsa)
     * @return array|bool
     */
    public function terima($id_pengadaan, $items)
    {
        if ($id_pengadaan <= 0) {
            $this->error = 'Pengadaan tidak ditemukan.';
            return false;
        }
        if (empty($items)) {
            $this->error = 'Item penerimaan kosong.';
            return false;
        }

        $this->load->model('stok/stok_model');
        $this->db->trans_start();

        // Lock row pengadaan untuk cegah race condition / double submit
        $pengadaan = $this->db->query('SELECT * FROM pengadaan_obat WHERE id_pengadaan = ? FOR UPDATE', array($id_pengadaan))->row();
        if (! $pengadaan) {
            $this->db->trans_complete();
            $this->error = 'Pengadaan tidak ditemukan.';
            return false;
        }

        $status_upper = strtoupper($pengadaan->status);
        // ponytail: terima hanya dari DIPESAN/DITERIMA_SEBAGIAN; PO DRAFT/ajuan
        // harus disetujui dulu, PO SELESAI/BATAL terminal.
        if (! in_array($status_upper, array('DIPESAN', 'DITERIMA_SEBAGIAN'))) {
            $this->db->trans_complete();
            $this->error = "Pengadaan berstatus {$pengadaan->status}, belum dapat diterima.";
            return false;
        }

        // Fase 1: validasi SEMUA item dulu tanpa menulis apa pun
        $detail_po = array();
        foreach ($this->db->where('id_pengadaan', $id_pengadaan)->get('pengadaan_obat_detail')->result() as $detail) {
            $detail_po[(int) $detail->id_obat] = (int) $detail->jumlah_pesan;
        }

        // Hanya penerimaan DIKONFIRMASI yang mengurangi sisa (draft tidak dihitung).
        $sudah_diterima = array();
        foreach ($this->db->select('penerimaan_obat_detail.id_obat, SUM(penerimaan_obat_detail.jumlah_terima) AS jumlah')
            ->join('penerimaan_obat', 'penerimaan_obat.id_penerimaan = penerimaan_obat_detail.id_penerimaan')
            ->where('penerimaan_obat.id_pengadaan', $id_pengadaan)
            ->where('penerimaan_obat.status_konfirmasi', 'DIKONFIRMASI')
            ->group_by('penerimaan_obat_detail.id_obat')
            ->get('penerimaan_obat_detail')->result() as $terima) {
            $sudah_diterima[(int) $terima->id_obat] = (int) $terima->jumlah;
        }

        $diminta = array();
        foreach ($items as $item) {
            if (empty($item['id_obat']) || ! preg_match('/^\d+$/', (string) ($item['jumlah_terima'] ?? '')) || (int) $item['jumlah_terima'] <= 0) {
                $this->db->trans_complete();
                $this->error = 'Jumlah terima harus bilangan bulat positif.';
                return false;
            }
            $id_obat = (int) $item['id_obat'];
            if (! isset($detail_po[$id_obat])) {
                $this->db->trans_complete();
                $this->error = 'Obat penerimaan tidak ada pada detail pengadaan.';
                return false;
            }
            $diminta[$id_obat] = ($diminta[$id_obat] ?? 0) + (int) $item['jumlah_terima'];
        }

        foreach ($diminta as $id_obat => $jumlah_diminta) {
            $sisa = $detail_po[$id_obat] - ($sudah_diterima[$id_obat] ?? 0);
            if ($jumlah_diminta > $sisa) {
                $this->db->trans_complete();
                $this->error = "Jumlah penerimaan obat ID {$id_obat} melebihi sisa pesanan ({$sisa}).";
                return false;
            }
        }

        $nomor = nomor_baru('PN', 'penerimaan_obat', 'nomor_penerimaan');
        $this->db->insert('penerimaan_obat', array(
            'id_pengadaan' => $id_pengadaan,
            'nomor_penerimaan' => $nomor,
            'tanggal_terima' => date('Y-m-d H:i:s'),
            'status' => 'SESUAI',
        ));
        $id_penerimaan = $this->db->insert_id();
        $retur_items = array();
        $semua_sesuai = true;

        // Fase 2: tulis header, detail, stok, retur.
        foreach ($items as $item) {
            $kondisi = isset($item['kondisi']) ? $item['kondisi'] : 'Baik';
            $detail_penerimaan = array(
                'id_penerimaan' => $id_penerimaan,
                'id_obat' => $item['id_obat'],
                'jumlah_terima' => (int) $item['jumlah_terima'],
                'kondisi' => $kondisi,
            );
            if ($this->db->field_exists('nomor_batch', 'penerimaan_obat_detail')
                && $this->db->field_exists('tanggal_kadaluarsa', 'penerimaan_obat_detail')) {
                $detail_penerimaan['nomor_batch'] = ! empty($item['nomor_batch']) ? trim($item['nomor_batch']) : null;
                $detail_penerimaan['tanggal_kadaluarsa'] = ! empty($item['tanggal_kadaluarsa']) ? $item['tanggal_kadaluarsa'] : null;
            }
            $this->db->insert('penerimaan_obat_detail', $detail_penerimaan);
            if (strtoupper($kondisi) === 'BAIK') {
                if (! $this->stok_model->masuk($item['id_obat'], (int) $item['jumlah_terima'], 'PENGADAAN', $id_penerimaan)) {
                    $this->db->trans_complete();
                    $this->error = $this->stok_model->error;
                    return false;
                }
            } else {
                $semua_sesuai = false;
                $retur_items[] = array(
                    'id_obat' => $item['id_obat'],
                    'jumlah' => (int) $item['jumlah_terima'],
                    'alasan' => "Kondisi: {$kondisi}",
                );
            }
        }

        $id_retur = null;
        if (! empty($retur_items)) {
            // ponytail: barang ditolak saat terima tidak pernah masuk stok, jadi
            // retur otomatis ini terminal (SELESAI) — tak ada yang perlu dikonfirmasi.
            $this->db->where('id_penerimaan', $id_penerimaan)->update('penerimaan_obat', array('status' => 'TIDAK_SESUAI'));
            $this->db->insert('retur_pengadaan', array(
                'id_penerimaan' => $id_penerimaan,
                'nomor_retur' => nomor_baru('RT', 'retur_pengadaan', 'nomor_retur'),
                'tanggal_retur' => date('Y-m-d H:i:s'),
                'status' => 'SELESAI',
                'keterangan' => 'Otomatis dari penerimaan tidak sesuai (tanpa mutasi stok)',
            ));
            $id_retur = $this->db->insert_id();
            foreach ($retur_items as &$r) {
                $r['id_retur'] = $id_retur;
            }
            $this->db->insert_batch('retur_pengadaan_detail', $retur_items);
        }

        $this->sinkron_status_po($id_pengadaan, $detail_po, $sudah_diterima, $diminta);

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->error = 'Gagal menyimpan penerimaan.';
            return false;
        }

        return array('id_penerimaan' => $id_penerimaan, 'nomor_penerimaan' => $nomor, 'id_retur' => $id_retur);
    }

    /**
     * Sinkron status PO dari akumulasi penerimaan DIKONFIRMASI + batch baru.
     * PO SELESAI menutup permintaan asalnya. Dipakai terima() & konfirmasi().
     */
    private function sinkron_status_po($id_pengadaan, $detail_po, $sudah_diterima, $diminta)
    {
        $lunas_semua = true;
        foreach ($detail_po as $id_o => $pesan_qty) {
            $terima_total = ($sudah_diterima[$id_o] ?? 0) + ($diminta[$id_o] ?? 0);
            if ($terima_total < $pesan_qty) {
                $lunas_semua = false;
                break;
            }
        }
        $this->db->where('id_pengadaan', $id_pengadaan)->update('pengadaan_obat', array(
            'status' => $lunas_semua ? 'SELESAI' : 'DITERIMA_SEBAGIAN',
            'updated_at' => date('Y-m-d H:i:s'),
        ));
        if ($lunas_semua) {
            $po = $this->db->where('id_pengadaan', $id_pengadaan)->get('pengadaan_obat')->row();
            if ($po && ! empty($po->id_permintaan)) {
                $this->db->where('id_permintaan', $po->id_permintaan)->where('status', 'DIPROSES')
                    ->update('permintaan_pengadaan', array('status' => 'SELESAI', 'updated_at' => date('Y-m-d H:i:s')));
                $this->db->insert('permintaan_status_log', array(
                    'id_permintaan' => $po->id_permintaan, 'status_lama' => 'DIPROSES', 'status_baru' => 'SELESAI',
                    'id_user' => null, 'tanggal' => date('Y-m-d H:i:s'),
                    'catatan' => 'PO ' . $po->nomor_pengadaan . ' terpenuhi.',
                ));
            }
        }
    }

    /** Validasi item penerimaan (dipakai terima langsung maupun draft). */
    private function validasi_item_terima($id_pengadaan, $items, &$detail_po, &$sudah_diterima, &$diminta)
    {
        $detail_po = array();
        foreach ($this->db->where('id_pengadaan', $id_pengadaan)->get('pengadaan_obat_detail')->result() as $detail) {
            $detail_po[(int) $detail->id_obat] = (int) $detail->jumlah_pesan;
        }
        $sudah_diterima = array();
        foreach ($this->db->select('penerimaan_obat_detail.id_obat, SUM(penerimaan_obat_detail.jumlah_terima) AS jumlah')
            ->join('penerimaan_obat', 'penerimaan_obat.id_penerimaan = penerimaan_obat_detail.id_penerimaan')
            ->where('penerimaan_obat.id_pengadaan', $id_pengadaan)
            ->where('penerimaan_obat.status_konfirmasi', 'DIKONFIRMASI')
            ->group_by('penerimaan_obat_detail.id_obat')
            ->get('penerimaan_obat_detail')->result() as $terima) {
            $sudah_diterima[(int) $terima->id_obat] = (int) $terima->jumlah;
        }
        $diminta = array();
        foreach ($items as $item) {
            if (empty($item['id_obat']) || ! preg_match('/^\d+$/', (string) ($item['jumlah_terima'] ?? '')) || (int) $item['jumlah_terima'] <= 0) {
                $this->error = 'Jumlah terima harus bilangan bulat positif.';
                return false;
            }
            $id_obat = (int) $item['id_obat'];
            if (! isset($detail_po[$id_obat])) {
                $this->error = 'Obat penerimaan tidak ada pada detail pengadaan.';
                return false;
            }
            $diminta[$id_obat] = ($diminta[$id_obat] ?? 0) + (int) $item['jumlah_terima'];
        }
        foreach ($diminta as $id_obat => $jumlah_diminta) {
            $sisa = $detail_po[$id_obat] - ($sudah_diterima[$id_obat] ?? 0);
            if ($jumlah_diminta > $sisa) {
                $this->error = "Jumlah penerimaan obat ID {$id_obat} melebihi sisa pesanan ({$sisa}).";
                return false;
            }
        }
        return true;
    }

    /**
     * Simpan penerimaan sebagai DRAFT (tanpa gerak stok). $items sama seperti
     * terima() + opsional no_surat_jalan/no_faktur/catatan/id_penerima via $opt.
     */
    public function terima_draft($id_pengadaan, $items, $opt = array())
    {
        if (empty($items)) {
            $this->error = 'Item penerimaan kosong.';
            return false;
        }
        $this->db->trans_start();
        $pengadaan = $this->db->query('SELECT * FROM pengadaan_obat WHERE id_pengadaan = ? FOR UPDATE', array($id_pengadaan))->row();
        if (! $pengadaan) {
            $this->db->trans_complete();
            $this->error = 'Pengadaan tidak ditemukan.';
            return false;
        }
        if (! in_array(strtoupper($pengadaan->status), array('DIPESAN', 'DITERIMA_SEBAGIAN'))) {
            $this->db->trans_complete();
            $this->error = "Pengadaan berstatus {$pengadaan->status}, belum dapat diterima.";
            return false;
        }
        if (! $this->validasi_item_terima($id_pengadaan, $items, $detail_po, $sudah_diterima, $diminta)) {
            $this->db->trans_complete();
            return false;
        }
        $nomor = nomor_baru('PN', 'penerimaan_obat', 'nomor_penerimaan');
        $this->db->insert('penerimaan_obat', array(
            'id_pengadaan' => $id_pengadaan,
            'nomor_penerimaan' => $nomor,
            'tanggal_terima' => date('Y-m-d H:i:s'),
            'status' => 'SESUAI',
            'status_konfirmasi' => 'DRAFT',
            'no_surat_jalan' => isset($opt['no_surat_jalan']) ? trim($opt['no_surat_jalan']) : null,
            'no_faktur' => isset($opt['no_faktur']) ? trim($opt['no_faktur']) : null,
            'id_penerima' => isset($opt['id_penerima']) ? (int) $opt['id_penerima'] : null,
            'catatan' => isset($opt['catatan']) ? trim($opt['catatan']) : null,
        ));
        $id_penerimaan = $this->db->insert_id();
        foreach ($items as $item) {
            $this->db->insert('penerimaan_obat_detail', array(
                'id_penerimaan' => $id_penerimaan,
                'id_obat' => (int) $item['id_obat'],
                'jumlah_terima' => (int) $item['jumlah_terima'],
                'kondisi' => isset($item['kondisi']) ? $item['kondisi'] : 'Baik',
                'nomor_batch' => ! empty($item['nomor_batch']) ? trim($item['nomor_batch']) : null,
                'tanggal_kadaluarsa' => ! empty($item['tanggal_kadaluarsa']) ? $item['tanggal_kadaluarsa'] : null,
            ));
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false || ! $id_penerimaan) {
            $this->error = 'Gagal menyimpan draft penerimaan.';
            return false;
        }
        return array('id_penerimaan' => $id_penerimaan, 'nomor_penerimaan' => $nomor);
    }

    /** DRAFT -> DIPERIKSA (tahap periksa sebelum konfirmasi; tanpa gerak stok). */
    public function periksa_penerimaan($id_penerimaan, $id_user, $catatan = '')
    {
        $row = $this->db->where('id_penerimaan', $id_penerimaan)->get('penerimaan_obat')->row();
        if (! $row) {
            $this->error = 'Penerimaan tidak ditemukan.';
            return false;
        }
        if ($row->status_konfirmasi !== 'DRAFT') {
            $this->error = "Penerimaan berstatus {$row->status_konfirmasi}, tidak dapat diperiksa.";
            return false;
        }
        return $this->update_konfirmasi($id_penerimaan, 'DIPERIKSA', $id_user, $catatan);
    }

    /**
     * Konfirmasi penerimaan (DIPERIKSA -> DIKONFIRMASI): stok + batch + mutasi
     * bergerak di sini; item tak-Baik menjadi retur otomatis (SELESAI, tanpa stok).
     */
    public function konfirmasi_penerimaan($id_penerimaan, $id_user, $catatan = '')
    {
        $this->load->model('stok/stok_model');
        $this->db->trans_start();
        $row = $this->db->query('SELECT * FROM penerimaan_obat WHERE id_penerimaan = ? FOR UPDATE', array($id_penerimaan))->row();
        if (! $row) {
            $this->db->trans_complete();
            $this->error = 'Penerimaan tidak ditemukan.';
            return false;
        }
        if ($row->status_konfirmasi !== 'DIPERIKSA') {
            $this->db->trans_complete();
            $this->error = "Penerimaan berstatus {$row->status_konfirmasi}; periksa dulu sebelum konfirmasi.";
            return false;
        }
        $items = $this->db->where('id_penerimaan', $id_penerimaan)->get('penerimaan_obat_detail')->result();
        if (empty($items)) {
            $this->db->trans_complete();
            $this->error = 'Penerimaan tidak memiliki item.';
            return false;
        }
        // Validasi ulang sisa terhadap yang sudah DIKONFIRMASI (draft ini belum dihitung).
        $detail_po = array();
        foreach ($this->db->where('id_pengadaan', $row->id_pengadaan)->get('pengadaan_obat_detail')->result() as $detail) {
            $detail_po[(int) $detail->id_obat] = (int) $detail->jumlah_pesan;
        }
        $sudah = array();
        foreach ($this->db->select('penerimaan_obat_detail.id_obat, SUM(penerimaan_obat_detail.jumlah_terima) AS jumlah')
            ->join('penerimaan_obat', 'penerimaan_obat.id_penerimaan = penerimaan_obat_detail.id_penerimaan')
            ->where('penerimaan_obat.id_pengadaan', $row->id_pengadaan)
            ->where('penerimaan_obat.status_konfirmasi', 'DIKONFIRMASI')
            ->group_by('penerimaan_obat_detail.id_obat')
            ->get('penerimaan_obat_detail')->result() as $t) {
            $sudah[(int) $t->id_obat] = (int) $t->jumlah;
        }
        $diminta = array();
        foreach ($items as $it) {
            $diminta[(int) $it->id_obat] = ($diminta[(int) $it->id_obat] ?? 0) + (int) $it->jumlah_terima;
        }
        foreach ($diminta as $id_obat => $jml) {
            $sisa = ($detail_po[$id_obat] ?? 0) - ($sudah[$id_obat] ?? 0);
            if ($jml > $sisa) {
                $this->db->trans_complete();
                $this->error = "Obat ID {$id_obat} melebihi sisa pesanan ({$sisa}).";
                return false;
            }
        }
        $retur_items = array();
        $ada_tak_sesuai = false;
        foreach ($items as $it) {
            if (strtoupper($it->kondisi) === 'BAIK') {
                if (! $this->stok_model->masuk((int) $it->id_obat, (int) $it->jumlah_terima, 'PENGADAAN', (int) $id_penerimaan,
                    'Penerimaan ' . $row->nomor_penerimaan, $it->nomor_batch, $it->tanggal_kadaluarsa, $id_user)) {
                    $this->db->trans_complete();
                    $this->error = $this->stok_model->error;
                    return false;
                }
            } else {
                $ada_tak_sesuai = true;
                $retur_items[] = array('id_obat' => (int) $it->id_obat, 'jumlah' => (int) $it->jumlah_terima, 'alasan' => "Kondisi: {$it->kondisi}");
            }
        }
        if (! empty($retur_items)) {
            $this->db->where('id_penerimaan', $id_penerimaan)->update('penerimaan_obat', array('status' => 'TIDAK_SESUAI'));
            $this->db->insert('retur_pengadaan', array(
                'id_penerimaan' => $id_penerimaan,
                'nomor_retur' => nomor_baru('RT', 'retur_pengadaan', 'nomor_retur'),
                'tanggal_retur' => date('Y-m-d H:i:s'),
                'status' => 'SELESAI',
                'keterangan' => 'Otomatis dari penerimaan tidak sesuai (tanpa mutasi stok)',
            ));
            $id_retur = $this->db->insert_id();
            foreach ($retur_items as &$r) {
                $r['id_retur'] = $id_retur;
            }
            $this->db->insert_batch('retur_pengadaan_detail', $retur_items);
        }
        $this->update_konfirmasi($id_penerimaan, 'DIKONFIRMASI', $id_user, $catatan, false);
        $this->sinkron_status_po($row->id_pengadaan, $detail_po, $sudah, $diminta);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->error = 'Gagal mengkonfirmasi penerimaan.';
            return false;
        }
        return true;
    }

    private function update_konfirmasi($id_penerimaan, $status, $id_user, $catatan, $pakai_transaksi = true)
    {
        if ($pakai_transaksi) {
            $this->db->trans_start();
        }
        $this->db->where('id_penerimaan', $id_penerimaan)->update('penerimaan_obat', array(
            'status_konfirmasi' => $status,
            'id_penerima' => (int) $id_user,
            'catatan' => trim((string) $catatan) !== '' ? trim((string) $catatan) : null,
        ));
        if ($pakai_transaksi) {
            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                $this->error = 'Gagal mengubah status penerimaan.';
                return false;
            }
        }
        return true;
    }

    /**
     * Retur manual atas penerimaan DIKONFIRMASI. $items: id_obat, jumlah,
     * alasan, batch?, tanggal_kadaluarsa?. Jumlah ≤ (diterima-Baik − sudah diretur).
     */
    public function buat_retur_manual($id_penerimaan, $items, $keterangan, $id_user)
    {
        $penerimaan = $this->db->where('id_penerimaan', $id_penerimaan)->get('penerimaan_obat')->row();
        if (! $penerimaan) {
            $this->error = 'Penerimaan tidak ditemukan.';
            return false;
        }
        if ($penerimaan->status_konfirmasi !== 'DIKONFIRMASI') {
            $this->error = 'Hanya penerimaan DIKONFIRMASI yang dapat diretur.';
            return false;
        }
        if (empty($items)) {
            $this->error = 'Item retur kosong.';
            return false;
        }
        $diterima = array();
        foreach ($this->db->where('id_penerimaan', $id_penerimaan)->where('kondisi', 'Baik')->get('penerimaan_obat_detail')->result() as $d) {
            $diterima[(int) $d->id_obat] = ($diterima[(int) $d->id_obat] ?? 0) + (int) $d->jumlah_terima;
        }
        $sudah_retur = array();
        foreach ($this->db->select('retur_pengadaan_detail.id_obat, SUM(retur_pengadaan_detail.jumlah) AS jumlah')
            ->join('retur_pengadaan', 'retur_pengadaan.id_retur = retur_pengadaan_detail.id_retur')
            ->where('retur_pengadaan.id_penerimaan', $id_penerimaan)
            ->where_in('retur_pengadaan.status', array('DIAJUKAN', 'SELESAI'))
            ->group_by('retur_pengadaan_detail.id_obat')
            ->get('retur_pengadaan_detail')->result() as $t) {
            $sudah_retur[(int) $t->id_obat] = (int) $t->jumlah;
        }
        $this->db->trans_start();
        $retur = array();
        foreach ($items as $it) {
            if (empty($it['id_obat']) || ! preg_match('/^\d+$/', (string) ($it['jumlah'] ?? '')) || (int) $it['jumlah'] <= 0) {
                $this->db->trans_complete();
                $this->error = 'Jumlah retur harus bilangan bulat positif.';
                return false;
            }
            $id_obat = (int) $it['id_obat'];
            if (! isset($diterima[$id_obat])) {
                $this->db->trans_complete();
                $this->error = "Obat ID {$id_obat} tidak ada pada penerimaan ini.";
                return false;
            }
            $retur[$id_obat] = ($retur[$id_obat] ?? 0) + (int) $it['jumlah'];
            if (empty($it['alasan'])) {
                $this->db->trans_complete();
                $this->error = 'Tiap item retur wajib disertai alasan.';
                return false;
            }
        }
        foreach ($retur as $id_obat => $jml) {
            $maks = $diterima[$id_obat] - ($sudah_retur[$id_obat] ?? 0);
            if ($jml > $maks) {
                $this->db->trans_complete();
                $this->error = "Retur obat ID {$id_obat} melebihi sisa yang dapat diretur ({$maks}).";
                return false;
            }
        }
        $this->db->insert('retur_pengadaan', array(
            'id_penerimaan' => $id_penerimaan,
            'nomor_retur' => nomor_baru('RT', 'retur_pengadaan', 'nomor_retur'),
            'tanggal_retur' => date('Y-m-d H:i:s'),
            'status' => 'DIAJUKAN',
            'keterangan' => trim($keterangan),
        ));
        $id_retur = $this->db->insert_id();
        foreach ($items as $it) {
            $this->db->insert('retur_pengadaan_detail', array(
                'id_retur' => $id_retur,
                'id_obat' => (int) $it['id_obat'],
                'jumlah' => (int) $it['jumlah'],
                'alasan' => trim($it['alasan']),
                'nomor_batch' => ! empty($it['nomor_batch']) ? trim($it['nomor_batch']) : null,
                'tanggal_kadaluarsa' => ! empty($it['tanggal_kadaluarsa']) ? $it['tanggal_kadaluarsa'] : null,
            ));
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false || ! $id_retur) {
            $this->error = 'Gagal menyimpan retur.';
            return false;
        }
        return $id_retur;
    }

    /**
     * Konfirmasi retur (DIAJUKAN -> SELESAI): stok berkurang per batch
     * (batch tertentu bila disebut, sonst FEFO). Penolakan butuh catatan.
     */
    public function konfirmasi_retur($id_retur, $id_user, $setuju = true, $catatan = '')
    {
        $this->load->model('stok/stok_model');
        $this->db->trans_start();
        $row = $this->db->query('SELECT * FROM retur_pengadaan WHERE id_retur = ? FOR UPDATE', array($id_retur))->row();
        if (! $row) {
            $this->db->trans_complete();
            $this->error = 'Retur tidak ditemukan.';
            return false;
        }
        if (! in_array($row->status, array('DIAJUKAN', 'Diajukan'))) {
            $this->db->trans_complete();
            $this->error = "Retur berstatus {$row->status}, tidak dapat diputus.";
            return false;
        }
        if (! $setuju && trim($catatan) === '') {
            $this->db->trans_complete();
            $this->error = 'Penolakan retur wajib disertai catatan.';
            return false;
        }
        if ($setuju) {
            $details = $this->db->where('id_retur', $id_retur)->get('retur_pengadaan_detail')->result();
            foreach ($details as $d) {
                if (! $this->stok_model->keluar((int) $d->id_obat, (int) $d->jumlah, 'RETUR_PEMBELIAN', (int) $id_retur,
                    'Retur ' . $row->nomor_retur, $d->nomor_batch, $d->tanggal_kadaluarsa, $id_user)) {
                    $this->db->trans_complete();
                    $this->error = $this->stok_model->error ?: 'Stok tidak cukup untuk retur.';
                    return false;
                }
            }
        }
        $this->db->where('id_retur', $id_retur)->update('retur_pengadaan', array(
            'status' => $setuju ? 'SELESAI' : 'DITOLAK',
            'diproses_oleh' => (int) $id_user,
            'tanggal_keputusan' => date('Y-m-d H:i:s'),
            'keterangan' => trim($catatan) !== '' ? trim($catatan) : $row->keterangan,
        ));
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->error = 'Gagal memutus retur.';
            return false;
        }
        return true;
    }
    /** Batalkan pesanan yang masih DIPESAN (belum ada penerimaan). */
    public function batalkan($id_pengadaan)
    {
        $pengadaan = $this->find($id_pengadaan);
        if (! $pengadaan) {
            $this->error = 'Pengadaan tidak ditemukan.';
            return false;
        }
        if ($pengadaan->status !== 'DIPESAN') {
            $this->error = "Hanya pesanan Dipesan yang dapat dibatalkan (saat ini {$pengadaan->status}).";
            return false;
        }
        return $this->update($id_pengadaan, array('status' => 'DIBATALKAN'));
    }
}
