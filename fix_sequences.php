<?php
$db = pg_connect('host=localhost port=5432 dbname=apotek user=postgres password=585858');

$tables = [
    'antrian' => 'id_antrian',
    'audit_logs' => 'id_log',
    'diagnosis' => 'id_diagnosis',
    'dokter' => 'id_dokter',
    'kunjungan' => 'id_kunjungan',
    'mutasi_stok' => 'id_mutasi',
    'obat' => 'id_obat',
    'pasien' => 'id_pasien',
    'pelayanan' => 'id_pelayanan',
    'pembayaran' => 'id_pembayaran',
    'pemeriksaan' => 'id_pemeriksaan',
    'penerimaan_obat' => 'id_penerimaan',
    'penerimaan_obat_detail' => 'id_detail',
    'pengadaan_obat' => 'id_pengadaan',
    'pengadaan_obat_detail' => 'id_detail',
    'penjualan_obat' => 'id_penjualan',
    'penjualan_obat_detail' => 'id_detail',
    'permissions' => 'id_permission',
    'pesanan_online' => 'id_pesanan',
    'pesanan_online_detail' => 'id_detail',
    'poli' => 'id_poli',
    'resep' => 'id_resep',
    'resep_detail' => 'id_resep_detail',
    'retur_pengadaan' => 'id_retur',
    'retur_pengadaan_detail' => 'id_detail',
    'roles' => 'id_role',
    'ruangan' => 'id_ruangan',
    'spesialis' => 'id_spesialis',
    'stok_obat' => 'id_stok',
    'supplier' => 'id_supplier',
    'tagihan' => 'id_tagihan',
    'tagihan_detail' => 'id_detail',
    'tindakan' => 'id_tindakan',
    'transaksi' => 'id_transaksi',
    'users' => 'id_user'
];

foreach ($tables as $table => $col) {
    $seq = $table . '_' . $col . '_seq';
    $res = @pg_query($db, "SELECT MAX($col) as max_id FROM $table");
    if ($res) {
        $max_id = (int)pg_fetch_assoc($res)['max_id'];
        if ($max_id == 0) $max_id = 1;
        
        $set = @pg_query($db, "SELECT setval('$seq', $max_id)");
        if ($set) {
            echo "Successfully updated $seq to $max_id\n";
        } else {
            echo "Failed to update $seq (maybe it doesn't exist)\n";
        }
    }
}
