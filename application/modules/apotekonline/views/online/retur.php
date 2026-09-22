<div class="row"><div class="col-md-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title"><i class="fas fa-undo"></i> Retur Online</h3></div>
    <div class="card-body">
        <div class="btn-group mb-3" role="group">
            <a href="<?php echo site_url(SITE_AREA . '/apotek/retur'); ?>" class="btn btn-sm <?php echo empty($f_status) ? 'btn-primary' : 'btn-default'; ?>">Semua</a>
            <a href="<?php echo site_url(SITE_AREA . '/apotek/retur?status=DIMINTA'); ?>" class="btn btn-sm <?php echo $f_status === 'DIMINTA' ? 'btn-primary' : 'btn-default'; ?>">Diminta</a>
            <a href="<?php echo site_url(SITE_AREA . '/apotek/retur?status=SELESAI'); ?>" class="btn btn-sm <?php echo $f_status === 'SELESAI' ? 'btn-primary' : 'btn-default'; ?>">Selesai</a>
            <a href="<?php echo site_url(SITE_AREA . '/apotek/retur?status=DITOLAK'); ?>" class="btn btn-sm <?php echo $f_status === 'DITOLAK' ? 'btn-primary' : 'btn-default'; ?>">Ditolak</a>
        </div>
        <div class="table-responsive"><table class="table table-bordered table-striped">
            <thead><tr><th>Nomor Retur</th><th>Pesanan</th><th>Pasien</th><th>Pengajuan</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($retur_list)): ?><tr><td colspan="6" class="text-center text-muted">Belum ada pengajuan retur.</td></tr><?php endif; ?>
            <?php foreach ($retur_list as $r): ?><tr>
                <td><?php echo html_escape($r->nomor_retur); ?></td>
                <td><?php echo html_escape($r->nomor_pesanan); ?><br><small class="text-muted">Rp <?php echo number_format((float) $r->total, 0, ',', '.'); ?></small></td>
                <td><?php echo html_escape($r->nama_pasien); ?></td>
                <td><?php echo html_escape($r->tanggal_pengajuan); ?></td>
                <td><span class="badge <?php echo $r->status === 'SELESAI' ? 'badge-success' : ($r->status === 'DITOLAK' ? 'badge-danger' : 'badge-warning'); ?>"><?php echo html_escape($r->status); ?></span></td>
                <td><a href="<?php echo site_url(SITE_AREA . '/apotek/retur/' . (int) $r->id_retur); ?>" class="btn btn-sm btn-info"><i class="fas fa-gavel"></i> Putuskan</a></td>
            </tr><?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
</div></div></div>
