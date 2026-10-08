# PRODUCT.md — Klinik & Apotek

## Apa ini
Aplikasi web klinik + apotek: rekam medis (kunjungan, pemeriksaan, resep),
penjualan obat, apotek online (keranjang, pesanan, retur), tagihan/pembayaran,
stok & pengadaan, laporan/cetak, backup. Bahasa Indonesia.

## Stack
CodeIgniter 3.1 + Bonfire HMVC, PostgreSQL, AdminLTE 4 + Bootstrap 4,
FontAwesome, Chart.js (vendored, `public/assets/plugins/chart.js`).
Base URL lokal: `http://localhost/klinikapotek/public/`.

## Pengguna (role)
- **Pasien** — dashboard ringkas, belanja obat, lacak pesanan/retur, antrean, riwayat.
- **Admin pelayanan** — pendaftaran, kunjungan, antrean.
- **Dokter** — pemeriksaan, resep, dashboard antrean.
- **Apoteker** — penjualan, proses pesanan online, retur, stok, pengadaan.
- **Admin sistem** — user/role, backup, pengaturan.

## Permukaan UI
- **Publik** (`public/themes/default/`): landing + login, CSS `landing.css`
  dengan variabel `:root` Indonesia (`--hijau-utama #198754`, `--lebar-konten`,
  `--radius-*`, `--ukuran-*`, `--bayangan-*`, `--transisi-cepat`).
- **Admin** (`public/themes/adminlte/`): seluruh area kerja role.
  Dashboard role memakai bahasa `dash-*` yang sama: kartu putih radius 12px,
  border `#E4ECEB`, teks `#155E57`, aksen `#087F6C`, muted `#607D8B`.
- **Nota cetak**: partial `transaksi/partials/_nota.php`
  (`.nota-screen` vs `.nota-print`); modal tidak ikut tercetak.

## Register
**product** — design melayani fungsi: keterbacaan data, alur kerja cepat
(kasir, apoteker), status yang jelas (badge per status), cetakan rapi.
Bukan marketing/landing (kecuali halaman publik itu sendiri).

## Batasan desain
- Ringan: tanpa dependensi baru; pakai AdminLTE/Bootstrap/Chart.js yang ada.
- Copy Indonesia, kalimat pendek, tanpa jargon sistem.
- Tanpa AI slop: tanpa gradien ungu/pink, tanpa hero-metric template,
  tanpa eyebrow bernomor di tiap section.
- Kontras teks isi minimal 4.5:1; hormati `prefers-reduced-motion`.

## Next steps
- `DESIGN.md` (via `document`) untuk menangkap sistem visual yang ada.
- Permukaan prioritas bila redesign: dashboard pasien, halaman retur,
  nota mutasi stok.
