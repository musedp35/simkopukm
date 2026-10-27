# Panduan UAT - SIMKOPUKM

Base URL: http://127.0.0.1:8000 (jalankan php artisan serve)

## Prasyarat
Run seeder:
  "C:\laragonin\php\php-8.3.36-nts-Win32-vs16-x86\php.exe" artisan db:seed --class=TestDatabaseSeeder
Run test suite (harus 11 passed):
  "C:\laragonin\php\php-8.3.36-nts-Win32-vs16-x86\php.exe" artisan test
Jalankan server:
  "C:\laragonin\php\php-8.3.36-nts-Win32-vs16-x86\php.exe" artisan serve

Semua akun password: admin123
| Username | Role |
| admin | A (full access) |
| budi | M (Manager) |
| siti | P (Penanggung Jawab) |
| ao3 / ao4 / ao6 | AO3 / AO4 / AO6 |
| kasie | KASIE |

Data seeded: 186 member, 50 loan, 10 savings, 420 installment, 100 image

## S1 - Login & Role-based Access
| # | Langkah | Expected | Status |
|---|---------|----------|--------|
| 1.1 | Login admin/admin123 | Dashboard, menu lengkap | [ ] |
| 1.2 | Login ao3/admin123 | Dashboard, menu role AO3 | [ ] |
| 1.3 | Login kasie/admin123 | Dashboard, menu role KASIE | [ ] |
| 1.4 | Buka /admin/loan/ukm tanpa login | Redirect ke /login | [ ] |
| 1.5 | Login password salah | Error, tidak masuk | [ ] |

## S2 - Modul Member
| # | Langkah | URL / Aksi | Expected | Status |
|---|---------|-----------|----------|--------|
| 2.1 | Daftar member | /admin/member | ~186 member, pagination | [ ] |
| 2.2 | Search Budi | search box | Filter tampil | [ ] |
| 2.3 | Detail MEM0002 | klik row | Data lengkap | [ ] |
| 2.4 | Tambah | /admin/member/create submit | Flash success | [ ] |
| 2.5 | Edit & simpan | form edit | Perubahan tersimpan | [ ] |
| 2.6 | Nonaktifkan | aksi hapus | Status jadi 0 | [ ] |

## S3 - Modul Simpanan
| # | Langkah | URL / Aksi | Expected | Status |
|---|---------|-----------|----------|--------|
| 3.1 | Master SimHar | /admin/savings/harian | List member SimHar | [ ] |
| 3.2 | Daftar Akun | /admin/savings/list-akun | 10 akun seeded | [ ] |
| 3.3 | Cek Saldo SimHar | /admin/savings/cek-saldo/simhar input MEM0002 | Tampil saldo | [ ] |
| 3.4 | Cek Saldo Tabungan | /admin/savings/cek-saldo/tabungan input SAV0001 | Tampil saldo | [ ] |
| 3.5 | Transaksi | /admin/savings/harian/transaksi | Form transaksi | [ ] |
| 3.6 | Persetujuan | /admin/savings/persetujuan | List pending | [ ] |
| 3.7 | Cetak BKM preview | /admin/savings/cetak-bkm | Preview PDF | [ ] |
| 3.8 | BKM download | /admin/savings/cetak-bkm/pdf | File PDF valid | [ ] |
| 3.9 | BKK download | /admin/savings/cetak-bkk/pdf | File PDF valid | [ ] |
| 3.10 | BU download | /admin/savings/cetak-bu/pdf | File PDF valid | [ ] |
| 3.11 | Auto Closing | /admin/savings/auto-closing | Hitung jasa & update saldo | [ ] |

## S4 - Modul Pinjaman
| # | Langkah | URL / Aksi | Expected | Status |
|---|---------|-----------|----------|--------|
| 4.1 | Dashboard | /admin/loan/pj-pinjaman | Statistik + breakdown status | [ ] |
| 4.2 | Master UKM | /admin/loan/ukm | List loan, filter berfungsi | [ ] |
| 4.3 | Data All Status | /admin/loan/data-pinjaman-all-status | 50 loan, pagination | [ ] |
| 4.4 | Tagihan Jatem | /admin/loan/tagihan | Item jatem tampil | [ ] |
| 4.5 | Laporan Tunggakan | /admin/loan/laporan-tunggakan | Item tunggakan tampil | [ ] |
| 4.6 | Laporan Pelunasan | /admin/loan/laporan-pelunasan | Loan status 6 tampil | [ ] |
| 4.7 | Laporan Agunan | /admin/loan/laporan-agunan | Data + filter jenis | [ ] |
| 4.8 | Laporan SPH | /admin/loan/laporan-sph | List + tombol detail | [ ] |
| 4.9 | SPH Kecil | /admin/loan/sph/kecil/CR20260038 | View A5 landscape | [ ] |
| 4.10 | SPH Besar | /admin/loan/sph/besar/CR20260038 | View A4 | [ ] |
| 4.11 | MG Serti Detail | /admin/loan/mg-serti/CR20260001 | Data akun + riwayat angsuran | [ ] |
| 4.12 | MG Simhar Detail | /admin/loan/mg-simhar/CR20260001 | Data akun + riwayat | [ ] |
| 4.13 | Survey | /admin/loan/survey/CR20260001 | Form + foto ma_images | [ ] |
| 4.14 | Agunan | /admin/loan/agunan/CR20260001 | Form upload foto agunan | [ ] |
| 4.15 | Pelunasan | /admin/loan/pelunasan/CR20260001 | Sisa + form konfirmasi | [ ] |
| 4.16 | Log AO/PJ | /admin/loan/log-ao-pj/CR20260001 | Tabel riwayat (empty ok) | [ ] |
| 4.17 | Approval AO3 | /admin/loan/approval-multi-level?level=ao3 | List menunggu AO3 | [ ] |
| 4.18 | Approval AO4 | /admin/loan/approval-multi-level?level=ao4 | List menunggu AO4 | [ ] |
| 4.19 | Approval AO6 | /admin/loan/approval-multi-level?level=ao6 | List menunggu AO6 | [ ] |
| 4.20 | Approval KASIE | /admin/loan/approval-multi-level?level=kasie | List menunggu KASIE | [ ] |
| 4.21 | Cetak BKM | /admin/loan/cetak-bkm | Preview PDF | [ ] |
| 4.22 | BKM PDF | /admin/loan/cetak-bkm/pdf | File PDF valid | [ ] |
| 4.23 | BKK PDF | /admin/loan/cetak-bkk/pdf | File PDF valid | [ ] |
| 4.24 | BU PDF | /admin/loan/cetak-bu/pdf | File PDF valid | [ ] |

## S5 - MG Multiguna & Pengajuan
| # | Langkah | URL / Aksi | Expected | Status |
|---|---------|-----------|----------|--------|
| 5.1 | Form MG Emas | /admin/loan/pengajuan-mg-emas | Form + tabel jasa | [ ] |
| 5.2 | Form MG Simhar | /admin/loan/pengajuan-mg-simhar | Form + tabel jasa | [ ] |
| 5.3 | Form MG Potong Gaji | /admin/loan/pengajuan-mg-potong | Form + tabel jasa | [ ] |
| 5.4 | Submit MG | Isi form lalu submit | Loan baru di DB, sukses | [ ] |
| 5.5 | Pengajuan PP | /admin/loan/pengajuan-pp | Form tampil | [ ] |
| 5.6 | Pengajuan Jatem | /admin/loan/pengajuan-jatem | Form tampil | [ ] |

## S6 - Verifikasi DB
Query SQL di MySQL/terminal terpisah:
SELECT LOAN_STATUS_ID, COUNT(*) FROM mu_loan_account GROUP BY LOAN_STATUS_ID ORDER BY LOAN_STATUS_ID;
SELECT IMAGE_TYPE, COUNT(*) FROM ma_images GROUP BY IMAGE_TYPE;
SELECT LOAN_ACCOUNT_NO, PRINCIPAL_AMOUNT, REMAINING_AMOUNT FROM mu_loan_account WHERE LOAN_STATUS_ID=0 LIMIT 5;
SELECT LOAN_ACCOUNT_NO, COUNT(*) FROM mu_trans_installment WHERE LOAN_STATUS_ID=0 GROUP BY LOAN_ACCOUNT_NO LIMIT 5;

| # | Verifikasi | Expected | Status |
|---|-----------|----------|--------|
| 6.1 | Distribusi loan status | 0:10, 1:10, 2:10, 6:10, 7:5, 8:5 | [ ] |
| 6.2 | Distribusi images | SURVEY:50, AGUNAN:50 | [ ] |
| 6.3 | Loan aktif remaining < principal | Semua loan status 0 dan 1 | [ ] |
| 6.4 | Loan pelunas remaining = 0 | Semua loan status 6 | [ ] |

## S7 - Negative Test
| # | Langkah | Expected | Status |
|---|---------|----------|--------|
| 7.1 | /admin/loan/mg-serti/XXXX9999 | Error page, bukan HTTP 500 | [ ] |
| 7.2 | POST form kosong ke proses MG | Validasi error, form kembali | [ ] |
| 7.3 | /admin/loan/tagihan?dtpTgl=1990-01-01&dtpTglAkhir=1990-01-02 | Empty table, tidak crash | [ ] |
| 7.4 | Buka URL admin di tab tanpa sesi login | Redirect ke /login | [ ] |
| 7.5 | Login role AO3 lalu akses /admin/member/create | Hak akses ditolak | [ ] |

## Kriteria LULUS UAT
| # | Kriteria | Status |
|---|----------|--------|
| 1 | Semua S1-S5: tidak ada HTTP 500 | [ ] |
| 2 | Semua PDF bisa dibuka valid di browser | [ ] |
| 3 | Angka laporan sesuai query SQL DB | [ ] |
| 4 | Approval flow AO3-AO4-AO6-KASIE berfungsi urut | [ ] |
| 5 | Role-based: user AO tidak lihat menu admin | [ ] |
| 6 | Pelunasan: remaining=0, status=6 terverifikasi di DB | [ ] |
| 7 | Negative test: input invalid ditangani, tidak crash | [ ] |

## Tips Penguji
- Buka DevTools - Network tab untuk cek status HTTP tiap request
- Jika ada error 500: cek storage/logs/laravel.log
- Loan CR20260001-CR20260050 bisa di-test detail
- Loan status 6 (lunas, bisa SPH): cek di Laporan Pelunasan
