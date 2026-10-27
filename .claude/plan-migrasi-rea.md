# Plan Detail — REA & Migrasi simkop_ci3 ke Laravel 10 (PHP 8.3)

> Disusun menggunakan pendekatan REA (Reverse Engineer Anything):
> (1) reverse engineer peta sistem lama
> (2) rancang peta migrasi ke Laravel 10
> (3) implementasi bertahap per modul.

---

## 0. Ringkasan Temuan (Output REA)

### Project Sumber (CI3) — simkopukm - Backup 23-10-2026
- Framework: CodeIgniter 3 (struktur application/, system/)
- Jumlah controllers: 7 (Admin, Anggota, Home, Login, Pinjaman, Simpanan, test)
- Total baris controller: ~7.200 (Simpanan 2.831, Pinjaman 2.052, Anggota 1.196, Admin 1.018)
- Jumlah models: 38 (anggota, pinjaman, simpanan, agunan, survey, master data)
- Jumlah views: ~380 file (admin views sangat besar, 100+ form & laporan)
- Routing: CI3 default routing (satu route = satu controller/metode), tanpa route mapping custom

### Module Bisnis yang Teridentifikasi
| No | Domain | CI3 (controller/model) | Keterangan |
|----|--------|------------------------|-----------|
| 1 | Auth & Pengguna | Login, model User, Role, Module | Login + RBAC (role-module) |
| 2 | Master Data Anggota | Anggota, model Member*, Family, Heiress*, Employee, Images | CRUD anggota, KTA, validasi, PD, pembekuan |
| 3 | Lokasi & Rujukan | model City/District/Village, Business, Insurance, Fee | Data master |
| 4 | Master Konstanta | model Constants, SimpananProduct, LoanProduct | Konfigurasi produk & suku bunga |
| 5 | Simpanan | Simpanan, model Saving, TransDailySavings, TransNTRSavings, TransSSBSavings, TransSetiaPlus* | 5 jenis: Harian, NTR, SSB, SetiaPlus, ALB |
| 6 | Pinjaman | Pinjaman, model Loan, LoanAccount, agunan (Collateral*), TransInstallment, TransRealization | UKM/Mikro/Jaminan, survey, approval multi-level |
| 7 | Audit & Log | model Audit, method auditTrail di Admin | Jejak perubahan |
| 8 | Laporan & Cetak | ~200 views cetak_* | PDF (Fpdf), Excel (Spreadsheet), bilyet |

### Project Target (Laravel 10) — simkopukm
- Laravel 10 + PHP 8.3 (terkonfirmasi dari composer.json)
- Struktur app/Http/Controllers sudah ter-pecah per domain: Admin/, Auth/, Loan/, Member/, Saving/
- Models Eloquent sudah ada: ~44 model (mirip CI3 + tambahan LoanInterest, SavingsInterest, MemberStatus, TypeCity)
- Packages: barryvdh/laravel-dompdf (PDF), phpoffice/phpspreadsheet (Excel)
- Routing: sudah sangat lengkap, Batch 1-14 tersusun rapi di routes/web.php
- Status: Fase 1-7 sebagian besar sudah dikerjakan

---

## 1. Status Migrasi saat ini (Checklist)

### Sudah ada di target Laravel
| Fase | Modul | Status |
|------|-------|--------|
| 1 | Auth (Login, Logout) | OK |
| 2 | Admin: dashboard, user, role, module, employee, fee, location | OK |
| 3 | Audit trail & constants (simhar/ssb/pinjaman + product) | OK |
| 4 | Member CRUD + simpanan harian (master, transaksi, tutup, NTR, SSB, SetiaPlus, approval, auto closing, bayar jasa, BKM/BKK/BU, PDF, Excel) | OK |
| 5 | Pinjaman: master, pengajuan, approval, cetak, tagihan, angsuran kolektif, rescheduling, laporan agunan/realisasi/pelunasan, PDF & Excel | OK |

### Belum / Paruh di target (perlu diverifikasi per metode)
| Area | Potensi gap |
|------|-------------|
| Anggota (CI3 Anggota.php, 60 metode) | KTA cetak, validasi ALB/NTR, PD (Pindah Domisili), pembekuan, upload berkas, laporan jumlah anggota |
| Pinjaman (detail) | Pengajuan multiguna (MG: Emas, Sertifikat, AO), survey, pelunasan jatem, rescheduling detail, log pinjaman AO/PJ, agunan, pembayaran kolektif detail |
| Simpanan (detail) | Laporan saldo/mutasi lengkap, bilyet, buku simhar, cover, validasi trans, laporan admin simhar |
| Admin (detail) | Manajemen keamanan (log konstanta), koreksi user/role/module |
| Laporan PDF (dompdf) | ~200 view cetak di CI3, perlu mapping 1-per-1 |
| Excel Export | beberapa laporan |

---

## 2. Peta Migrasi CI3 ke Laravel 10 (Mapping)

### 2.1 Mapping Controller
| CI3 | Laravel Target |
|-----|----------------|
| Login.php | Auth\LoginController |
| Admin.php (management dasar) | Admin\AdminController |
| Admin.php (user/role/module/employee/fee/location/constants/audit) | Admin\UserController, RoleController, ModuleController, EmployeeController, FeeController, LocationController, ConstantsController, AuditController |
| Anggota.php | Admin\MemberController (CRUD) + Controller baru: Member\KtaController, Member\PdController, Member\PembekuanController, Member\UploadController |
| Simpanan.php | Saving\SavingController (+ Saving\ReportController) |
| Pinjaman.php | Loan\LoanController (+ Loan\SurveyController, Loan\CollateralController, Loan\PelunasanController) |
| Home.php | Redirect ke dashboard (sudah ada) |

### 2.2 Mapping Model CI3 ke Eloquent
Semua 38 model CI3 sudah punya counterpart Eloquent di app/Models (~44 model).
Yang perlu dicek: relasi hasMany/belongs, cast tipe, dan fillable agar cocok dengan logika lama.

### 2.3 Mapping View
- CI3: application/views/admin/*.php (~380 file, campur form + laporan + cetak)
- Laravel: resources/views/admin/* dipisah per modul:
  - admin/member/, admin/savings/, admin/loan/, admin/reports/
  - Cetak PDF pakai Blade + dompdf
  - Komponen Blade @include untuk layout berulang (nav, sidebar)

### 2.4 Mapping Dataflow
- CI3 Session -> Laravel Session (auth & state)
- CI3 URL::to / redirect -> Laravel route() / redirect()->route()
- CI3 session userdata role -> Laravel auth()->user()->role
- CI3 manual query -> Eloquent / Query Builder (hindari SQL injection)
- CI3 date() -> Carbon

---

## 3. Rencana Implementasi Bertahap (Baby Steps)

> Prinsip (sesuai rules.md #8): satu langkah = satu fitur = testing dulu sebelum lanjut.

### Fase A - Penyelesaian Modul Anggota (prioritas tinggi)
- A1: Cetak KTA (PDF), verifikasi logika cetak_KTA & ktaCetakPDF CI3
- A2: Validasi anggota ALB & NTR
- A3: Proses PD (Pindah Domisili) & Pembekuan
- A4: Upload berkas anggota (berkas scan)
- A5: Laporan jumlah anggota (rekap/rinci) + Excel export
- Setiap sub-fase: implement -> testing -> commit

### Fase B - Detail Modul Pinjaman
- B1: Pengajuan Multiguna (MG: Emas / Sertifikat / AO)
- B2: Survey pinjaman (jadwalSurvey, pengelolaanSurvey, pengajuanSurvey)
- B3: Pelunasan: normal, jatem, reschedule
- B4: Agunan (emas, sertifikat, simhar, SSB), validasi & release
- B5: Log pinjaman AO & PJ (logPinjamanAO/PJ, cekLogPinjaman)
- B6: Cetak SPH (Lama & Besar) & laporan rescheduling

### Fase C - Detail Modul Simpanan
- C1: Laporan saldo & mutasi lengkap (NTR/ALB/SSB/SimHar)
- C2: Cetak bilyet (SetiaPlus, SSB) & buku/cover simhar
- C3: Laporan admin simhar & aktivitas akun
- C4: Validasi transaksi & status buku
- C5: Laporan jatuh tempo SSB & pembayaran jasa

### Fase D - Admin / Utility
- D1: Koreksi user/role/module (rollback perubahan)
- D2: Log konstanta (audit jejak perubahan constant simhar/ssb/pinjaman)
- D3: Manajemen keamanan (akses IP / session)

### Fase E - Regresi & Finalisasi
- E1: End-to-end test per modul
- E2: Cross-check angka (saldo, angsuran) vs CI3
- E3: Bersih-besih kode (hapus comment, konsistensi, format Pint)
- E4: Dokumentasi final + update README

---

## 4. Checklist Pertimbangan Dampak

### Efek Positif
- Peta sistem lengkap, tingkat "lewat logika" turun drastis
- Gap bisa dilihat jelas per modul, tidak nyangkut di tengah migrasi
- Kode target sudah terstruktur (per-domain), mudah di-maintain
- PHP 8.3 + Laravel 10, performa & security lebih baik dari CI3

### Efek Negatif / Risiko
- Risiko 1: Logika bisnis tersembunyi di view CI3, beberapa aturan (hitung saldo, bunga, jatem) di-compute di controller besar, perlu extra careful saat porting
- Risiko 2: Data eksisting, jika database CI3 sama dengan target, perlu migrasi data / seeder (belum ada di plan saat ini)
- Risiko 3: View cetak (200+) sangat banyak, mapping 1-per-1 memakan waktu, perlu prioritas (yang sering dipakai dulu)
- Risiko 4: Regression test belum ada, tanpa test otomatis, tiap perubahan berisiko mengubah fitur yang sudah jalan

### Mitigasi
- Buat feature list per batch dengan DoD (Definition of Done)
- Prioritaskan fitur P0 (saling bergantung, dipakai harian) dulu
- Setelah tiap fase, jalankan smoke test manual
- Buat test suite dasar (PHPUnit feature test) untuk modul inti (auth, member, saving, loan)

---

## 5. Langkah Selanjutnya (Action Items)
1. Konfirmasi prioritas: mana yang mau dikerjakan lebih dulu, A (Anggota), B (Pinjaman), atau C (Simpanan)?
2. Jika sudah yakin, mulai dari A1 (Cetak KTA) atau yang paling urgent
3. Saat tiap batch selesai, jalankan command /daily & /resume sesuai aturan

---

*Catatan: file ini bisa di-update seiring kemajuan implementasi. Setiap fase yang selesai = ditandai di checklist.*
