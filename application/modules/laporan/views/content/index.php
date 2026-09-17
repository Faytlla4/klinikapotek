<div class="row">
  <div class="col-12">
    <div class="card card-primary card-outline card-tabs">
      <div class="card-header p-0 pt-1 border-bottom-0">
        <ul class="nav nav-tabs" role="tablist">
          <?php
          $tabs = array(
              'kunjungan'      => 'Kunjungan',
              'pendapatan'     => 'Pendapatan',
              'penjualan_obat' => 'Penjualan Obat',
              'resep'          => 'Resep',
              'mutasi_stok'    => 'Mutasi Stok',
          );
          foreach ($tabs as $key => $label) :
              $active = ($report === $key) ? ' active' : '';
          ?>
          <li class="nav-item">
            <a class="nav-link<?php echo $active; ?>" href="<?php echo site_url(SITE_AREA.'/content/laporan?report='.$key.'&dari='.$dari.'&sampai='.$sampai); ?>">
              <?php echo $label; ?>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="card-body">
        <form method="get" action="<?php echo site_url(SITE_AREA.'/content/laporan'); ?>" class="form-inline mb-3">
          <input type="hidden" name="report" value="<?php echo html_escape($report); ?>">
          <label class="mr-2">Dari</label>
          <input type="date" name="dari" class="form-control form-control-sm mr-3" value="<?php echo html_escape($dari); ?>">
          <label class="mr-2">Sampai</label>
          <input type="date" name="sampai" class="form-control form-control-sm mr-3" value="<?php echo html_escape($sampai); ?>">
          <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        </form>

        <?php if ($report === 'kunjungan') : ?>
        <table class="table table-bordered table-striped table-sm">
          <thead><tr><th>Status</th><th>Jumlah</th></tr></thead>
          <tbody>
          <?php if (empty($data_laporan)) : ?>
            <tr><td colspan="2" class="text-center text-muted">Tidak ada data</td></tr>
          <?php else : foreach ($data_laporan as $row) : ?>
            <tr><td><?php echo html_escape($row->status); ?></td><td><?php echo (int) $row->jumlah; ?></td></tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>

        <?php elseif ($report === 'pendapatan') : ?>
        <table class="table table-bordered table-striped table-sm">
          <thead><tr><th>Tanggal</th><th>Omzet (Rp)</th></tr></thead>
          <tbody>
          <?php if (empty($data_laporan)) : ?>
            <tr><td colspan="2" class="text-center text-muted">Tidak ada data</td></tr>
          <?php else : foreach ($data_laporan as $row) : ?>
            <tr><td><?php echo html_escape($row->tanggal); ?></td><td><?php echo 'Rp '.number_format((float)$row->omzet, 0, ',', '.'); ?></td></tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>

        <?php elseif ($report === 'penjualan_obat') : ?>
        <table class="table table-bordered table-striped table-sm">
          <thead><tr><th>Kode Obat</th><th>Nama Obat</th><th>Qty</th><th>Omzet (Rp)</th></tr></thead>
          <tbody>
          <?php if (empty($data_laporan)) : ?>
            <tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>
          <?php else : foreach ($data_laporan as $row) : ?>
            <tr><td><?php echo html_escape($row->kode_obat); ?></td><td><?php echo html_escape($row->nama_obat); ?></td><td><?php echo (int) $row->qty; ?></td><td><?php echo 'Rp '.number_format((float)$row->omzet, 0, ',', '.'); ?></td></tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>

        <?php elseif ($report === 'resep') : ?>
        <table class="table table-bordered table-striped table-sm">
          <thead><tr><th>Status</th><th>Jumlah</th></tr></thead>
          <tbody>
          <?php if (empty($data_laporan)) : ?>
            <tr><td colspan="2" class="text-center text-muted">Tidak ada data</td></tr>
          <?php else : foreach ($data_laporan as $row) : ?>
            <tr><td><?php echo html_escape($row->status); ?></td><td><?php echo (int) $row->jumlah; ?></td></tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>

        <?php elseif ($report === 'mutasi_stok') : ?>
        <table class="table table-bordered table-striped table-sm">
          <thead><tr><th>Nama Obat</th><th>Jenis Mutasi</th><th>Jumlah</th></tr></thead>
          <tbody>
          <?php if (empty($data_laporan)) : ?>
            <tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr>
          <?php else : foreach ($data_laporan as $row) : ?>
            <tr><td><?php echo html_escape($row->nama_obat); ?></td><td><?php echo html_escape($row->jenis_mutasi); ?></td><td><?php echo (int) $row->qty; ?></td></tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>
