<?php // Daftar permintaan pengadaan: filter status + tombol buat baru. ?>
<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Permintaan Pengadaan</h3>
    <div class="card-tools"><a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/pengadaan/permintaan_buat'); ?>" class="btn btn-sm btn-primary"><i class="fas fa-plus mr-1"></i>Buat Permintaan</a></div></div>
    <div class="card-body">
        <div class="btn-group mb-3" role="group">
            <a href="<?php echo site_url($this->uri->uri_string()); ?>" class="btn btn-sm <?php echo empty($f_status) ? 'btn-primary' : 'btn-default'; ?>">Semua</a>
            <?php foreach (array('DRAFT','DIAJUKAN','DISETUJUI','DIPROSES','SELESAI','DITOLAK','DIBATALKAN') as $s): ?>
            <a href="<?php echo site_url($this->uri->uri_string() . '?status=' . $s); ?>" class="btn btn-sm <?php echo $f_status === $s ? 'btn-primary' : 'btn-default'; ?>"><?php echo $s; ?></a>
            <?php endforeach; ?>
        </div>
        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
            <thead><tr><th>Nomor</th><th>Tanggal</th><th>Unit</th><th>Item</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($minta_list)): ?><tr><td colspan="6" class="text-center text-muted">Belum ada permintaan.</td></tr>
            <?php else: foreach ($minta_list as $m): ?><tr>
                <td><?php echo html_escape($m->nomor_permintaan); ?></td>
                <td><?php echo date('d-m-Y H:i', strtotime($m->tanggal_permintaan)); ?></td>
                <td><?php echo html_escape($m->unit); ?></td>
                <td class="text-center"><?php echo (int) $m->jml_item; ?></td>
                <td><span class="badge <?php echo $m->status === 'SELESAI' ? 'badge-success' : (in_array($m->status, array('DITOLAK','DIBATALKAN')) ? 'badge-danger' : ($m->status === 'DISETUJUI' ? 'badge-primary' : 'badge-warning')); ?>"><?php echo html_escape($m->status); ?></span></td>
                <td><a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/pengadaan/permintaan_detail/' . (int) $m->id_permintaan); ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
            </tr><?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>
</div></div></div>
