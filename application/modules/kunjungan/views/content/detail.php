<?php $this->load->view('transaksi/partials/_nota'); ?>
<div class="nota-screen">
<div class="row"><div class="col-md-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Detail Kunjungan</h3></div>
    <div class="card-body"><dl class="row">
        <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->tanggal_kunjungan); ?></dd>
        <dt class="col-sm-3">No. RM</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->no_rm); ?></dd>
        <dt class="col-sm-3">Pasien</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->nama_pasien); ?></dd>
        <dt class="col-sm-3">Pelayanan</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->nama_pelayanan); ?></dd>
        <dt class="col-sm-3">Poli</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->nama_poli); ?></dd>
        <dt class="col-sm-3">Dokter</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->nama_dokter); ?></dd>
        <dt class="col-sm-3">Ruangan</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->nama_ruangan); ?></dd>
        <dt class="col-sm-3">Status</dt>
        <dd class="col-sm-9">
            <?php
            $badge = array(
                'TERDAFTAR' => 'badge-primary',
                'MENUNGGU'  => 'badge-warning',
                'DIPROSES'  => 'badge-info',
                'SELESAI'   => 'badge-success',
                'BATAL'     => 'badge-danger',
            );
            $cls = isset($badge[$kunjungan->status]) ? $badge[$kunjungan->status] : 'badge-secondary';
            ?>
            <span class="badge <?php echo $cls; ?> p-2"><?php echo html_escape($kunjungan->status); ?></span>
        </dd>
    </dl></div>

    <?php
    $alur = array(
        'TERDAFTAR' => array('MENUNGGU', 'BATAL'),
        'MENUNGGU'  => array('DIPROSES',  'BATAL'),
        'DIPROSES'  => array('SELESAI',   'BATAL'),
        'SELESAI'   => array(),
        'BATAL'     => array(),
    );
    $status_berikut = isset($alur[$kunjungan->status]) ? $alur[$kunjungan->status] : array();
    $label_btn = array(
        'MENUNGGU' => array('label' => 'Tandai Menunggu',    'class' => 'btn-warning'),
        'DIPROSES' => array('label' => 'Tandai Diproses',    'class' => 'btn-info'),
        'SELESAI'  => array('label' => 'Tandai Selesai',     'class' => 'btn-success'),
        'BATAL'    => array('label' => 'Batalkan Kunjungan', 'class' => 'btn-danger'),
    );
    // Cek apakah sudah ada tagihan aktif
    $ada_tagihan = $this->db
        ->where('id_kunjungan', $kunjungan->id_kunjungan)
        ->where('status !=', 'BATAL')
        ->get('tagihan')->row();
    ?>

    <div class="card-footer d-flex align-items-center justify-content-between flex-wrap" style="gap:8px">
        <div class="d-flex" style="gap:8px">
            <a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3)); ?>" class="btn btn-default">Kembali</a>
            <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> Cetak</button>
        </div>

        <div class="d-flex flex-wrap" style="gap:8px">
            <?php foreach ($status_berikut as $st): ?>
                <?php $btn = isset($label_btn[$st]) ? $label_btn[$st] : array('label' => $st, 'class' => 'btn-secondary'); ?>
                <?php echo form_open(site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3) . '/ubah_status/' . $kunjungan->id_kunjungan)); ?>
                    <input type="hidden" name="status" value="<?php echo $st; ?>">
                    <button type="submit" class="btn <?php echo $btn['class']; ?>"
                        <?php if ($st === 'BATAL'): ?>onclick="return confirm('Yakin ingin membatalkan kunjungan ini?')"<?php endif; ?>>
                        <?php echo $btn['label']; ?>
                    </button>
                <?php echo form_close(); ?>
            <?php endforeach; ?>

            <?php if ($kunjungan->status === 'SELESAI' && ! $ada_tagihan): ?>
                <button type="button" id="btn_susun_tagihan" class="btn btn-primary"
                        data-id="<?php echo (int) $kunjungan->id_kunjungan; ?>">
                    <i class="fas fa-file-invoice-dollar"></i> Susun Tagihan
                </button>
            <?php elseif ($ada_tagihan): ?>
                <a href="<?php echo site_url(SITE_AREA . '/transaksi/tagihan/detail/' . $ada_tagihan->id_tagihan); ?>" class="btn btn-info">
                    <i class="fas fa-receipt"></i> Lihat Tagihan
                </a>
            <?php endif; ?>
        </div>
    </div>
</div></div></div>
</div>
<div class="nota-print">
    <div class="nota">
        <div class="nota-head"><h4>Klinik &amp; Apotek</h4><p>Bukti Kunjungan</p></div>
        <div class="nota-row"><span>Tanggal</span><span><?php echo html_escape($kunjungan->tanggal_kunjungan); ?></span></div>
        <div class="nota-row"><span>Pasien</span><span><?php echo html_escape($kunjungan->no_rm . ' — ' . $kunjungan->nama_pasien); ?></span></div>
        <div class="nota-row"><span>Pelayanan</span><span><?php echo html_escape($kunjungan->nama_pelayanan); ?></span></div>
        <div class="nota-row"><span>Poli / Ruangan</span><span><?php echo html_escape($kunjungan->nama_poli . ' / ' . $kunjungan->nama_ruangan); ?></span></div>
        <div class="nota-row"><span>Dokter</span><span><?php echo html_escape($kunjungan->nama_dokter); ?></span></div>
        <div class="nota-row"><span>Status</span><strong><?php echo html_escape($kunjungan->status); ?></strong></div>
        <div class="nota-foot">Terima kasih atas kunjungan Anda.<br>Semoga lekas sembuh.</div>
    </div>
</div>

<script>
// Inline script view dieksekusi sebelum jQuery dimuat (di akhir body) — tunggu DOM siap.
document.addEventListener('DOMContentLoaded', function () {
    if (typeof jQuery === 'undefined') {
        return;
    }
    $('#btn_susun_tagihan').on('click', function () {
        var idKunjungan = $(this).data('id');
        var $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
        $.ajax({
            url: site_url + 'admin/transaksi/tagihan/api/susun/' + idKunjungan,
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    alert('Tagihan ' + res.data.nomor_tagihan + ' berhasil dibuat.\nTotal: Rp ' + Number(res.data.total).toLocaleString('id-ID'));
                    window.location.reload();
                } else {
                    alert('Gagal: ' + (res.error || 'Error tidak diketahui'));
                    $btn.prop('disabled', false).html('<i class="fas fa-file-invoice-dollar"></i> Susun Tagihan');
                }
            },
            error: function (xhr) {
                alert('Terjadi kesalahan. Pastikan Anda punya akses kelola_tagihan.');
                $btn.prop('disabled', false).html('<i class="fas fa-file-invoice-dollar"></i> Susun Tagihan');
            }
        });
    });
});
</script>
