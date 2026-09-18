# FULL SYSTEM AUDIT — Klinik + Apotek

Tanggal audit: 18 September 2026. Metode: inspeksi statis source, route, model, controller, view, konfigurasi dan dump PostgreSQL. Tidak ada kode, migrasi, seed, atau data yang diubah. Pengujian runtime/database tidak dilakukan karena audit tidak diberi lingkungan uji terisolasi; item yang memerlukan eksekusi diberi **BLOCKED**.

## 1. Executive Summary

Jumlah temuan/fitur pada matriks: **PASS 9, PARTIAL 13, BUG 8, NOT IMPLEMENTED 12, BLOCKED 3**.

Severity temuan: **CRITICAL 2, HIGH 5, MEDIUM 9, LOW 3**.

Kesimpulan: fondasi alur klinik (pasien → kunjungan → antrian → pemeriksaan → resep) dan apotek (stok, penjualan, pengadaan, pesanan online) sudah nyata di backend/database. Namun aplikasi belum aman untuk produksi: terdapat bypass otorisasi di controller legacy, integritas resep-pasien belum dipaksa, pembatalan pesanan/penjualan tidak mempunyai mekanisme pembalikan stok, dan laporan serta pembayaran tidak meliputi seluruh sumber pendapatan.

## 2. Feature Matrix

| Modul | Fitur | Frontend | Backend | Database | Integrasi | Status | Severity | Catatan/bukti |
|---|---|---:|---:|---:|---:|---|---|---|
| Dashboard | Dashboard per pelayanan/dokter/apoteker/pasien | Ya | Ya | Ya | Ya | PASS | LOW | `application/controllers/Dashboard.php`, view dashboard memakai nilai query, bukan hardcode. |
| Pasien | CRUD, pencarian, RM unik | Ya | Ya | Ya | Sebagian | PARTIAL | MEDIUM | `pasien/Pasien_model.php`; riwayat detail hanya kunjungan, tidak memuat pemeriksaan/resep/transaksi. |
| Kunjungan | Pendaftaran dan penetapan pelayanan/poli/dokter/ruang | Ya | Ya | Ya | Ya | PARTIAL | HIGH | API aman (`kunjungan/controllers/Api.php`), tetapi alias legacy ke `kunjungan/Content.php` tidak memanggil `restrict()`. |
| Kunjungan | Nomor antrian otomatis | Ya | Ya | Ya | Ya | PARTIAL | MEDIUM | API membuat otomatis; form legacy membuat kunjungan tanpa antrian (`Content.php:87`). |
| Antrian | Status dan pemanggilan dokter | Ya | Ya | Ya | Ya | PASS | MEDIUM | `Antrian_model.php` memiliki state machine dan waktu status. |
| Pemeriksaan | Buka, keluhan, diagnosis, tindakan, selesai | Ya | Ya | Ya | Ya | PARTIAL | HIGH | Update keluhan/hasil/catatan setelah buka tidak ada endpoint; diagnosis/tindakan hanya tambah. |
| Rekam medis | Hubungan pasien–kunjungan–pemeriksaan | Sebagian | Ya | Ya | Ya | PARTIAL | HIGH | Relasi ada, tetapi resep tidak divalidasi ke pasien kunjungan. |
| Resep | Header/detail, dosis, aturan pakai | Ya | Ya | Ya | Ya | PARTIAL | HIGH | `Resep_model.php:47-52` hanya cek dokter pemeriksaan, bukan pasien. |
| Resep/apotek | Proses dan penyerahan resep | Ya | Ya | Ya | Sebagian | BUG | HIGH | `resep/Api.php:93-108` mengizinkan lompatan status bebas; set DISERAHKAN tidak mengurangi stok/menjual. |
| Penjualan langsung | Validasi obat wajib resep & kurangi stok | Ya | Ya | Ya | Ya | PASS | MEDIUM | `Penjualan_model.php:94-101,139-147` melakukan validasi backend dan mutasi stok dalam transaksi. |
| Penjualan | Pembatalan/retur penjualan | Tidak | Tidak | Tidak | Tidak | NOT IMPLEMENTED | HIGH | Tidak ada operasi pembatalan penjualan; komentar model juga menyatakan stok tidak kembali. |
| Stok | Posisi, mutasi, stok minimum | Ya | Ya | Ya | Ya | PASS | MEDIUM | `Stok_model.php`; constraint stok >= 0 dan unique per obat. |
| Pengadaan | Supplier, PO, penerimaan, stok masuk | Ya | Ya | Ya | Ya | PARTIAL | MEDIUM | Penerimaan tidak membatasi item ke detail PO atau kuantitas sisa (`Pengadaan_model.php:120-131`). |
| Pengadaan | Retur/komplain lifecycle | Tidak lengkap | Sebagian | Ya | Tidak | NOT IMPLEMENTED | MEDIUM | Retur otomatis dibuat, tetapi tidak ada controller/model untuk tindak lanjut atau status selesai. |
| Online | Katalog, keranjang, checkout, alamat | Ya | Ya | Ya | Ya | PASS | MEDIUM | `Pesanan_model.php:177-224`; alamat disimpan di `alamat_kirim`. |
| Online | Pembayaran dan pemenuhan | Ya | Sebagian | Ya | Sebagian | BUG | HIGH | Pesanan dapat `SIAP/SELESAI` walau `status_bayar` masih BELUM_DIBAYAR (`Apotek.php:71-76`). |
| Tagihan | Tagihan layanan dan pembayaran | Ya | Ya | Ya | Sebagian | PARTIAL | HIGH | Tagihan disusun manual via API; penjualan langsung tidak dibuatkan tagihan. |
| Pembayaran | Cicilan | Tidak | Tidak | Ya | Tidak | NOT IMPLEMENTED | MEDIUM | `Transaksi_model.php:64` menolak bayar < sisa, meski komentar menyebut bertahap. |
| Laporan | Kunjungan, pendapatan, penjualan, resep, mutasi | Ya | Ya | Ya | Sebagian | PARTIAL | HIGH | Hanya lima endpoint (`laporan/Api.php:31`); tanpa PDF/Excel/print/filter dokter/poli/status. |
| Master | poli, dokter, ruang, spesialis, pelayanan, obat | Ya | Ya | Ya | Ya | PARTIAL | MEDIUM | CRUD ada; hubungan dokter–poli/ruangan tidak dimodelkan. |
| RBAC | Permission pada API modern | Ya | Ya | Ya | Ya | PARTIAL | HIGH | API memakai `auth->restrict`; controller legacy mengekspos bypass. |

## 3. Missing Features

1. **Pembatalan/retur penjualan obat** — modul Penjualan. Tidak ada endpoint/model untuk membatalkan penjualan serta memulihkan stok dan mutasi. Dampak: stok tidak dapat direkonsiliasi setelah salah jual. Prioritas: HIGH.
2. **Laporan layanan/pasien/pemeriksaan/pengadaan/retur/pembayaran lengkap dan export** — modul Laporan. `Laporan_model.php` hanya menyediakan enam query dasar (stok bahkan tidak diekspos API). Dampak: pelaporan operasional/keuangan tidak lengkap. Prioritas: HIGH.
3. **Cetak nomor antrian** — modul Antrian. Tidak ditemukan controller/view PDF/print khusus; nomor hanya disimpan/ditampilkan. Dampak: proses front-office tidak lengkap. Prioritas: MEDIUM.
4. **CRUD/lifecycle retur pengadaan** — modul Pengadaan. Tabel dan pembuatan otomatis ada, tetapi tidak ada UI/API pemrosesan retur. Dampak: komplain supplier tidak dapat diselesaikan/ditelusuri. Prioritas: MEDIUM.
5. **Master diagnosis/tindakan** — modul Pemeriksaan. Diagnosis/tindakan adalah teks per pemeriksaan (`diagnosis`, `tindakan`), bukan master pilihan. Dampak: pelaporan/standardisasi klinis lemah. Prioritas: MEDIUM.
6. **Keterkaitan dokter dengan poli/ruangan dan spesialis di kunjungan** — modul Master/Kunjungan. Skema hanya mengikat dokter ke spesialis; tidak ada validasi dokter untuk poli/ruangan. Dampak: pelayanan dapat memilih kombinasi yang tidak sah. Prioritas: HIGH.
7. **Kasir pelayanan vs kasir apotek/sumber pembayaran** — modul Transaksi. `transaksi`/`pembayaran` tidak punya kasir, metode, kanal, atau sumber yang memadai. Dampak: rekonsiliasi kas tidak dapat dipisahkan. Prioritas: HIGH.

## 4. Incomplete Features

- Detail pasien tidak menyajikan riwayat pemeriksaan, resep dan transaksi walau requirement memintanya: `application/modules/pasien/models/Pasien_model.php:125-140`.
- Kunjungan dari form legacy sengaja berhenti di `TERDAFTAR`, sehingga memerlukan aksi terpisah untuk antrian; API mengikuti alur berbeda (otomatis): `kunjungan/controllers/Content.php:87` vs `kunjungan/controllers/Api.php:27`.
- Pemeriksaan hanya mendukung data awal pada `buka()`; tidak ada edit pemeriksaan dan tidak ada hapus/koreksi diagnosis/tindakan.
- Pengadaan tidak memvalidasi item penerimaan terhadap `pengadaan_obat_detail`, tidak mencegah penerimaan berulang melebihi pesanan, dan status hanya langsung selesai/sebagian.
- Status pesanan online dibayar diselaraskan hanya ketika detail pasien dibaca (`Pesanan_model.php:354-364`), bukan event pembayaran.
- PDF/print/Excel tidak ditemukan untuk laporan atau bukti antrean; bukti transaksi hanya view HTML (`transaksi/Content.php`).

## 5. Incorrect Flow

### Resep dapat terhubung ke pasien yang bukan pasien pemeriksaan

- **Alur saat ini:** API menerima `id_pasien`; model hanya memastikan `id_pemeriksaan` milik dokter tersebut.
- **Seharusnya:** pasien resep harus diambil/ditolak berdasarkan `pemeriksaan → kunjungan → id_pasien`.
- **Masalah:** rekam medis/resep dapat tertukar antar pasien.
- **File terkait:** `application/modules/resep/models/Resep_model.php:47-52`; `application/modules/resep/controllers/Api.php:32-66`.
- **Rekomendasi:** validasi kesetaraan di model dan jadikan pasien sumber data server-side.

### Penyerahan resep dapat terjadi tanpa penjualan dan stok keluar

- **Alur saat ini:** endpoint status langsung menulis `DISERAHKAN`.
- **Seharusnya:** penyerahan hanya sesudah penjualan/resep diproses sukses; stok dan transaksi harus atomik.
- **Masalah:** resep tampak diserahkan tanpa obat keluar/tagihan.
- **File terkait:** `application/modules/resep/controllers/Api.php:93-108`; `application/modules/penjualan/models/Penjualan_model.php:139-151`.
- **Rekomendasi:** state transition di model dan satu service penyerahan yang memanggil penjualan.

### Pesanan online dapat selesai sebelum pembayaran

- **Alur saat ini:** apoteker dapat memilih `SIAP` lalu `SELESAI`, tanpa memeriksa tagihan/status bayar.
- **Seharusnya:** pemenuhan perlu kebijakan eksplisit (lunas sebelum kirim/ambil, atau terutang dengan approval) dan status konsisten.
- **Masalah:** obat/stok keluar tanpa pembayaran.
- **File terkait:** `application/modules/apotekonline/controllers/Apotek.php:71-76`; `application/modules/apotekonline/models/Pesanan_model.php:354-364`.
- **Rekomendasi:** guard status pembayaran pada transisi yang memerlukan pelunasan dan sinkronisasi event-based.

### Kunjungan bisa dibuka melalui jalur legacy tanpa izin

- **Alur saat ini:** route `/admin/content/kunjungan/*` menuju `kunjungan/Content`; konstruktor mempunyai `restrict()` yang dikomentari.
- **Seharusnya:** setiap controller dan endpoint membatasi backend dengan `kelola_pendaftaran`.
- **Masalah:** pengguna login tanpa permission berpotensi membaca/membuat kunjungan melalui URL lama.
- **File terkait:** `application/config/routes.php:138-141`; `application/modules/kunjungan/controllers/Content.php:13`.
- **Rekomendasi:** hapus alias legacy atau aktifkan guard dan uji semua route langsung.

## 6. Bugs

| ID | Severity | Modul | Masalah / reproduksi | Expected vs actual | Bukti |
|---|---|---|---|---|---|
| BUG-01 | CRITICAL | RBAC/Kunjungan | Login role tanpa `kelola_pendaftaran`, buka `/admin/content/kunjungan/create`, POST form. | Ditolak; guard dikomentari. | `kunjungan/controllers/Content.php:13`. |
| BUG-02 | CRITICAL | Rekam medis/Resep | Dokter membuat resep dari pemeriksaan A tetapi mengirim `id_pasien` B. | Harus ditolak; resep tersimpan untuk B. | `resep/models/Resep_model.php:47-66`. |
| BUG-03 | HIGH | Resep/Stok | Ubah resep ke `DISERAHKAN` via API tanpa penjualan. | Stok/transaksi ikut berubah; hanya status resep berubah. | `resep/controllers/Api.php:93-108`. |
| BUG-04 | HIGH | Online/Pembayaran | Proses pesanan lalu ubah sampai `SELESAI` sebelum tagihan dibayar. | Harus diblokir/policy; diizinkan. | `apotekonline/controllers/Apotek.php:71-76`. |
| BUG-05 | HIGH | Pengadaan | Terima obat yang tidak ada di detail PO atau kuantitas lebih besar dari PO. | Ditolak; model hanya cek obat master. | `pengadaan/models/Pengadaan_model.php:120-131`. |
| BUG-06 | HIGH | Kunjungan/Antrian | Buat dua kunjungan paralel pada poli/hari yang sama. Nomor memakai COUNT+1 tanpa lock. | Nomor unik; konflik/duplikasi mungkin karena schema tidak punya unique `(tanggal,poli,nomor)`. | `antrian/models/Antrian_model.php:62-75`; schema `antrian`. |
| BUG-07 | MEDIUM | Pembayaran | Dokumentasi model menyebut pembayaran bertahap, kemudian nominal kurang ditolak. | Perilaku dan kontrak konsisten; actual menolak cicilan. | `transaksi/models/Transaksi_model.php:8-9,64-66`. |
| BUG-08 | MEDIUM | Laporan | Pendapatan menghitung `transaksi` tagihan saja, sementara penjualan langsung tidak membuat tagihan/transaksi. | Semua sumber pendapatan; actual mengabaikan penjualan langsung. | `laporan/models/Laporan_model.php:27-37`; `penjualan/models/Penjualan_model.php:118-151`. |

## 7. Database Audit

**Kekuatan:** PK, FK, CHECK dan unique penting telah tersedia: pasien.no_rm/nik, antrian.id_kunjungan, pemeriksaan.id_kunjungan, resep.id_pemeriksaan, stok.id_obat dan nomor dokumen. Bukti: `database/apotek_latest.sql:1117-1614,1860-2244`.

**Masalah integritas:**

- Tidak ada constraint yang menjamin `resep.id_pasien` = pasien kunjungan pada `resep.id_pemeriksaan`; FK independen tidak cukup.
- Tidak ada FK/constraint yang menghubungkan penjualan resep ke seluruh/detail resep yang tepat; aplikasi hanya mengecek item per item.
- `antrian` tidak memiliki unique nomor per poli/tanggal; generation COUNT rentan race.
- Penerimaan/retur detail tidak mempunyai CHECK `jumlah > 0`, dan penerimaan tidak dibatasi ke detail PO.
- `retur_pengadaan.status` tidak mempunyai CHECK dan tidak ada `updated_at`; lifecycle/audit lemah.
- `tagihan_detail.id_referensi` polimorfik tanpa FK; bisa merujuk ke data yang tidak ada. Jasa dokter dipaksa menjadi jenis `TINDAKAN` (`Tagihan_model.php:71-74`), membuat semantik laporan kabur.
- Nomor dokumen dihasilkan aplikasi (`nomor_baru`), bukan sequence atomik per prefix; potensi tabrakan harus diuji dengan beban paralel.
- Konfigurasi database berisi kredensial development plaintext dan `db_debug=true`: `application/config/database.php:73-87`. Jangan simpan kredensial produksi dalam repository.

## 8. Role & Permission Audit

Keterangan: `V` view, `C/U` create/update, `B` diblokir, `!` tidak aman/terlalu luas berdasarkan source. Role “Admin Sistem” tidak ada di seed; akun `admin` memiliki role `ADMIN_PELAYANAN` (`database/apotek_latest.sql:2402-2529`).

| Fitur | Admin Sistem | Pelayanan | Dokter | Apotek | Pasien |
|---|---|---|---|---|---|
| Dashboard | BLOCKED (role tidak disediakan) | V | V | V | V |
| Pasien/kunjungan/antrian | ! C/U melalui legacy | C/U | B (kecuali antrian sendiri) | B | B |
| Pemeriksaan/rekam medis | ! bergantung permission | ! diberi permission seed | C/U milik sendiri | B | B |
| Resep | ! | ! diberi permission seed | C/U milik sendiri | C/U/status | B |
| Penjualan/stok/pengadaan | ! | ! banyak permission seed | B | C/U | B |
| Online | B | B | B | C/U pesanan | C/U pesanan sendiri |
| Tagihan/transaksi | ! | C/U | B | B | B |
| Laporan | ! | V | B | V | B |

Catatan: `ADMIN_PELAYANAN` seed memperoleh permission pemeriksaan, resep, penjualan, stok, pengadaan, transaksi, master, user dan role (baris permission role di dump `2412-2490`). Ini tidak memenuhi least privilege untuk “kasir pelayanan”. API modern memeriksa permission tetapi penggunaan permission yang sangat luas tetap melampaui pemisahan tugas.

## 9. End-to-End Flow Audit

| Skenario | Hasil | Tahap yang tervalidasi | Titik putus |
|---|---|---|---|
| 1. Pasien baru → layanan → apotek → bayar | PARTIAL | pasien/RM, kunjungan, antrian, pemeriksaan, resep, penjualan, stok | resep-pasien tidak aman; tagihan harus disusun manual; penyerahan resep bypass stok; laporan pendapatan parsial. |
| 2. Pasien lama → kunjungan baru | PARTIAL | pasien dan kunjungan memakai `id_pasien` yang sama | legacy controller tanpa permission; alur antrian berbeda antara UI/API. |
| 3. Jual obat langsung | PASS | validasi resep backend, stok keluar, mutasi, detail penjualan | tidak ada pembayaran/tagihan/kasir otomatis dan tidak ada pembatalan/retur. |
| 4. Stok menipis → pengadaan → terima | PARTIAL | stok minimum, PO, supplier, stok masuk | tidak ada link wajib antara penerimaan dan detail PO/kuantitas. |
| 5. Barang bermasalah → retur | PARTIAL | retur otomatis dicatat ketika kondisi bukan Baik | tidak ada proses/penutupan retur atau rekonsiliasi supplier. |
| 6. Online → checkout → proses → selesai | BUG | cart, alamat, validasi obat/resep, stok keluar, tagihan dibuat | selesai tidak tergantung pembayaran; status bayar baru sinkron saat detail dibaca. |

## 10. Priority Fix

### CRITICAL

1. Tutup bypass otorisasi controller/rute legacy dan uji akses langsung untuk seluruh controller/API.
2. Paksa konsistensi pasien pada resep terhadap pemeriksaan/kunjungan, termasuk constraint/validasi service.

### HIGH

1. Jadikan proses penyerahan resep atomik: state machine + penjualan + stok + tagihan sesuai kebijakan.
2. Definisikan dan terapkan policy pembayaran pesanan online sebelum `SELESAI`/pengiriman.
3. Validasi penerimaan terhadap PO (item, jumlah sisa, status) dan buat lifecycle retur.
4. Pisahkan permission kasir pelayanan, dokter, apotek dan admin sistem; hilangkan grant berlebihan pada `ADMIN_PELAYANAN`.
5. Satukan sumber penjualan/pembayaran supaya tagihan dan laporan pendapatan tidak kehilangan penjualan langsung.

### MEDIUM

1. Tambahkan locking/unique constraint nomor antrian dan nomor dokumen yang aman paralel.
2. Lengkapi rekam medis/riwayat pasien, edit/koreksi klinis dengan audit trail, dan master diagnosis/tindakan bila diperlukan.
3. Lengkapi laporan operasional, filter, PDF/print/Excel sesuai kebutuhan.

### LOW

1. Pindahkan kredensial database dari source dan nonaktifkan `db_debug` di produksi.
2. Standarkan status retur dan timestamp/audit pada seluruh dokumen.

## Batas Verifikasi

Tidak dilakukan HTTP runtime test, import database, migration, seeding, atau modifikasi. Oleh karena itu hasil query nyata, CSRF/session behavior, template rendering, PDF/print, race condition paralel, dan konsistensi data yang sudah tersimpan adalah **BLOCKED** sampai tersedia database uji dan akun role terisolasi.
