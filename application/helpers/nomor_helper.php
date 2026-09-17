<?php defined('BASEPATH') || exit('No direct script access allowed');

if (! function_exists('nomor_baru')) {
    /**
     * Generate nomor dokumen unik: PREFIX-YYYYMMDD-#### (urutan harian per tabel).
     *
     * @param string $prefix  Prefix nomor (mis. RM, KJ, AN, RS, PJ, TG, TR, PO).
     * @param string $tabel   Nama tabel untuk hitung urutan harian.
     * @param string $kolom   Kolom nomor di tabel tersebut.
     * @return string Nomor baru.
     */
    function nomor_baru($prefix, $tabel, $kolom)
    {
        $ci = &get_instance();
        $today = date('Ymd');
        // ponytail: like() CI meng-escape wildcard % dan _ secara default,
        // sehingga pola harus diberikan via side='after' + escape=false.
        $row = $ci->db->select($kolom)
            ->like($kolom, $prefix . '-' . $today . '-', 'after', false)
            ->order_by($kolom, 'DESC')
            ->limit(1)
            ->get($tabel)
            ->row();
        $seq = 1;
        if ($row && preg_match('/-(\d+)$/', $row->{$kolom}, $m)) {
            $seq = ((int) $m[1]) + 1;
        }
        return sprintf('%s-%s-%04d', $prefix, $today, $seq);
    }
}
