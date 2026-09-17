# Database Apotek

Nama database: `apotek` (PostgreSQL).

## Instalasi database KOSONG (teman yang belum punya database)

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

## Update database LAMA yang sudah berisi data (PENTING untuk teman)

Jika database `apotek` sudah ada dan berisi data, **JANGAN** import
`apotek_latest.sql` (akan error karena tabel sudah ada). Jalankan script
update incremental:

```bash
psql -U postgres -d apotek -v ON_ERROR_STOP=1 -f database/update_dari_versi_lama.sql
```

Sifat script ini:

- **Idempoten** — aman dijalankan berulang; sudah diuji 2× berturut-turut
  tanpa error (termasuk untuk update apotek online / migrasi 004:
  tabel `pesanan_online`(+detail), CHECK `ONLINE`, 5 permission baru).
- **Hanya menambah yang belum ada**: 7 tabel pengadaan
  (`supplier`, `pengadaan_obat`+detail, `penerimaan_obat`+detail,
  `retur_pengadaan`+detail), kolom `dokter.id_user` dan `pasien.id_user`,
  27 permission baru + grants per role. Data dan tabel lama **tidak
  dihapus dan tidak diubah**.
- Grants memakai **nama role** (`ADMIN_PELAYANAN`/`DOKTER`/`APOTEKER`).
  Bila nama role di database teman berbeda, sesuaikan Bagian 4 script.

Setelah update, wajib petakan akun ke datanya (contoh):

```sql
UPDATE dokter SET id_user = <id user dokter> WHERE id_dokter = <...>;
UPDATE pasien SET id_user = <id user pasien> WHERE id_pasien = <...>;
```

Tanpa pemetaan ini, dokter/pasien yang login tidak melihat datanya
(filter "milik sendiri" tidak tahu id-nya).

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
- Kolom `pasien.id_user → users(id_user)` — relasi user login ke data
  pasien untuk portal pasien (migrasi `003_pasien_user.php`).
- Permission `{Modul}.{Context}.View` dan `Site.{Context}.View` — pengatur
  tampilnya menu sidebar per role. Tanpa baris ini sidebar akan kosong.
- Aplikasi memakai populasi status HURUF BESAR
  (`AKTIF`, `TERDAFTAR`, `MENUNGGU`, `DIPROSES`, `SELESAI`, `DIBUAT`, …)
  yang dijaga CHECK constraint — jangan insert status lowercase.
