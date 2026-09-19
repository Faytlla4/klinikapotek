<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Penjualan Model (§14, §15).
 *
 * jenis_penjualan: RESEP (dari resep dokter) atau LANGSUNG (pembelian langsung).
 * Setiap item mengurangi stok via Stok_model (gagal bila stok tak cukup).
 * Status penjualan: SELESAI (+BATAL; pembatalan tidak mengembalikan stok otomatis).
 */
class Penjualan_model extends BF_Model
{
    protected $table_name = 'penjualan_obat';
    protected $key = 'id_penjualan';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array('field' => 'jenis_penjualan', 'label' => 'Jenis', 'rules' => 'required'),
    );
    protected $insert_validation_rules = array();
    protected $skip_validation = false;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Buat penjualan + kurangi stok per item (transaksi tunggal).
     *
     * @param string   $jenis RESEP|LANGSUNG
     * @param int|null $id_resep  wajib bila RESEP
     * @param int|null $id_pasien
     * @param array    $items   list array(id_obat, jumlah)
     * @return array|bool array(id_penjualan, nomor_penjualan, total) atau false.
     */
    public function jual($jenis, $id_resep, $id_pasien, $items)
    {
        $jenis = strtoupper($jenis);
        if (! in_array($jenis, array('RESEP', 'LANGSUNG', 'ONLINE'))) {
            $this->error = 'Jenis penjualan harus RESEP, LANGSUNG, atau ONLINE.';
            return false;
        }
        if ($jenis === 'RESEP' && empty($id_resep)) {
            $this->error = 'Penjualan resep wajib menyertakan id_resep.';
            return false;
        }
        if (in_array($jenis, array('RESEP', 'ONLINE')) && ! empty($id_resep)) {
            $resep = $this->db->where('id_resep', $id_resep)->get('resep')->row();
            if (! $resep || in_array($resep->status, array('DISERAHKAN', 'BATAL'))) {
                $this->error = 'Resep tidak tersedia atau sudah diserahkan.';
                return false;
            }
            if ($id_pasien && (int) $resep->id_pasien !== (int) $id_pasien) {
                $this->error = 'Pasien tidak sesuai resep.';
                return false;
            }
            if ($this->db->where('id_resep', $id_resep)->where('status', 'SELESAI')->get('penjualan_obat')->row()) {
                $this->error = 'Resep sudah diproses.';
                return false;
            }
        }
        if (empty($items)) {
            $this->error = 'Item penjualan kosong.';
            return false;
        }

        $this->load->model('stok/stok_model');
        $this->db->trans_start();
        // Kunci: cek & kunci stok SEMUA item dulu (FOR UPDATE) sebelum ada
        // tulisan apa pun, agar kegagalan logis tak pernah terjadi
        // setelah insert (nested trans_complete akan COMMIT bila
        // trans_status masih TRUE).
        $total = 0;
        $rows = array();
        foreach ($items as $item) {
            if (empty($item['id_obat']) || ! preg_match('/^\d+$/', (string) ($item['jumlah'] ?? '')) || (int) $item['jumlah'] <= 0) {
                $this->db->trans_complete();
                $this->error = 'Jumlah obat harus bilangan bulat positif.';
                return false;
            }
            $obat = $this->db->where('id_obat', $item['id_obat'])->get('obat')->row();
            if (! $obat || $obat->status !== 'AKTIF') {
                $this->db->trans_complete();
                $this->error = "Obat ID {$item['id_obat']} tidak tersedia.";
                return false;
            }
            if (in_array($jenis, array('RESEP', 'ONLINE')) && ! empty($id_resep)) {
                $rd = $this->db->where(array('id_resep' => $id_resep, 'id_obat' => $item['id_obat']))->get('resep_detail')->row();
                if (! $rd || (int) $item['jumlah'] > (int) $rd->jumlah) {
                    $this->db->trans_complete();
                    $this->error = 'Jumlah obat melebihi detail resep.';
                    return false;
                }
            } elseif (in_array($obat->wajib_resep, array(true, 1, '1', 't', 'T'), true)) {
                $this->db->trans_complete();
                $this->error = 'Obat wajib resep.';
                return false;
            }
            $stok_row = $this->db->query('SELECT jumlah_stok FROM stok_obat WHERE id_obat = ? FOR UPDATE', array($item['id_obat']))->row();
            $stok_tersedia = $stok_row ? (int) $stok_row->jumlah_stok : 0;
            if ($stok_tersedia < (int) $item['jumlah']) {
                $this->db->trans_complete();
                $this->error = "Stok tidak cukup (tersedia {$stok_tersedia}, diminta {$item['jumlah']}).";
                return false;
            }
            $harga = (float) $obat->harga;
            $subtotal = $harga * (int) $item['jumlah'];
            $total += $subtotal;
            $rows[] = array(
                'id_obat'  => $item['id_obat'],
                'jumlah'   => (int) $item['jumlah'],
                'harga'    => $harga,
                'subtotal' => $subtotal,
            );
        }

        $id_penjualan = $this->insert(array(
            'id_resep'         => in_array($jenis, array('RESEP', 'ONLINE')) ? ($id_resep ?: null) : null,
            'id_pasien'        => $id_pasien,
            'nomor_penjualan'  => nomor_baru('PJ', 'penjualan_obat', 'nomor_penjualan'),
            'tanggal_penjualan'=> date('Y-m-d H:i:s'),
            'jenis_penjualan'  => $jenis,
            'total'            => $total,
            'status'           => 'SELESAI',
        ));
        if (! $id_penjualan) {
            $this->db->trans_complete();
            $this->error = 'Gagal menyimpan penjualan.';
            return false;
        }
        foreach ($rows as &$r) {
            $r['id_penjualan'] = $id_penjualan;
        }
        $this->db->insert_batch('penjualan_obat_detail', $rows);

        foreach ($rows as $r) {
            if (! $this->stok_model->keluar($r['id_obat'], $r['jumlah'], 'PENJUALAN', $id_penjualan)) {
                $this->db->trans_complete();
                $this->error = $this->stok_model->error ?: 'Stok tidak cukup.';
                return false;
            }
        }
        if (in_array($jenis, array('RESEP', 'ONLINE')) && ! empty($id_resep)) {
            $this->db->where('id_resep', $id_resep)->update('resep', array('status' => 'DISERAHKAN'));
        }
        if ($jenis === 'RESEP') {
            // Penjualan resep harus langsung masuk antrean tagihan agar
            // administrasi dapat menagih obat tanpa menunggu input manual.
            $this->load->model('tagihan/tagihan_model');
            $tagihan = $this->tagihan_model->tambahkan_penjualan_resep($id_penjualan);
            if (! $tagihan) {
                $this->db->trans_rollback();
                $this->error = $this->tagihan_model->error ?: 'Gagal membuat tagihan resep.';
                return false;
            }
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->error = 'Transaksi penjualan gagal.';
            return false;
        }
        $nomor = $this->db->select('nomor_penjualan')->where('id_penjualan', $id_penjualan)
            ->get('penjualan_obat')->row()->nomor_penjualan;
        return array('id_penjualan' => $id_penjualan, 'nomor_penjualan' => $nomor, 'total' => $total);
    }

    /** Detail penjualan + item + nama obat. */
    public function detail($id_penjualan)
    {
        $row = $this->find($id_penjualan);
        if (! $row) {
            return false;
        }
        $row->items = $this->db->select('penjualan_obat_detail.*, obat.nama_obat, obat.satuan')
            ->join('obat', 'obat.id_obat = penjualan_obat_detail.id_obat')
            ->where('id_penjualan', $id_penjualan)
            ->get('penjualan_obat_detail')
            ->result();
        return $row;
    }
}
