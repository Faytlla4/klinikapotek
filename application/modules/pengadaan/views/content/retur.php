<?php // Daftar retur pembelian: filter status + tombol kembali ke PO terkait via detail. ?>
<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Retur Pembelian</h3></div>
    <div class="card-body">
        <div class="btn-group mb-3" role="group">
            <a href="<?php echo site_url($this->uri->uri_string()); ?>" class="btn btn-sm <?php echo empty($f_status) ? 'btn-primary' : 'btn-default'; ?>">Semua</a>
            <?php foreach (array('DIAJUKAN','SELESAI','DITOLAK') as $s): ?>
            <a href="<?php echo site_url($this->uri->uri_string() . '?status=' . $s); ?>" class="btn btn-sm <?php echo $f_status === $s ? 'btn-primary' : 'btn-default'; ?>"><?php echo $s; ?></a>
            <?php endforeach; ?>
        </div>
        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
            <thead><tr><th>Nomor Retur</th><th>Tanggal</th><th>Penerimaan</th><th>Supplier</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($retur_list)): ?><tr><td colspan="6" class="text-center text-muted">Belum ada retur.</td></tr>
            <?php else: foreach ($retur_list as $r): ?><tr>
                <td><?php echo html_escape($r->nomor_retur); ?></td>
                <td><?php echo date('d-m-Y H:i', strtotime($r->tanggal_retur)); ?></td>
                <td><?php echo html_escape($r->nomor_penerimaan); ?><br><small class="text-muted"><?php echo html_escape($r->nomor_pengadaan); ?></small></td>
                <td><?php echo html_escape($r->nama_supplier); ?></td>
                <td><span class="badge <?php echo $r->status === 'SELESAI' ? 'badge-success' : ($r->status === 'DITOLAK' ? 'badge-danger' : 'badge-warning'); ?>"><?php echo html_escape($r->status); ?></span></td>
                <td><a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/pengadaan/retur_detail/' . (int) $r->id_retur); ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
            </tr><?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>
</div></div></div>
<div class="row"><div class="col-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Buat Retur Baru — Pilih Penerimaan</h3></div>
    <div class="card-body">
        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
            <thead><tr><th>No. Penerimaan</th><th>Tanggal</th><th>PO</th><th>Supplier</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($terima_list)): ?><tr><td colspan="5" class="text-center text-muted">Belum ada penerimaan yang dikonfirmasi.</td></tr>
            <?php else: foreach ($terima_list as $t): ?><tr>
                <td><?php echo html_escape($t->nomor_penerimaan); ?></td>
                <td><?php echo date('d-m-Y H:i', strtotime($t->tanggal_terima)); ?></td>
                <td><?php echo html_escape($t->nomor_pengadaan); ?></td>
                <td><?php echo html_escape($t->nama_supplier); ?></td>
                <td><a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/pengadaan/retur_buat/' . (int) $t->id_penerimaan); ?>" class="btn btn-sm btn-danger"><i class="fas fa-undo mr-1"></i>Buat Retur</a></td>
            </tr><?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>
</div></div></div>
