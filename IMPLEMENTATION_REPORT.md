# IMPLEMENTATION REPORT

Tanggal: 18 September 2026. Baseline: `AUDIT_FULL_SYSTEM.md`. Implementasi dibatasi pada SAFE TO FIX yang diperintahkan. Tidak ada migration, perubahan schema, constraint, maupun data database.

## 1. Klasifikasi baseline

### A. SAFE TO FIX

| ID | Severity | Status | Catatan |
|---|---|---|---|
| BUG-01 | CRITICAL | IMPLEMENTED | Authorization backend controller legacy kunjungan. |
| BUG-02 | CRITICAL | IMPLEMENTED | Pasien resep diverifikasi dan ditentukan dari pemeriksaan/kunjungan. |
| BUG-03 | HIGH | IMPLEMENTED | Endpoint status tidak dapat lagi mengeset `DISERAHKAN`. |
| BUG-05 | HIGH | IMPLEMENTED | Penerimaan divalidasi terhadap detail dan sisa PO. |
| BUG-07 | MEDIUM | IMPLEMENTED | Dokumentasi diselaraskan; aturan pembayaran tidak berubah. |
| SEC-01 | LOW | IMPLEMENTED | Password DB dibaca dari `DB_PASSWORD`, bukan source. |
| DB-01 | HIGH | IMPLEMENTED (application layer) | Tidak menambah constraint lintas tabel. |
| DB-02 | HIGH | IMPLEMENTED (application layer) | Tidak menambah schema/constraint. |
| INT-01 | HIGH | IMPLEMENTED | Penyerahan resep tetap eksklusif di alur penjualan/stok. |
| INT-03 | MEDIUM | IMPLEMENTED | Riwayat read-only pemeriksaan/resep/transaksi pada detail pasien. |
| INT-04 | MEDIUM | IMPLEMENTED | Form legacy memakai service yang sama dan membuat antrean seperti API. |

### B. NEEDS BUSINESS DECISION

| ID | Severity | Keputusan yang diperlukan |
|---|---|---|
| BUG-08 | MEDIUM | Definisi pendapatan: apakah laporan mencakup penjualan langsung, dan apakah basisnya penjualan atau pembayaran. |
| RBAC-01 | HIGH | Matriks permission final untuk pelayanan, dokter, apotek, dan admin. |
| RBAC-02 | HIGH | Definisi/peran Admin Sistem terpisah dari Admin Pelayanan. |
| INT-02 | HIGH | Aturan apakah pesanan online boleh disiapkan/diselesaikan sebelum lunas. |
| INT-05 | HIGH | Model kasir, sumber transaksi, dan hubungan penjualan langsung dengan tagihan/pembayaran. |
| BIZ-01 | HIGH | Kebijakan COD/transfer/tempo dan titik wajib lunas pada pesanan online. |
| BIZ-02 | HIGH | Persetujuan matriks RBAC. |
| BIZ-03 | HIGH | Pelunasan penuh saja atau cicilan. |
| BIZ-04 | HIGH | Pemisahan kasir pelayanan dan apotek. |
| BIZ-05 | MEDIUM | Lifecycle retur supplier. |
| BIZ-06 | MEDIUM | Penggunaan master diagnosis/tindakan vs teks bebas. |
| BIZ-07 | MEDIUM | Aturan dokter lintas poli/ruangan dan jadwalnya. |

### C. NEEDS DATABASE/DESIGN REVIEW

| ID | Severity | Review yang diperlukan |
|---|---|---|
| BUG-06 | HIGH | Cek data nomor antrian existing, dampak unique/locking, strategi rollback; validasi aplikasi saja belum cukup terhadap race paralel. |
| DB-01 | HIGH | Constraint lintas resep–pemeriksaan–kunjungan tidak sederhana; validasi service sudah diterapkan. |
| DB-02 | HIGH | Validasi aplikasi sudah diterapkan; perubahan schema hanya bila audit concurrent receipt menunjukkan masih diperlukan. |
| DB-03 | MEDIUM | CHECK status retur/timestamp memerlukan pemeriksaan data existing dan migration terpisah. |
| DB-04 | MEDIUM | Normalisasi `tagihan_detail.id_referensi` dan jenis jasa dokter memerlukan desain laporan/keuangan. |

### D. BLOCKED / NEEDS RUNTIME TEST

| ID | Severity | Alasan |
|---|---|---|
| BUG-01 | CRITICAL | HTTP test dengan akun tanpa permission belum dapat dilakukan pada database test. |
| BUG-02 / DB-01 | CRITICAL/HIGH | Perlu request API terhadap pemeriksaan dan dua pasien pada database test. |
| BUG-03 / INT-01 | HIGH | Perlu uji API status dan uji penjualan+mutasi stok atomik. |
| BUG-05 / DB-02 | HIGH | Perlu PO dengan penerimaan sebagian/berulang pada database test. |
| BUG-06 | HIGH | Perlu uji request paralel. |
| INT-03 / INT-04 | MEDIUM | Perlu render halaman/detail dan submit kunjungan pada database test. |
| BUG-07 / SEC-01 | MEDIUM/LOW | Perlu boot aplikasi dengan environment `DB_PASSWORD` terpasang. |

## 2. Temuan yang diperbaiki

| ID | Status | File | Perubahan |
|---|---|---|---|
| BUG-01 | IMPLEMENTED | `application/modules/kunjungan/controllers/Content.php` | Mengaktifkan `restrict('kelola_pendaftaran')` pada controller yang dipakai alias legacy. |
| BUG-02, DB-01 | IMPLEMENTED | `application/modules/resep/models/Resep_model.php` | Join pemeriksaan ke kunjungan, tolak pasien request yang tidak cocok, gunakan pasien server-side. |
| BUG-03, INT-01 | IMPLEMENTED | `application/modules/resep/controllers/Api.php` | Menghapus `DISERAHKAN` dari endpoint status; Penjualan_model tetap menjadi jalur yang mengubah stok dan resep. |
| BUG-05, DB-02 | IMPLEMENTED | `application/modules/pengadaan/models/Pengadaan_model.php` | Memeriksa detail PO, total penerimaan sebelumnya, agregat item request, dan sisa pesanan sebelum insert. |
| INT-03 | IMPLEMENTED | `application/modules/pasien/models/Pasien_model.php`, `application/modules/pasien/views/content/detail.php` | Menampilkan riwayat read-only pemeriksaan, resep, dan transaksi pelayanan. |
| INT-04 | IMPLEMENTED | `application/modules/kunjungan/controllers/Content.php`, `application/modules/kunjungan/views/content/create.php` | Form legacy kini memanggil `Kunjungan_model::daftar()` dengan pembuatan antrean. |
| BUG-07 | IMPLEMENTED | `application/modules/transaksi/models/Transaksi_model.php` | Komentar menjelaskan pembayaran kurang ditolak dan cicilan belum didukung. |
| SEC-01 | IMPLEMENTED | `application/config/database.php`, `database/README_DATABASE.md` | Menghapus password hardcode; dokumentasi memakai environment variable `DB_PASSWORD`. |

## 3. Temuan yang belum dikerjakan

| ID | Alasan | Keputusan/review dibutuhkan |
|---|---|---|
| BUG-06 | Race condition memerlukan desain locking/constraint dan uji paralel. | Database/design review. |
| BUG-08 | Basis pendapatan tidak boleh diasumsikan. | Keputusan bisnis laporan. |
| RBAC-01, RBAC-02 | Mengubah grant/role mengubah tugas operasional. | Matriks RBAC eksplisit. |
| DB-03, DB-04 | Perlu perubahan schema/desain data. | Review data, migration/rollback plan. |
| INT-02, INT-05 | Berhubungan dengan kebijakan pembayaran/kasir. | Keputusan bisnis. |
| BIZ-01 s/d BIZ-07 | Memang membutuhkan kebijakan pemilik sistem. | Keputusan bisnis masing-masing. |

## 4. File yang berubah

- `application/config/database.php`
- `application/modules/kunjungan/controllers/Content.php`
- `application/modules/kunjungan/views/content/create.php`
- `application/modules/resep/controllers/Api.php`
- `application/modules/resep/models/Resep_model.php`
- `application/modules/pengadaan/models/Pengadaan_model.php`
- `application/modules/pasien/models/Pasien_model.php`
- `application/modules/pasien/views/content/detail.php`
- `application/modules/transaksi/models/Transaksi_model.php`
- `database/README_DATABASE.md`

`IMPLEMENTATION_REPORT.md` adalah artefak laporan ini. Tidak ada file migration atau dump schema yang diubah.

## 5. Database

- Migration: tidak ada.
- Perubahan schema: tidak ada.
- Perubahan constraint: tidak ada.
- Perubahan data: tidak ada.
- Perubahan query aplikasi: validasi penerimaan PO dan query read-only riwayat pasien.

## 6. Testing

| Test | Hasil |
|---|---|
| Static check (`php -l` file terdampak) | PASS |
| `git diff --check` | PASS |
| Application test suite (`php tests/run.php -a`) | BLOCKED — bootstrap legacy gagal pada PHP 8.2 sebelum test berjalan (`Base_Controller.php:143`), disertai deprecation legacy. |
| Backend authorization | IMPLEMENTED, BLOCKED runtime |
| Resep-pasien | IMPLEMENTED, BLOCKED runtime |
| Resep-stok | IMPLEMENTED, BLOCKED runtime |
| Penerimaan PO | IMPLEMENTED, BLOCKED runtime |
| Riwayat pasien | IMPLEMENTED, BLOCKED runtime |
| Kunjungan-antrian | IMPLEMENTED, BLOCKED runtime |

## 7. Regression

Tidak ada regression runtime yang dapat dinyatakan VERIFIED tanpa database test. Pemeriksaan statis menunjukkan perubahan hanya menyentuh jalur pendaftaran legacy, validasi resep, endpoint status resep, penerimaan PO, read model pasien, dokumentasi pembayaran, dan konfigurasi credential. Modul pemeriksaan, stok, penjualan, pesanan online, tagihan, laporan, serta schema tidak diubah.
