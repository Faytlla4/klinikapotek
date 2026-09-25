<?php defined('BASEPATH') || exit('No direct script access allowed');

  $base = site_url(SITE_AREA . '/cetak');

  $items = array(
      array(
          'url'   => $base . '/kunjungan',
          'label' => 'Laporan Kunjungan',
          'icon'  => 'fa-user-md',
      ),
      array(
          'url'   => $base . '/transaksi',
          'label' => 'Laporan Transaksi',
          'icon'  => 'fa-money-bill-wave',
      ),
      array(
          'url'   => $base . '/antrian',
          'label' => 'Laporan Antrian',
          'icon'  => 'fa-list-ol',
      ),
      array(
          'url'   => $base . '/pendaftaran',
          'label' => 'Laporan Pendaftaran',
          'icon'  => 'fa-user-plus',
      ),
      array(
          'url'   => $base . '/mutasi',
          'label' => 'Laporan Mutasi Obat',
          'icon'  => 'fa-exchange-alt',
      ),
  );

  $current = $this->uri->segment(3);

  echo '<ul class="nav nav-treeview">';

  foreach ($items as $item) {
      $active = '';

      if ($current && strpos($item['url'], '/' . $current) !== false) {
          $active = ' active';
      }

      echo '<li class="nav-item">';
      echo '<a class="nav-link' . $active . '" href="' .
          html_escape($item['url']) . '">';
      echo '<i class="far ' . html_escape($item['icon']) .
          ' nav-icon"></i>';
      echo '<p>' . html_escape($item['label']) . '</p>';
      echo '</a>';
      echo '</li>';
  }

  echo '</ul>';