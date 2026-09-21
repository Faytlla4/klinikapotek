<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__ . '/Content.php';

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/** Laporan Cetak dalam context LAPORAN CETAK (terpisah dari LAPORAN). */
class Cetak extends Content
{
    public function index()
    {
        redirect(SITE_AREA . '/cetak/kunjungan');
    }

    /**
     * Halaman cetak per jenis: kunjungan | transaksi | antrian | pendaftaran.
     * Filter tanggal auto-submit; tombol Excel (.xls) + Cetak/PDF via browser.
     */
    public function lihat($jenis = null)
    {
        $cetak = array(
            'kunjungan'   => 'Laporan Kunjungan',
            'transaksi'   => 'Laporan Transaksi',
            'antrian'     => 'Laporan Antrian',
            'pendaftaran' => 'Laporan Pendaftaran Pasien',
        );
        if (! isset($cetak[$jenis])) {
            redirect(SITE_AREA . '/cetak');
        }
        list($dari, $sampai) = $this->rentang();
        $method = 'cetak_' . $jenis;
        Template::set(array(
            'jenis'  => $jenis,
            'judul'  => $cetak[$jenis],
            'dari'   => $dari,
            'sampai' => $sampai,
            'rows'   => $this->laporan_model->{$method}($dari, $sampai),
        ));
        Template::set('toolbar_title', $cetak[$jenis]);
        Template::set_view('content/cetak');
        Template::render();
    }

    /** Unduh Excel (.xlsx asli) untuk jenis laporan cetak. */
    public function xlsx($jenis = null)
    {
        $judul = array(
            'kunjungan'   => 'Laporan Kunjungan',
            'transaksi'   => 'Laporan Transaksi',
            'antrian'     => 'Laporan Antrian',
            'pendaftaran' => 'Laporan Pendaftaran Pasien',
        );
        if (! isset($judul[$jenis])) {
            redirect(SITE_AREA . '/cetak');
        }
        list($dari, $sampai) = $this->rentang();
        $rows = $this->laporan_model->{'cetak_' . $jenis}($dari, $sampai);

        $kolom = array(
            'kunjungan'   => array('Tanggal', 'No. RM', 'Pasien', 'Pelayanan', 'Poli', 'Dokter', 'Status'),
            'transaksi'   => array('Nomor', 'Tanggal', 'Total', 'Status'),
            'antrian'     => array('Nomor', 'Tanggal', 'Poli', 'Pasien', 'Status'),
            'pendaftaran' => array('No. RM', 'Nama', 'NIK', 'Terdaftar'),
        );
        $map = array(
            'kunjungan'   => array('tanggal_kunjungan', 'no_rm', 'nama_pasien', 'nama_pelayanan', 'nama_poli', 'nama_dokter', 'status'),
            'transaksi'   => array('nomor_transaksi', 'tanggal_transaksi', 'total', 'status'),
            'antrian'     => array('nomor_antrian', 'tanggal_antrian', 'nama_poli', 'nama_pasien', 'status'),
            'pendaftaran' => array('no_rm', 'nama', 'nik', 'created_at'),
        );
        // Kolom yang wajib teks (awet nol depan, mis. NIK) + kolom yang tampil '-' jika kosong.
        $teks  = array(
            'kunjungan'   => array('no_rm'),
            'transaksi'   => array('nomor_transaksi'),
            'antrian'     => array('nomor_antrian'),
            'pendaftaran' => array('no_rm', 'nik'),
        );
        $strip = array('nama_pelayanan', 'nama_poli', 'nama_dokter', 'nik');

        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle(mb_substr($judul[$jenis], 0, 31));
        $sheet->setCellValue('A1', $judul[$jenis]);
        $sheet->setCellValue('A2', 'Periode ' . $dari . ' s/d ' . $sampai);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $n = count($kolom[$jenis]);
        $rh = 4;
        $sheet->fromArray($kolom[$jenis], null, 'A' . $rh);
        $last = Coordinate::stringFromColumnIndex($n);
        $sheet->getStyle('A' . $rh . ':' . $last . $rh)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $rh . ':' . $last . $rh)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('059669');

        $r = $rh + 1;
        $awal = $r;
        foreach ($rows as $row) {
            $c = 1;
            foreach ($map[$jenis] as $k) {
                $v = isset($row->{$k}) ? $row->{$k} : '';
                if (in_array($k, $strip) && ($v === null || $v === '')) {
                    $v = '-';
                }
                $sel = Coordinate::stringFromColumnIndex($c) . $r;
                if ($jenis === 'transaksi' && $k === 'total') {
                    $sheet->setCellValue($sel, (float) $v);
                    $sheet->getStyle($sel)->getNumberFormat()->setFormatCode('"Rp" #,##0');
                } elseif (in_array($k, $teks[$jenis])) {
                    $sheet->setCellValueExplicit($sel, (string) $v, DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($sel, $v);
                }
                $c++;
            }
            $r++;
        }
        if ($jenis === 'transaksi' && ! empty($rows)) {
            $sheet->setCellValue('A' . $r, 'Grand Total');
            $sheet->mergeCells('A' . $r . ':B' . $r);
            $sheet->getStyle('A' . $r)->getFont()->setBold(true);
            $sheet->setCellValue('C' . $r, '=SUM(C' . $awal . ':C' . ($r - 1) . ')');
            $sheet->getStyle('C' . $r)->getFont()->setBold(true);
            $sheet->getStyle('C' . $r)->getNumberFormat()->setFormatCode('"Rp" #,##0');
        }
        for ($c = 1; $c <= $n; $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="laporan-' . $jenis . '-' . $dari . '-' . $sampai . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spread);
        $writer->save('php://output');
        exit;
    }
}
