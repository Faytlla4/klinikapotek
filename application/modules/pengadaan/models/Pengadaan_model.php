<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Pengadaan Model (§16).
 *
 * Alur: Pesan (Dipesan) -> Terima (penerimaan Sesuai => stok +) /
 *       Tidak sesuai => retur (Diajukan -> Selesai).
 * Status pengadaan: Dipesan -> Diterima sebagian -> Selesai (+Dibatalkan).
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
     * @param int   $id_supplier
     * @param array $items list array(id_obat, jumlah_pesan, harga?)
     * @return array|bool array(id_pengadaan, nomor_pengadaan, total) atau false.
     */
    public function pesan($id_supplier, $items)
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
            $harga = isset($item['harga']) ? (float) $item['harga'] : (float) $obat->harga;
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
        if (in_array($status_upper, array('SELESAI', 'DIBATALKAN'))) {
            $this->db->trans_complete();
            $this->error = "Pengadaan sudah {$pengadaan->status}.";
            return false;
        }

        // Fase 1: validasi SEMUA item dulu tanpa menulis apa pun
        $detail_po = array();
        foreach ($this->db->where('id_pengadaan', $id_pengadaan)->get('pengadaan_obat_detail')->result() as $detail) {
            $detail_po[(int) $detail->id_obat] = (int) $detail->jumlah_pesan;
        }

        $sudah_diterima = array();
        foreach ($this->db->select('penerimaan_obat_detail.id_obat, SUM(penerimaan_obat_detail.jumlah_terima) AS jumlah')
            ->join('penerimaan_obat', 'penerimaan_obat.id_penerimaan = penerimaan_obat_detail.id_penerimaan')
            ->where('penerimaan_obat.id_pengadaan', $id_pengadaan)
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
            $this->db->where('id_penerimaan', $id_penerimaan)->update('penerimaan_obat', array('status' => 'TIDAK_SESUAI'));
            $this->db->insert('retur_pengadaan', array(
                'id_penerimaan' => $id_penerimaan,
                'nomor_retur' => nomor_baru('RT', 'retur_pengadaan', 'nomor_retur'),
                'tanggal_retur' => date('Y-m-d H:i:s'),
                'status' => 'Diajukan',
                'keterangan' => 'Otomatis dari penerimaan tidak sesuai',
            ));
            $id_retur = $this->db->insert_id();
            foreach ($retur_items as &$r) {
                $r['id_retur'] = $id_retur;
            }
            $this->db->insert_batch('retur_pengadaan_detail', $retur_items);
        }

        // Cek total akumulasi penerimaan setelah transaksi ini untuk menentukan status pengadaan
        $lunas_semua = true;
        foreach ($detail_po as $id_o => $pesan_qty) {
            $terima_total = ($sudah_diterima[$id_o] ?? 0) + ($diminta[$id_o] ?? 0);
            if ($terima_total < $pesan_qty) {
                $lunas_semua = false;
                break;
            }
        }

        $new_status = ($lunas_semua && $semua_sesuai) ? 'SELESAI' : 'DITERIMA_SEBAGIAN';
        $this->db->where('id_pengadaan', $id_pengadaan)->update('pengadaan_obat', array(
            'status' => $new_status,
            'updated_at' => date('Y-m-d H:i:s'),
        ));

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->error = 'Gagal menyimpan penerimaan.';
            return false;
        }

        return array('id_penerimaan' => $id_penerimaan, 'nomor_penerimaan' => $nomor, 'id_retur' => $id_retur);
    }

    /** Batalkan pesanan yang masih Dipesan. */
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
