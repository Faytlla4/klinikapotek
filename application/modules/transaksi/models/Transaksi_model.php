<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Transaksi & Pembayaran Model (§17).
 *
 * Alur: Buat Tagihan -> bayar(jumlah) [boleh bertahap] ->
 *   total bayar >= total tagihan => tagihan Lunas + transaksi Selesai (cetak),
 *   bila dibatalkan sebelum lunas => transaksi Batal.
 */
class Transaksi_model extends BF_Model
{
    protected $table_name = 'transaksi';
    protected $key = 'id_transaksi';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;

    protected $validation_rules = array();
    protected $insert_validation_rules = array();
    protected $skip_validation = true;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Proses pembayaran tagihan (satu pembayaran per transaksi, sesuai
     * constraint UNIQUE pembayaran.id_transaksi).
     *
     * Flowchart: bayar -> Lunas? YA = tagihan LUNAS + transaksi LUNAS (cetak),
     * TIDAK (uang kurang) = tolak tanpa efek samping; panggil batalkan().
     *
     * @param int   $id_tagihan
     * @param float $jumlah_bayar
     * @return array|bool array(status, dibayar, sisa, kembalian, ...) atau false.
     */
    public function bayar($id_tagihan, $jumlah_bayar)
    {
        $jumlah_bayar = (float) $jumlah_bayar;
        if ($jumlah_bayar <= 0) {
            $this->error = 'Jumlah bayar harus positif.';
            return false;
        }
        $this->load->model('tagihan/tagihan_model');
        $tagihan = $this->tagihan_model->detail($id_tagihan);
        if (! $tagihan) {
            $this->error = 'Tagihan tidak ditemukan.';
            return false;
        }
        if ($tagihan->status !== 'BELUM_DIBAYAR') {
            $this->error = "Tagihan sudah {$tagihan->status}.";
            return false;
        }
        if ($tagihan->total <= 0) {
            $this->error = 'Total tagihan harus lebih besar dari nol.';
            return false;
        }
        $sisa = (float) $tagihan->total - (float) $tagihan->sudah_dibayar;
        if ($jumlah_bayar < $sisa) {
            $this->error = 'Uang kurang ' . number_format($sisa - $jumlah_bayar, 0, ',', '.') . '.';
            return false;
        }

        $this->db->trans_start();
        $trx = $this->db->where(array('id_tagihan' => $id_tagihan, 'status' => 'BELUM_DIBAYAR'))
            ->get('transaksi')->row();
        if ($trx && $this->db->where('id_transaksi', $trx->id_transaksi)->get('pembayaran')->row()) {
            $this->db->trans_complete();
            $this->error = 'Transaksi ini sudah dibayar.';
            return false;
        }
        if (! $trx) {
            $id_transaksi = $this->insert(array(
                'id_tagihan' => $id_tagihan,
                'nomor_transaksi' => nomor_baru('TR', 'transaksi', 'nomor_transaksi'),
                'tanggal_transaksi' => date('Y-m-d H:i:s'),
                'total' => (float) $tagihan->total,
                'status' => 'BELUM_DIBAYAR',
            ));
        } else {
            $id_transaksi = $trx->id_transaksi;
        }

        $kembalian = $jumlah_bayar - $sisa;
        $this->db->insert('pembayaran', array(
            'id_transaksi' => $id_transaksi,
            'tanggal_pembayaran' => date('Y-m-d H:i:s'),
            'jumlah_bayar' => $jumlah_bayar,
            'kembalian' => $kembalian,
            'status' => 'LUNAS',
        ));
        $this->db->where('id_tagihan', $id_tagihan)->update('tagihan', array('status' => 'LUNAS'));
        $this->db->where('id_transaksi', $id_transaksi)->update('transaksi', array('status' => 'LUNAS'));
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->error = 'Gagal memproses pembayaran.';
            return false;
        }
        return array(
            'id_transaksi' => $id_transaksi, 'status' => 'LUNAS',
            'dibayar' => (float) $tagihan->total, 'sisa' => 0, 'kembalian' => $kembalian,
        );
    }

    /** Batalkan transaksi aktif (tagihan kembali Belum bayar). */
    public function batalkan($id_transaksi)
    {
        $trx = $this->find($id_transaksi);
        if (! $trx) {
            $this->error = 'Transaksi tidak ditemukan.';
            return false;
        }
        if ($trx->status !== 'BELUM_DIBAYAR') {
            $this->error = "Hanya transaksi BELUM_DIBAYAR yang dapat dibatalkan (saat ini {$trx->status}).";
            return false;
        }
        $this->db->trans_start();
        $this->update($id_transaksi, array('status' => 'BATAL'));
        $this->db->where('id_transaksi', $id_transaksi)->delete('pembayaran');
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /** Bukti transaksi (cetak): transaksi + pembayaran + tagihan + item. */
    public function bukti($id_transaksi)
    {
        $trx = $this->find($id_transaksi);
        if (! $trx) {
            return false;
        }
        $trx->pembayaran = $this->db->where('id_transaksi', $id_transaksi)
            ->order_by('id_pembayaran', 'ASC')->get('pembayaran')->result();
        $trx->items = $this->db->where('id_tagihan', $trx->id_tagihan)->get('tagihan_detail')->result();
        return $trx;
    }
}
