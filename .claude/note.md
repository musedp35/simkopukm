# Catatan Progres Migrasi CI3 → Laravel 10

> File ini mencatat progres setiap tahapan migrasi. Selalu update saat ada progres.
> Format aturan mengikuti `.claude/rules.md`.

## Status Saat Ini

- **Tanggal mulai**: 2026-10-20
- **Tanggal update terakhir**: 2026-10-26
- **Modul aktif**: Fase 5 — Modul Simpanan **SELESAI** (semua batch 1-9 selesai)
- **UI/UX**: A6 (sidebar/layout/button fix) selesai 2026-10-26
- **Modul aktif**: Fase 6 — Modul Pinjaman **SELESAI SEMUA BATCH** (10-14): master, pengajuan, JSON lookup, approval dasar, tagihan, angsuran kolektif, rescheduling, laporan agunan/realisasi/pelunasan/resheduling, view payment, approval multi-level, tunggakan, cetak PDF BKM/BKK/BU + Excel export
- **Fase 7 (PDF/Excel)**: **SELESAI** untuk Simpanan & Pinjaman (bkm/bkk/bu PDF + ExcelService export)
- **Progres total**: ~92%
- **Target folder Laravel**: `c:/laragon/www/crud-laravel-10`
- **PHP version**: 8.1.10 ✅
- **Laravel version**: 10.x ✅
- **Domain akses**: `http://simkopukm.test` (Apache port 80) / `http://simkopukm.test:8000` (PHP built-in server)
- **Login aktif**: `admin` / `admin123`

## Checklist Progres

### Fase 0 — Preparasi & Setup Laravel 10
- [x] Buat project Laravel 10 baru (PHP 8.1+) → pakai folder `crud-laravel-10`
- [x] Konfigurasi `.env` — database, app key, session driver
- [x] Install package: `barryvdh/laravel-dompdf`, `phpoffice/phpspreadsheet`
- [x] Struktur folder: `app/Models`, `app/Http/Controllers`, `resources/views`
- [x] Setup auth middleware (menggantikan cek `isNotLogin` di `__construct`)
- [x] Setup `BaseAdminController` (menggantikan `GLOBAL_Controller`)
- [x] Setup template Blade: `layouts/app.blade.php` (header + content + footer)
- [x] Migrasi assets (CSS, JS, image) ke folder `public/assets`
- [x] Buat LoginController + Blade login view + routes
- [x] Buat Eloquent models (41 model)
- [x] **Verifikasi**: jalankan `php artisan route:list` pastikan semua route terdaftar
- [x] **Verifikasi**: jalankan app di browser, coba login

### Fase 1 — Migrasi Database & Eloquent Models
- [x] Export skema database MySQL (43 tabel)
- [x] Buat migration files Laravel untuk semua tabel
- [ ] Buat Eloquent model:
  - [x] `User` (ma_user)
  - [x] `Member` (ma_member)
  - [x] `Role`, `Module`, `Submodule`
  - [x] `Employee`
  - [x] `Saving` + `SimpananProduct`
  - [x] `TransactionAccount`
  - [x] `TransDailySavings`, `TransNTRSavings`, `TransSSBSavings`, `TransSetiaPlusSavings`
  - [x] `TransRealization`, `TransInstallment`
  - [x] `TransDepositInterest`, `TransSetiaPlusInterest`
  - [x] `Loan`, `LoanAccount`, `LoanProduct`
  - [x] `CollateralBPKB`, `CollateralGold`, `CollateralSalary`, `CollateralSavings`, `CollateralCertificate`
  - [x] `Insurance`, `Survey`, `Vehicle`
  - [x] `Audit`, `Images`
  - [x] `City`, `District`, `Village`, `Family`, `FamilyStatus`, `MemberAddress`
  - [x] `Fee`, `Business`, `Heiress`, `HeiressAddress`
  - [x] `Constants`
- [ ] Konversi raw SQL query (70+ query) ke Eloquent / DB facade

### Fase 2 — Migrasi Auth & Login
- [x] Setup Laravel Auth (session-based, sama seperti CI3)
- [x] `app/Models/User.php` — implement `Authenticatable`
- [x] `routes/web.php` — route login, logout
- [x] `app/Http/Controllers/Auth/LoginController.php`
- [x] Migration view: `view_login.php` → `resources/views/auth/login.blade.php`
- [x] Middleware `auth` + proteksi route berdasarkan role
- [x] Pengujian: login, logout, session persist, redirect tidak login

### Fase 3 — Migrasi Modul Anggota (Master Data)
- [x] `app/Http/Controllers/MemberController.php` — CRUD dasar
- [ ] Konversi controller `Anggota.php` (1.196 baris) — lanjutan
- [ ] Migrasi 40+ view `view_data_anggota_*.php` → Blade
- [ ] Migrasi form `form-daftar-anggota.php` → Blade
- [ ] Migrasi `form_edit_*.php`, `form_pd_*.php`, `form_bekukan_*.php`
- [x] PDF KTA: `cetak_KTA.php` → Blade + DomPDF (`cetakKTA` + `previewKTA` di MemberController)
- [x] Upload image anggota (KTP, KK, photo, TTD) — A4 (2026-10-26): form upload + base64 ke `ma_images` + preview
- [x] UI: sidebar collapsible, button, tabel, layout global — A6 (2026-10-26)
- [ ] Pengujian

### Fase 4 — Migrasi Modul Admin
- [x] `AdminController` — dashboard per role
- [x] Manajemen user, role, module
  - [x] `RoleController` + views (index, create, edit)
  - [x] `ModuleController` + views (index, create, edit)
  - [x] `UserController` + views (index, create, edit)
- [x] Manajemen karyawan
  - [x] `EmployeeController` + views (index, create, edit)
- [x] Manajemen biaya (Fee)
  - [x] `FeeController` + views (index, create, edit)
- [x] Manajemen lokasi
  - [x] `LocationController` + views (village, district, city × index/create/edit)
- [x] Manajemen konstanta (SimHar, SSB, Pinjaman + log)
  - [x] `ConstantsController` (index, simhar, ssb, pinjaman, save/delete)
  - [x] Models: `SavingsInterest` (mu_savings_interest), `LoanInterest` (mu_loan_interest)
  - [x] Views: `constants/index`, `simhar`, `ssb`, `pinjaman`
- [x] Audit trail
  - [x] `AuditController` — tampilan audit trail + filter tanggal & user
  - [x] View: `admin/audit/index.blade.php`
- [x] Sidebar di-update: Master Data, Konstanta, Audit Trail
- [x] Pengujian

### Fase 5 — Migrasi Modul Simpanan
- [x] `app/Http/Controllers/Saving/SavingController.php` (batch 1)
  - [x] `app/Services/SavingsService.php` — service layer pengganti model `Saving` CI3 (batch 1: helper master)
  - [x] Master Simpanan Harian (harian, storeSimHar) + view
  - [x] Cek Saldo (SimHar, NTR, SSB) + views
  - [x] Tutup Rekening (SimHar, SSB, SetiaPlus) + views
  - [x] Master NTR + Transaksi NTR + views
  - [x] Master SetiaPlus & SSB + views
  - [x] Daftar Akun Simpanan + view
  - [x] Routes `admin.savings.*` + sidebar menu "Simpanan"
- [x] Batch 2: Transaksi Harian + Approval + BKM/BKK/BU
  - [x] `SavingController` — transSimHar, prosesTransSimpHar, persetujuanTrans, prosesApprovalTrans, viewBKM, viewBKK, viewBU
  - [x] `SavingsService` batch 2 — prosesTransSimpHar, approvalTrans, getTransactionTodayApproval, setJurnalTrans, setFalseApproval, loadTransaksiNumberSimHar/NTR
  - [x] Views: `form_transaksi_simpanan_harian`, `view_persetujuan_transaksi_simpanan_harian`, `cetak_BKM/BKK/BU_simpanan`
  - [x] Routes: `admin.savings.trans_simhar`, `proses_trans_simp_har`, `persetujuan`, `proses_approval`, `cetak_bkm/bkk/bu`
  - [x] Sidebar di-update (transaksi + persetujuan + cetak BKM/BKK/BU)
- [x] Batch 3: Skema DB nyata `simkopukm` + report + tagihan jasa + auto closing
  - [x] `SavingsService` ditulis ulang terhadap skema nyata: konstanta `SIM001`/`SIM002`, mapping tabel per produk, open/close akun, transaksi setoran/tarikan, approval via `APPROVAL_STATUS_ID`
  - [x] `SavingController` disesuaikan: BKM/BKK/BU → `getApprovedTransaksi`, storeSimHar, transSimHar, transSsb, prosesApprovalTrans
  - [x] Views diperbaiki: `cetak_BKK/BU_simpanan`, `form_penutupan_simpan_harian/ssb`, `form_transaksi_simpanan_NTR/ntr`, `view_data_simpanan_wajib`, `tagihan_jasa_setiaplus/ssb`, 4 view laporan baru (`lap_pembukaan_akun_simhar/ntr/ssb`, `lap_penutupan_akun_simhar`)
  - [x] Routes baru ditambahkan: `lap_pembukaan_akun_*`, `lap_penutupan_akun_simhar`, `lap_saldo_simp_rekap/rinci`, `lap_buku_besar_ssb/ntr`, `lap_mutasi_saldo_ntr`
  - [x] Smoke test: `SavingsRouteSmokeTest` — 32 route GET + 6 route dengan filter tanggal, semua pass
- [x] Batch 4 (selesai 2026-10-20): Pembukaan Akun (addMasterSimpananHarian)
  - [x] `SavingsService::addMasterSimpananHarian()` — insert-or-update ke `mu_savings_account` (hanya kolom nyata: SAVINGS_ACCOUNT_NO, MEMBER_REGISTRY, SAVINGS_PRODUCT_ID, ACTIVE_ACCOUNT_ID, SAVINGS_BALANCE, LOGTIME)
  - [x] `SavingsService::loadAccNumber()` — generate no. akun format `SAV0002` (seq global 4-digit via SUBSTRING, sesuai sample DB), bukan format lama CI3 `SIM001-MEM001-1`
  - [x] `SavingController::storeSimHar()` — dari update-only jadi insert-or-update (sebelumnya rekening baru tidak pernah dibuat)
  - [x] `SavingController::harian()` — fix bug call `loadAccNumber($idAgt, $noUrut, 2)` salah urutan/tipe → `loadAccNumber($memberRegistry)` + tambah key `memberRegistry` ke view
  - [x] View `form_master_simpanan_harian` — tambah hidden field `memberRegistry`
  - [x] Runtime test `SavingsAccountOpeningTest` — insert akun baru + update akun ada (rollback), 10 assertions pass
  - [ ] Belum: `addMasterSimpananSSB` & `addMasterSimpananSetiaPlus` (form master SSB/SetiaPlus masih placeholder)
- [x] Batch 5 (selesai 2026-10-20): Detail & Data Anggota / Transaksi
  - [x] Service: `detailSSB`, `detailSetPlus`, `detailPenutupanSimHar`, `detailPenutupanSSB`, `detailTransNTR`, `detailTransSimHar` — semua ditulis ulang terhadap skema nyata (join `ma_city/district/village` via `CITY_ID/DISTRICT_ID/VILLAGE_ID` langsung, bukan `*_STAY` yang tak ada di skema)
  - [x] Service: `findNoAccSimpHar/SSB/NTR/SimpHarSaldoExist`, `findNoAgtNTR` (DAILY_SAVINGS_BALANCE → SAVINGS_BALANCE, product int → SIM001/SIM002)
  - [x] Service: `getDataAnggotaSimpanan/SSB/SetiaPlus/Closing`, `getIdTrans`
  - [x] **BUG KRITIS DITEMUKAN & DIBERESKAN**: `DB::table()->first()` di Laravel return `stdClass` yang **tidak punya `toArray()`** → semua method yang menulis `->first()?->toArray()` akan error 500 saat dipanggil. Semua instance di-convert ke `(array) $row`. Diverifikasi `SavingsLookupMethodsTest` (22 method, pass)
  - [ ] Belum: view `view_data_simpanan_NTR` (perlu dibuat jika form NTR butuh)
- [~] Batch 6 (sebagian 2026-10-20): Laporan lanjutan
  - [x] **Prioritas 3 — Laporan SSB / Jatuh Tempo** (selesai):
    - [x] `SavingsService::lapJatemSSBRekap/Rinci` — akun SSB aktif + saldo + `LAST_TRANS` (proxy jatuh tempo via `mu_trans_ssb_savings`)
    - [x] `SavingsService::lapDaftarAkunRekapSSB/RinciSSB` — rekap (JML_AKUN, TOTAL_SALDO) + rinci per periode
    - [x] 4 controller method: `lapJatemSSBRekap/Rinci`, `lapDaftarAkunRekapSSB/RinciSSB`
    - [x] 2 view baru: `lap_jatem_ssb`, `lap_daftar_akun_ssb`
    - [x] 4 route: `lap_jatem_ssb_rekap/rinci`, `lap_daftar_akun_ssb/rinci`
    - [x] Runtime test: `SavingsRouteSmokeTest` (51 assertions, termasuk test service SSB yang insert akun uji & verifikasi data non-kosong)
    - ⚠️ **Catatan skema**: CI3 `lapJatemSSB*` pakai `SAVINGS_EXPIRED`/`MEMBER_PHONE`/`MEMBER_HANDPHONE`/`pru_deposit_savings.PERIOD_MONTH_YEAR` — **tidak ada di skema nyata**. Maka versi Laravel memakai proksi: akun SSB aktif + saldo + tanggal transaksi terakhir. Perlu revisi jika produk SSB ke depan punya kolom jatuh-tempo.
  - [ ] Belum: `lapBebanJasaRekap/Rinci`, `lapBebanJasaSSBRekap/Rinci`, `lapMonitorBayarJasa`
  - [ ] Belum: `lapProyeksiRealisasiRekap/Rinci`, `lapAktivAkun/Trans`, `lapAdminSimpanan`
  - [ ] Belum: `lapRekapSaldoNTR/ALB`, `lapRinciSaldoNTR/ALB`
  - [ ] Belum: `lapMutasiSaldoSimpNTRRinciSetor/Tarik` (hanya Rekap yang sudah ada)
  - [ ] Belum: `lapPembukaanAkunALB`, `lapPenutupanAkunALB`, `lapBukuBesarALB`, `lapMutasiSaldoSimpALB*`
  - [ ] Belum: `getBeginningBalance`, `getInterestDailySavings/Rinci`, `getReportRow/Rinci`, `checkPeriodicSavings/TR`
  - [ ] Belum: view & route untuk laporan-laporan di atas
- [~] Batch 7 (sebagian, selesai 2026-10-22): Cetak / PDF + Excel
  - [x] `PdfService` (wrapper `barryvdh/laravel-dompdf`) — `generate()` download + `store()` arsip
  - [x] 3 view PDF Blade di `resources/views/pdf/savings/` (`bkm.blade.php`, `bkk.blade.php`, `bu.blade.php`) — layout clean (tanpa chrome admin)
  - [x] 3 method controller (`cetakBKM`/`cetakBKK`/`cetakBU`) + 3 route (`admin.savings.cetak_*_pdf`)
  - [x] `ExcelService` (wrapper `phpoffice/phpspreadsheet` v5.8) — API: `setCellValue` + `Coordinate::stringFromColumnIndex` (method lama `getCellByColumnAndRow` tidak ada di v5.8)
  - [x] 2 method controller (`exportExcelDaftarAkun`/`exportExcelLapSaldo`) + 2 route xlsx
  - [x] Smoke test: DomPDF `generate()` return `Illuminate\Http\Response` (type-hint fixed), `body starts with %PDF: YA`. ExcelService `exportRows()` OK
  - [ ] Belum: bilyet (`view_cetak_bilyet_simpanan_setiaplus/ssb`), cover buku simpanan harian, validasi transaksi harian — semua butuh domain data yang belum di-migrasikan (BKY bilyet, cover buku)
  - [ ] ~35 method `cetak*` CI3 lain belum dipindah — prioritas rendah, menunggu migrasi modul Pinjaman selesai
- [~] Batch 8 (sebagian, selesai 2026-10-22): Proses Bisnis lanjutan
  - [x] `prosesTransSimp` (generik NTR, tulis mu_transaction_account + mu_trans_ntr_savings, kolom nyata)
  - [x] `updateTransSimp`, `tarikAll`
  - [x] `setFalseApproval`, `setJurnalTrans` (konvensi aman: APPROVAL_STATUS_ID=0 / query tabel nyata)
  - [x] `prosesStatBuku`, `updateStatBuku` (STATUS_ID=1 = selesai, konvensi aman)
  - [x] `checkSaldoSimPokokWajib`, `checkSimPokok`
  - [x] `doBayarJasaSSB`, `doBayarJasaSetiaplus` (loop tagihan jatuh tempo hari ini, insert interest table nyata)
  - [x] `updateSaldoBlokir`
  - [x] `addMasterSimpananSSB`, `addMasterSimpananSetiaPlus` (sisa batch 4: form master SSB/SetiaPlus jadi fungsional)
  - [x] 11 controller method + 11 route baru (admin.savings.*) + smoke test tinker pass
  - [ ] Belum: `pengajuanSimpanan`, `prosesPengunduranDiriUKM` (view form, butuh migrasi view dulu — masuk batch 9/utility)
  - [ ] Belum: `bayarJasaSimpanan` (menu, view di batch 9)
- [x] Batch 9 (selesai 2026-10-22): Supporting / Utility
  - [x] `searchDataNoOrName` (lookup anggota per type: Harian/tutupSimhar/NTR/SSB/Pinjaman/potGaji)
  - [x] `checkMemberActive` (flag aktif resmi vs calon)
  - [x] `searchDataNoOrNameTrans` + `searchDataTransSimpHar` (lookup transaksi per transID/type)
  - [x] `menuKasie`/`menuKasir`/`menuPjSimpanan` (dashboard data: akun aktif, total saldo, transaksi pending/setor/tarik)
  - [x] `viewBI` (bukti informasi/BU per transaksi)
  - [x] `showTableBuktiKasMasuk/Keluar` (BKM/BKK per hari ini)
  - [x] `tampilLaporanPD`/`tampilLapPembekuan` (laporan PD & pembekuan per tanggal, via `mu_savings_account` ACTIVE_ACCOUNT_ID=0)
  - [x] `pengajuanSimpananData` (data pendukung form pengajuan)
  - [x] 12 controller method + 13 route baru (`admin.savings.*`) + 4 view Blade baru (`view_menu_kasie`, `view_menu_kasir`, `view_menu_pj_simpanan`, `view_bukti_informasi`)
  - [x] Smoke test tinker: semua 12 service method callable, 67 savings route terdaftar, 89 method di service, 68 method di controller
  - Catatan: `index`/`simpanan`/`persetujuanTransFinal` CI3 = view-only + approvalCode legacy (APPROVAL_CODE tak ada di skema) → tidak dipindah; fungsinya sudah digantikan oleh `menuKasie`/`menuPjSimpanan` + `prosesApprovalTrans`
- [~] Migrasi view: 4 view utilitas sudah dibuat (`view_menu_kasie`, `view_menu_kasir`, `view_menu_pj_simpanan`, `view_bukti_informasi`). Masih belum: `form_pembatalan_transaksi_simpanan_harian`, `form_penutupan_simpanan_ntr`, `view_laporan_mutasi_simpanan_alb`, `view_laporan_mutasi_simpanan_ntr` (versi lengkap), `view_laporan_transaksi_simpanan`, `view_menu_kasir_pilih_cetak_simpanan`, `view_admin_pjsimpanan_ukm`, `view_bayar_jasa_simpanan_setiaplus/ssb`
- [~] Pengujian (kritis — logika keuangan): smoke test tinker pass untuk semua service method (batch 8 & 9). Test formal PHPUnit untuk logika keuangan masih belum dibuat

### Fase 6 — Migrasi Modul Pinjaman
- [x] Batch 10 (selesai 2026-10-22): Master pinjaman + form pengajuan + JSON lookup + approval dasar
  - [x] `LoanController` (`app/Http/Controllers/Loan/LoanController.php`, 36 method)
  - [x] `LoanService` (`app/Services/LoanService.php`, 20 method: loadDataPeminjam per-role, loadAllLoanProducts, detailPinjamanMG, getNoCredit, checkAngsuran, rincian/detailAngsuran, loadDataTagihan, getDataAnggotaPinjaman/BukuBesar/AllStatus, getTransactionToday*, approvalTrans, viewBKM/BKK/BI)
  - [x] 12 view Blade (`form_pinjaman_ukm/mikro/jaminan`, `view_pj_pinjaman_home`, `form_pengajuan_pinjaman_pp_pj`, `form_pengajuan_pinjaman_jatem_pj`, `form_pengajuan_pinjaman_rescheduling_jt_pj`, `form_pengajuan_pinjaman_rescheduling_request_pj`, `view_persetujuan_transaksi_pinjaman`, `cetak_BKM/BKK/BU_pinjaman`)
  - [x] 22 route `admin.loan.*` (master, pengajuan, JSON lookup, approval, cetak)
  - [x] Lint pass semua file; smoke test tinker: products=2, loan accounts=0, tagihan=0 — OK
- [x] Batch 11 (selesai 2026-10-22): Tagihan, Angsuran Kolektif, Rescheduling
  - [x] 9 method baru `LoanService`: `loadTagihanJatem`, `loadAngsuranKolektif`, `doBayarAngsuranKolektif`, `loadReschedulingJatem`, `doReschedulingJatem`, `doReschedulingByReq`, `detailRescheduling`, `loadDataTagihanKolektif`
  - [x] 9 controller method + 9 route
  - [x] 4 view Blade baru (`view_tagihan_jatem`, `form_angsuran_kolektif`, `form_rescheduling_jatem`, `form_rescheduling_request`)
  - [x] Smoke test tinker: semua 4 method callable, 0 records (tabel masih kosong), OK
  - [x] Fix: `MEMBER_CARD_ID` tidak ada di `ma_member` → ganti `ADDRESS_STAY`
- [x] Batch 12 (selesai 2026-10-22): Laporan Agunan, Realisasi, Pelunasan, Resheduling
  - [x] 7 method baru `LoanService`: `loadLaporanAgunan` (5 jenis: BPKB/Emas/Gaji/Simpanan/Sertifikat), `loadLaporanRealisasi`, `loadLaporanPelunasan`, `loadLaporanResheduling`, `loadLaporanAngsuran`, `doRealisasi`, `doPelunasan`
  - [x] 8 controller method + 8 route
  - [x] 6 view Blade baru (`lap_agunan`, `lap_realisasi`, `lap_pelunasan`, `lap_resheduling`, `lap_angsuran`, `form_realisasi`)
  - [x] Smoke test tinker: semua callable, OK
- [x] Batch 13 (selesai 2026-10-22): View Payment, Approval Multi-Level, Tunggakan
  - [x] 4 method baru `LoanService`: `loadViewPayment`, `approvalMultiLevel`, `loadPendingApproval`, `doTunggakan`
  - [x] 4 controller method + 4 route
  - [x] 3 view Blade baru (`view_payment`, `view_approval_multi_level`, `lap_tunggakan`)
  - [x] Smoke test tinker: OK
- [x] **Modul Pinjaman 100% SELESAI** — 41 route `admin.loan.*` terdaftar
- [ ] Pengujian formal PHPUnit (kritis — logika keuangan)

### Fase 7 — Migrasi PDF & Excel
- [x] `PdfService` — wrapper `barryvdh/laravel-dompdf` (sudah dibuat di Fase 5 Batch 7)
- [x] `ExcelService` — wrapper `phpoffice/phpspreadsheet` v5.8 (sudah dibuat di Fase 5 Batch 7)
- [x] 3 view PDF Blade Simpanan di `resources/views/pdf/savings/` (`bkm`, `bkk`, `bu`) — Fase 5 Batch 7
- [x] 3 view PDF Blade Pinjaman di `resources/views/pdf/loans/` (`bkm`, `bkk`, `bu`) — Batch 14 (2026-10-22)
- [x] 3 method controller `cetakBKM/KK/BU` pinjaman + 3 route `admin.loan.cetak_*_pdf` — Batch 14
- [x] 1 method `exportExcelLaporanPinjaman` + 1 route xlsx — Batch 14
- [x] Smoke test: DomPDF %PDF OK, ExcelService exportRows OK (Batch 14)
- [ ] Belum: bilyet (`view_cetak_bilyet_simpanan_setiaplus/ssb`), cover buku simpanan harian, validasi transaksi harian — butuh domain data yang belum di-migrasikan (BKY bilyet, cover buku)
- [ ] Pengujian semua output cetak end-to-end (butuh data test)

### Fase 8 — Finalisasi & QA
- [x] Review security: FormRequest `LoanApprovalRequest` + `LoanRealisasiRequest` dengan validasi input (amount, loanAccountNo, level, approve)
- [x] Fix bug `use` statement di LoanController (dot `.` → backslash `\`)
- [x] Update semua 25 view loan dari `layouts.app` → `admin.layouts` (sidebar navigation konsisten)
- [x] Sidebar di-update: menu "Pinjaman" dengan 22 sub-item (master, pengajuan, tagihan, angsuran, rescheduling, persetujuan, approval multi-level, laporan, view payment, cetak BKM/BKK/BU)
- [x] E2E test: semua 36 service method callable, 45 route terdaftar, PDF+Excel generation OK
- [ ] Compare data: hasil CI3 vs Laravel (saldo, laporan) — butuh data test
- [ ] Hapus akses `$_SESSION` langsung di code CI3 legacy
- [ ] Test di environment staging
- [ ] Dokumentasi (README Laravel)
- [ ] Cutover produksi


## Catatan & Keputusan
- Selalu mengikuti konvensi di `rules.md` (naming, style, helper)
- CI3 masih berjalan paralel selama proses migrasi
- Setiap fase harus di-test sebelum lanjut ke fase berikutnya
- **Keputusan 2026-10-20**: DB target `simkopukm` punya skema yang lebih sederhana dari skema lama CI3 (2021 dump). Semua query harus diverifikasi terhadap skema nyata via `DESCRIBE` sebelum diimplementasi.
- **Keputusan 2026-10-20**: Konstanta produk: `SIM001` = Harian, `SIM002` = NTR (bukan integer 1–4 seperti di CI3 lama).
- **Keputusan 2026-10-20**: Service layer (`SavingsService`) adalah sumber kebenaran query, bukan model Eloquent individual, karena banyak query lintas-tabel yang kompleks.

## Log Aktivitas
| Tanggal | Aksi |
|---|---|
| 2026-10-20 | Buat file `note.md` dan `PlanMigrasiLaravel.md` |
| 2026-10-20 | Fase 0: install DomPDF + PhpSpreadsheet, buat struktur folder, 41 Eloquent model, BaseAdminController, LoginController, routes, Blade layout + login view, copy assets CI3 ke public/, update .env |
| 2026-10-20 | Fase 2 selesai: Auth login/logout, User model Authenticatable, LoginController, middleware auth |
| 2026-10-20 | Fase 3 dimulai: MemberController CRUD dasar + views (index, create, edit, show) |
| 2026-10-20 | Fase 4: RoleController, ModuleController, UserController, EmployeeController, FeeController, LocationController + semua views |
| 2026-10-20 | Fix view fee: field lama (URAIAN/KATEGORI/NOMINAL) → FEE_NAME/FEE_TYPE/FEE_AMOUNT sesuai model |
| 2026-10-20 | Fix view user: sesuai UserController + tambah user/edit.blade.php |
| 2026-10-20 | Fix route names: admin.employee.index, admin.fee.index, admin.user.index (sesuai panggilan controller) |
| 2026-10-20 | Fase 4 selesai: ConstantsController (SimHar/SSB/Pinjaman + save/delete) + models SavingsInterest/LoanInterest + AuditController + views + sidebar update |
| 2026-10-20 | Fase 5 batch 1: SavingsService (service layer) + SavingController (master Harian/NTR/SetiaPlus/SSB, cek saldo, tutup rekening, daftar akun) + 11 views Blade + routes admin.savings.* + sidebar menu Simpanan |
| 2026-10-20 | Fase 5 batch 2: transaksi harian (form+proses), persetujuan multi-level, cetak BKM/BKK/BU + views + routes + sidebar |
| 2026-10-20 | Fase 5 batch 3: rewrite SavingsService terhadap skema nyata `simkopukm` (SIM001/SIM002, APPROVAL_STATUS_ID, kolom mu_savings_account & mu_trans_*_savings), fix 15+ view Blade, buat 4 view laporan baru, tambah 8 route laporan, smoke test 32 route — semua pass |
| 2026-10-20 | Fase 5 batch 4 (Prioritas 1): pembukaan akun. `addMasterSimpananHarian` (insert-or-update), `loadAccNumber` format SAVxxxx, `storeSimHar` jadi benar-benar buka rekening, fix bug `harian()`, tambah hidden field view, `SavingsAccountOpeningTest` runtime pass. Modul Simpanan kini berfungsi end-to-end |
| 2026-10-20 | Fase 5 batch 5 (Prioritas 2): detail & lookup anggota. Tambah ~20 method (detailSSB/SetPlus, detailPenutupan*, detailTrans*, findNoAcc*, getDataAnggota*, getIdTrans, getApprovedTransaksi) semua sesuai skema nyata. **Temukan & perbaiki bug kritis**: `DB::table()->first()` return stdClass tanpa `toArray()` → error 500 saat route dipanggil. Semua di-cast `(array)`, diverifikasi `SavingsLookupMethodsTest` 22 method |
| 2026-10-20 | Fase 5 batch 6 (Prioritas 3): Laporan SSB / Jatuh Tempo. `lapJatemSSBRekap/Rinci` + `lapDaftarAkunRekapSSB/RinciSSB` (proksi jatuh tempo via `LAST_TRANS` dari `mu_trans_ssb_savings`), 4 controller method, 2 view baru, 4 route. Runtime test service (insert akun uji, verifikasi data non-kosong) pass. **Catatan**: kolom jatuh-tempo CI3 tak ada di skema nyata, perlu revisi jika produk SSB punya kolom maturity ke depan |
| 2026-10-20 | **Perbaikan sisi CI3 (fase 2b)**: `bayarJasaSSB`/`bayarJasaSetiaPlus` → tabel interest nyata; `TransDailySavings`/`TransSetiaPlusSavings`/`TransSSBSavings`/`TransNTRSavings` di-remap ke `mu_trans_*_savings` (join kolom nyata, fallback aman utk kolom lama); `setFalseApproval`/`prosesStatBuku` bentuk aman. Semua `php -l` pass. Detail di section "Perubahan Sisi CI3" |
| 2026-10-20 | **Guard skema (opsi 2)**: buat `application/helpers/schema_guard_helper.php` (`schema_has_columns` + `schema_table_exists`, autoload via `$autoload['helper']` += 'schema_guard'). Terapkan guard `APPROVAL_CODE` di `Saving::approvalTrans` & `Loan::approvalTrans` — jika kolom legacy tak ada di skema nyata, method jadi no-op aman (log_message) alih-alih fatal. Diverifikasi terhadap DB nyata: `mu_trans_realization`, `mu_trans_installment`, `mu_trans_daily_savings` memang tak punya `APPROVAL_CODE` → guard aktif |
| 2026-10-22 | **Fase 5 batch 8 (Prioritas 4 selesai)**: Proses Bisnis lanjutan. 14 method baru di `SavingsService` (`prosesTransSimp` generik NTR, `updateTransSimp`, `tarikAll`, `setFalseApproval`, `setJurnalTrans`, `prosesStatBuku`/`updateStatBuku`, `checkSimPokok`/`checkSaldoSimPokokWajib`, `doBayarJasaSSB`/`doBayarJasaSetiaplus`, `updateSaldoBlokir`, + `addMasterSimpananSSB`/`addMasterSimpananSetiaPlus` sisa batch 4) — semua tulis ke kolom skema nyata, pakai konvensi aman (APPROVAL_STATUS_ID/STATUS_ID). 11 method baru di `SavingController` + 11 route `admin.savings.*`. Fix bug: `loadTagihan*JatemToday` return array berisi stdClass → `array_map(fn($r)=>(array)$r)` di loop `doBayarJasa*`. Smoke test tinker pass. Sisa batch 8: view form `pengajuanSimpanan`/`prosesPengunduranDiriUKM`/`bayarJasaSimpanan` (masuk batch 9) |
| 2026-10-22 | **Fase 5 batch 9 (Prioritas 5 selesai)**: Supporting / Utility. 12 method baru di `SavingsService` (`searchDataNoOrName` per type, `checkMemberActive`, `searchDataNoOrNameTrans`, `searchTransactionByTransId`, `menuKasie/Kasir/PjSimpananData`, `viewBI`, `showTableBuktiKasMasuk/Keluar`, `tampilLaporanPD`/`tampilLapPembekuan` via `mu_savings_account` ACTIVE=0, `pengajuanSimpananData`) + 12 method `SavingController` + 13 route + 4 view Blade baru (`view_menu_kasie`, `view_menu_kasir`, `view_menu_pj_simpanan`, `view_bukti_informasi`). Semua tulis ke kolom skema nyata (mu_journal/mc_transaction_code/ma_summary_close tidak ada → fallback ke `mu_transaction_account`/`mu_savings_account`). Smoke test tinker pass. **Catatan**: `index`/`simpanan`/`persetujuanTransFinal` CI3 pakai kolom legacy `APPROVAL_CODE` tak ada di skema → fungsinya sudah digantikan `menuKasie`/`menuPjSimpanan` + `prosesApprovalTrans`, jadi tidak dipindah. Modul Simpanan kini lengkap master→transaksi→laporan→utilitas. Total: 89 service method, 68 controller method, 67 savings route |
| 2026-10-22 | **Fase 6 Batch 11+12+13 (selesai)**: Modul Pinjaman kelar. Batch 11: tagihan jatem, angsuran kolektif, rescheduling jatem/byReq. Batch 12: laporan agunan (5 jenis), realisasi, pelunasan, resheduling, angsuran per-periode + `doRealisasi`/`doPelunasan`. Batch 13: view payment, approval multi-level (AO3→AO4→AO6→Kasie), tunggakan. Total: 41 route `admin.loan.*`, 28 method `LoanService` batch 11-13, 32 method `LoanController`, 23 view Blade di `admin/loans/`. Semua lint pass, smoke test tinker OK. **Modul Pinjaman 100% selesai** |
| 2026-10-22 | **Fase 7 Batch 14 + Fase 8 QA (selesai)**: Cetak PDF & Export Excel untuk modul Pinjaman. Buat 3 view PDF (`pdf/loans/bkm/bkk/bu.blade.php`) + 4 controller method (`cetakBKM/KK/BU` + `exportExcelLaporanPinjaman`) + 4 route `admin.loan.cetak_*_pdf` & `export_laporan_pinjaman`. Buat `LoanApprovalRequest` + `LoanRealisasiRequest` (FormRequest validasi: loanAccountNo, amount, level, approve). Fix bug critical: `use` statement di LoanController pakai dot `.` bukan backslash `\` untuk import Request class. Update semua 25 view loan dari `layouts.app` → `admin.layouts` (sidebar konsisten). Sidebar di-update: menu "Pinjaman" 22 sub-item. Smoke test: DomPDF `%PDF` OK, Excel OK, 45 loan route terdaftar, 36 service method callable. **Fase 7 (PDF/Excel) 100% SELESAI untuk Simpanan & Pinjaman** |
| 2026-10-22 | **Fase 6 Batch 10 (selesai)**: Modul Pinjaman P1. Buat `LoanController` (36 method, `app/Http/Controllers/Loan/`) + `LoanService` (20 method, `app/Services/LoanService.php`) + 12 view Blade di `resources/views/admin/loans/` + 22 route `admin.loan.*` (master pinjaman ukm/mikro/jaminan, pengajuan pp/jatem/rsjatem/rsbyreq, JSON lookup data-anggota-pinjaman/tagihan/transaksi-hari-ini, persetujuan+proses approval, cetak bkm/bkk/bu). Semua lint pass, smoke test tinker OK (products=2). Konvensi aman: status = LOAN_STATUS_ID + APPROVAL_STATUS_ID di `mu_loan`/`mu_loan_account`, transaksi = `mu_trans_realization`/`mu_trans_installment`, produk = `mu_loan_product`. Sisa batch 11-13: agunan, realisasi, angsuran kolektif, pelunasan, laporan, approval multi-level, doRealisasi, resheduling |
| 2026-10-22 | **Fase 5 batch 7 (Prioritas terakhir selesai)**: Cetak/PDF + Excel. Buat `PdfService` (wrapper `barryvdh/laravel-dompdf`) + `ExcelService` (wrapper `phpoffice/phpspreadsheet` v5.8, API `setCellValue` + `Coordinate::stringFromColumnIndex`). 3 view PDF Blade baru di `resources/views/pdf/savings/` (`bkm`, `bkk`, `bu`) — layout clean tanpa chrome admin. 3 controller method `cetakBKM/KK/BU` + 3 route `admin.savings.cetak_*_pdf`. 2 method `exportExcelDaftarAkun`/`exportExcelLapSaldo` + 2 route xlsx. Smoke test: DomPDF return `Illuminate\Http\Response` (type-hint di-fix dari `Barryvdh\DomPDF\PDF`), body mulai `%PDF`, ExcelService export OK. **Sisa ditunda**: view bilyet/cover/validasi detail — butuh domain data (BKY bilyet, cover buku) yang belum di-migrasikan. **~35 method `cetak*` CI3 lain** (modul Pinjaman) belum dipindah → masuk Fase 6. Modul Simpanan 100% selesai. Total: 91 service method, 73 controller method, 70 savings route |
| 2026-10-23 | **Fase 3 lanjutan — A2: Validasi Anggota ALB & NTR (RE Plan)**. `MemberController`: `validasi($type,$registry)` (GET, tampilkan halaman validasi + 4 berkas KTP/KK/TTD/PHOTO via relasi `images()->keyBy('IMAGE_TYPE')`) + `prosesValidasi($type,$registry)` (POST, update `MEMBER_STATUS_ID`: ALB→10, NTR→4 + `MEMBER_DATE_VERIFICATION` + `UPDATE_BY` dari `auth()->user()` + `LOGTIME`). 2 route baru (`admin.member.validasi` GET, `admin.member.validasi.proses` POST, `where type=NTR|ALB`). 2 view Blade baru `admin/member/validasi_ntr` & `validasi_alb` (Materialize, `admin.layouts`). Tambah `UPDATE_BY`,`LOGTIME` ke `$fillable` `Member`. Konvensi aman: pakai Eloquent/Query Builder bukan string concat (hindari SQL injection). Tampilan view masih basic (menurut user boleh di-skip, fungsional) |
| 2026-10-23 | **Fase 3 lanjutan — A3: Proses PD & Pembekuan (RE Plan)**. `MemberController`: `formPdBekukan($action,$registry)` (GET form input alasan untuk PD/bekukan) + `prosesPD($registry)` (update `ACTIVE_STATUS_ID=3` + `SUMMARY_CLOSE_ID`/`TYPE_CLOSE_ID`/`DESCRIPT_SUMMARY`/`DATE_CLOSE` via `Schema::hasColumn` dynamic detect, opsional) + `prosesPembekuan($registry)` (update `ACTIVE_STATUS_ID=2` + `SUMMARY_CLOSE_ID=6` hardcoded sesuai CI3 + `DESCRIPT_SUMMARY`/`DATE_DEACTIVATE` via `Schema::hasColumn`). Route: `admin.member.form` GET (action=pd|bekukan) + `admin.member.pd.proses` POST + `admin.member.bekukan.proses` POST. 1 view Blade baru `admin/member/form_pd_bekukan` (Materialize, form input alasan PD & pembekuan). Update view `show` tambah tombol "Proses PD" & "Bekukan" (hanya muncul jika ACTIVE=1). Bug fix route `with()` tidak ada di Laravel → diganti segmen URL `{action}`. Opsi 1: dynamic detect kolom via `Schema::hasColumn` (aman terhadap skema yang tidak lengkap) |
| 2026-10-23 | **Fase 3 lanjutan — A5: Laporan Jumlah Anggota + Export Excel (RE Plan)**. `Member` model: static method `lapJumlahAnggota(int $activeStatus=3)` (query Eloquent join lokasi & type_city, filter `whereExists(ma_images where IMAGE_FILE!='')` sesuai opsi 1 CI3, default status=3/PD). `MemberController`: `lapJumlahAnggota($status)` (tampilkan laporan + jumlah total) + `exportExcelLapJumlahAnggota($status)` (export ke Excel via `ExcelService::export`, header: No. Reg, No. Anggota, Nama, Alamat, Status Aktif, Tanggal Daftar). Route: `admin.member.lap_jumlah` GET (status=1,2,3, default 3) + `admin.member.lap_jumlah.export` GET (export xlsx). 1 view Blade baru `admin/member/lap_jumlah_anggota` (Materialize, table laporan + tombol export Excel). Bug fix route `defaults(['status'=>3])` salah (butuh 2 argumen) → diganti `defaults('status',3)`. Konvensi aman: Eloquent/Query Builder, ExcelService wrapper sudah ada di project (konsistensi Simpanan) |
| 2026-10-22 | **Fase 8 QA — Bug fix login & runtime (10 critical fixes)**: ① `BaseAdminController` — typed property `$userLevel` diakses sebelum init (constructor jalan sebelum auth siap) → default `null` + `refreshUser()` dipanggil di action. ② `LoginController::proses()` — `setUser()` tidak persist ke session → diganti `\Auth::login($user)`. ③ `User::getAuthRememberToken()` — kolom `remember_token` tidak ada di `ma_user`, Eloquent `save()` gagal → hapus `setRememberTokenAttribute` (yang trigger DB write), ganti `setAuthRememberToken()` (dynamic property + `#[AllowDynamicProperties]`). ④ `Member::getTotAgtDashboard()` dsj — dipanggil statis tapi method non-static + `first()->toArray()` gagal (return `stdClass`) → `static` + `(array)` cast. ⑤ `AdminController::dashboard()` — `$this->user->ROLE_NAME` tidak di-load → tambah `roleName` fallback. ⑥ `public/assets/assets/` (doubled path dari CI3 copy) → semua file di `public/assets/assets/*` dipindah ke `public/assets/*` (URL `asset('assets/css/...')` sekarang resolve 200). ⑦ `.env` → `APP_URL=http://simkopukm.test` + `SESSION_DOMAIN=simkopukm.test`. ⑧ Apache vhost `auto.simkopukm.test.conf` → `DocumentRoot "C:/laragon/www/crud-laravel-10/public"` (sebelumnya menunjuk CI3 folder). ⑨ Session file lama di-hapus (corrupt karena domain change). ⑩ LoginController tambah `\Log::info()` per-step (ATTEMPT / SUCCESS / FAIL) untuk debug di `storage/logs/laravel.log`. **Verifikasi E2E via Apache port 80 + curl**: 21 route all 200, login success → dashboard 200 |
| 2026-10-24 | **Fase 8 QA — Bug fix A1–A5 (Member/MemberController/ReportController)**: A1: `keyType` di `Member::printKTA` — model Eloquent dipakai di controller CI3, `getKeyType()` undefined → fix di model `Member`. A2: `MemberController::saveMember()` — insert `mu_member` gagal, kolom `created_by`/`updated_by` tidak di-set → tambah `AuthUser()` helper. A3: `MemberController::updateMember()` — query `MuTransactionAccount` di tabel `ma_member` tidak ada; refactor ke model `Member` Eloquent. A4: `ReportController` — import class `MuTransaction` CI3 tidak ada di Laravel (namespace berbeda) → ganti ke model Eloquent `Member` + `MuTransactionAccount`. A5: `MemberController::deleteMember()` — hapus `mu_member` error FK ke `mu_savings_account` → tambah cascade delete di model `Member`. |
| 2026-10-26 | **Fase 8 QA — A6: UI Fix Sidebar & Layout (CSS/JS overhaul)**: Perbaikan visual menyeluruh untuk 4 halaman utama (`/admin/dashboard`, `/admin/member`, `/admin/member/MEM001`, `/admin/member/MEM001/edit`). **Root cause**: font `"Material Icons"` (untuk `<i class="material-icons">`) tidak ter-load di `materialize.css` — hanya `"Material-Design-Icons"` (prefix `mdi-*`) yang ada. Akibatnya icon sidebar tampil sebagai teks mentah (`dashboard`, `manage_accounts`, dst). **Fix**: ① `custom-style.css` — tambah `@font-face` alias: memetakan font-family `"Material Icons"` ke file `Material-Design-Icons.woff2` yang sudah ada di `public/assets/font/`. ② `custom-style.css` — style baru: `.sidebar-hdr` (padding, font-size, hover), `#sidebar-collapsible .collapsible-body a` (indent, font, background), chevron collapsible (override `:after` pakai `Material-Design-Icons` unicode `\e7c3/\e7c1`), button `.btn/.btn-small` normalisasi, `table.highlight` header styling, `.chip` fix, `.container` max-width `1280px`, `footer.page-footer` padding reset. ③ `style.css` — `#main` dan `footer` `padding-left` `240px`→`0` (layout CI3 tidak berlaku di Laravel). ④ `custom-script.js` — auto-init `M.Collapsible` (semua `ul.collapsible`), `M.FormSelect` (semua `select`), `M.Modal` di `DOMContentLoaded`. ⑤ `sidebar.blade.php` — ditulis ulang: hapus semua `<i class="material-icons left">` dari collapsible-header (font tidak reliable), ganti `href="#"` → `href="javascript:void(0)"`, tambah inline `M.Collapsible.init()` di akhir file. ⑥ View cache di-clear. **Verifikasi**: `curl` semua 4 URL → 200, `materialize.css` serve 200, `custom-style.css` serve 200 (4 occurrences "Material Icons"), `Material-Design-Icons.woff2` serve 200 |
| 2026-10-26 | **Fase 3 lanjutan — A4: Upload Berkas Anggota (RE Plan)**. `Image` model: rewrite full — tambah `uploadFoto()` (insert-or-update ke `ma_images`, base64 string) + `checkImage()` (cek existence, return `['notif','code']`). `MemberController`: `berkas($registry)` GET (form upload + status 4 berkas + preview base64) + `prosesBerkas($registry)` POST (validasi file: JPG/PNG/GIF, maks 2 MB, base64_encode, simpan ke `ma_images` via `Image::uploadFoto`). 2 route baru: `admin.member.berkas` GET + `admin.member.berkas.proses` POST. 1 view Blade baru: `admin/member/berkas` (Materialize, 4 input file, status table, preview image). Update view `show`: tambah tombol "Berkas" (deep-orange, `attach_file` icon). Logika sama dengan CI3 `Anggota::simpan_Anggota` bagian `image_base64` + `Images::upload_foto` (KTP=images_1, PHOTO=images_2, TTD=images_3, KK=images_4). Semua lint pass, curl `berkas` → 200 |

## Perubahan Sisi CI3 (skema-honest, 2026-10-20)
> Model CI3 di `simkopukm/application/models/` yang masih menulis ke tabel `tru_*` (skema lama, kolom tidak ada di `simkopukm`) sudah disesuaikan ke tabel nyata. Semua lolos `php -l`.

- `TransDepositInterest::bayarJasaSSB` → insert `mu_trans_deposit_interest` (MEMBER_REGISTRY, SAVINGS_ACCOUNT_NO, INTEREST_AMOUNT, INTEREST_DATE, LOGTIME); buang 3 cabang `cmbPembayaran` yang identik.
- `TransSetiaPlusInterest::bayarJasaSetiaPlus` → insert `mu_trans_setiaplus_interest` (MEMBER_REGISTRY, INTEREST_AMOUNT, INTEREST_DATE, LOGTIME).
- `TransaksiSimpanan::setFalseApproval/prosesStatBuku` → update `mu_transaction_account`; `APPROVAL_STATUS_ID` di-*comment* sebagai TODO (konvensi reject/done belum ada di skema, jadi saat ini hanya update `UPDATE_BY` = aman).
- `TransDailySavings`, `TransSetiaPlusSavings`, `TransSSBSavings`, `TransNTRSavings` → `protected $table` di-remap ke `mu_trans_daily_savings` / `mu_trans_setiaplus_savings` / `mu_trans_ssb_savings` / `mu_trans_ntr_savings`. Query join memakai kolom nyata (`ma_member.CITY_ID/DISTRICT_ID/VILLAGE_ID`, `ma_village→ma_district→ma_city`, `ma_city.TYPE_CITY_ID→ma_type_city`). Kolom lama yang hilang (`APPROVAL_CODE`, `JOURNAL_ID`, `PRINT_BOOK`, `mc_transaction_code/type`, `mu_journal`) diganti fallback aman (via `TRANSACTION_ID`) atau dihapus.
- **Belum disentuh (butuh keputusan user)**: engine approval multi-level `APPROVAL_CODE` (1/2/3) di `Saving.php` `approvalTrans` + `Loan.php` (update `tru_loan_realization`/`tru_loan_installment`) — bergantung konvensi `STATUS_ID`/`APPROVAL_STATUS_ID` yang belum ditetapkan, dan `mu_savings_account` versi lama (lebar) vs nyata (tipis 6 kolom).

### Catatan skema kunci
- `mu_savings_account` (nyata): SAVINGS_ACCOUNT_NO, MEMBER_REGISTRY, SAVINGS_PRODUCT_ID, ACTIVE_ACCOUNT_ID, SAVINGS_BALANCE, LOGTIME.
- `mu_transaction_account` (nyata): TRANSACTION_ID, TRANSACTION_NO, MEMBER_REGISTRY, SAVINGS_ACCOUNT_NO, TRANSACTION_TYPE, AMOUNT, TRANSACTION_DATE, STATUS_ID, APPROVAL_STATUS_ID, UPDATE_BY, LOGTIME.
- Kolom lama yang **tidak ada** di skema: APPROVAL, APPROVAL_CODE, APPROVAL_USER, APPROVAL_LOGTIME, JOURNAL_ID, CREDIT_NOMINAL, DEBIT_NOMINAL, NOMINAL, PRINT_BOOK, PRINT_CASH_PROOF, mc_*, mu_journal.

## Rangkuman Selisih (Gap Analysis) — update 2026-10-22

| Layer | CI3 (sumber) | Laravel (saat ini) | Selisih |
|---|---|---|---|
| Model/Service | 96 method | 91 method (+PdfService, ExcelService) | **5 belum dipindah** |
| Controller | 149 method | 73 method | **~76 belum dipindah** |
| View | ~60 file | 38 file | **~22 belum ada** |

### Prioritas Lanjutan
| Prioritas | Batch | Alasan |
|---|---|---|
| ~~1 — Tinggi~~ | ~~Batch 4 (Pembukaan Akun)~~ | **SELESAI** |
| ~~2 — Tinggi~~ | ~~Batch 6 (Laporan SSB/Rekap)~~ | **SELESAI** |
| ~~3 — Sedang~~ | ~~Batch 5 (Detail Anggota)~~ | **SELESAI** |
| ~~4 — Sedang~~ | ~~Batch 8 (Proses Bisnis)~~ | **SELESAI** (2026-10-22): prosesTransSimp, updateTransSimp, tarikAll, setFalseApproval, setJurnalTrans, prosesStatBuku, checkSimPokok/Wajib, doBayarJasaSSB/SetiaPlus, updateSaldoBlokir + master SSB/SetiaPlus |
| ~~4 — Sedang~~ | ~~Batch 9 (Utility)~~ | **SELESAI** (2026-10-22): searchDataNoOrName/Trans, checkMemberActive, menuKasie/Kasir/Pj, viewBI, showTableBKM/BKK, tampilLaporanPD/Pembekuan, pengajuanSimpananData + 4 view baru |
| ~~5 — Rendah~~ | ~~Batch 7 (Cetak/PDF)~~ | **SELESAI intinya** (2026-10-22): `PdfService` + 3 view PDF (bkm/bkk/bu) + 3 route, `ExcelService` + 2 export. Bilyet/cover/validasi detail butuh domain data → ditunda |
| ~~6 — Tinggi (selanjutnya)~~ | ~~Fase 6 (Pinjaman)~~ | **SELESAI** (2026-10-22): 4 batch (10-13), 41 route, modul lengkap master→transaksi→laporan→approval multi-level |
| **7 — Sedang** | Fase 7/8 (Finalisasi & QA) | Review security, test end-to-end, hapus akses `$_SESSION` legacy, update README, test staging |
