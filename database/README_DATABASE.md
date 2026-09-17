# Database Apotek

Nama database: `apotek` (PostgreSQL).

## Instalasi

1. Buat database `apotek`:
   ```sql
   CREATE DATABASE apotek;
   ```
2. Import struktur + seed:
   ```bash
   psql -U postgres -d apotek -f database/apotek_latest.sql
   ```
3. Sesuaikan koneksi di `application/config/database.php`
   (`hostname`, `username`, `password`, `port`) dengan PostgreSQL masing-masing.
   Nilai bawaan di repository (`postgres`/`postgres`) hanya untuk development lokal.
4. Buka aplikasi di browser (contoh: `http://localhost/klinikapotek/public/`)
   dan login dengan salah satu akun testing di bawah.

## Akun testing (password: `admin123`)

| Username   | Role            |
| ---------- | --------------- |
| admin      | ADMIN_PELAYANAN |
| pelayanan  | ADMIN_PELAYANAN |
| dokter     | DOKTER          |
| apoteker   | APOTEKER        |
| pasien     | PASIEN          |

Password tersimpan sebagai hash phpass bawaan aplikasi — jangan ganti
dengan plaintext.

## Isi `apotek_latest.sql`

- **Bagian A — struktur**: 35 tabel + PK, FK, index, CHECK status,
  default value, dan identity sequence.
- **Bagian B — seed**: roles, permissions (+36 permission View sidebar
  per-role), role_permissions, users, user_roles, dokter
  (terpetakan ke user `dokter`), spesialis, pelayanan, poli, ruangan,
  obat, stok_obat, supplier. Semua data dummy — **tanpa data pasien,
  transaksi, maupun audit log**.
- **Bagian C — sinkron sequence** agar insert berikutnya tidak clash PK.

## Catatan perubahan penting dari versi awal

- Tabel pengadaan: `supplier`, `pengadaan_obat` (+detail),
  `penerimaan_obat` (+detail), `retur_pengadaan` (+detail)
  (migrasi `application/db/migrations/001_pengadaan.php`).
- Kolom `dokter.id_user → users(id_user)` — relasi user login ke data
  dokter untuk filter antrian/pemeriksaan/resep per dokter
  (migrasi `application/db/migrations/002_dokter_user.php`).
- Permission `*.Content.View` dan `Site.*.View` — pengatur tampilnya
  menu sidebar per role. Tanpa baris ini sidebar akan kosong.
- Aplikasi memakai populasi status HURUF BESAR
  (`AKTIF`, `TERDAFTAR`, `MENUNGGU`, `DIPROSES`, `SELESAI`, `DIBUAT`, …)
  yang dijaga CHECK constraint — jangan insert status lowercase.
