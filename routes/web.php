<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ModuleController;

use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\FeeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ConstantsController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Saving\SavingController;
use App\Http\Controllers\Loan\LoanController;

/*
|--------------------------------------------------------------------------
| Web Routes - SIMKOP Migration
|--------------------------------------------------------------------------
*/

// ===== Auth Routes =====
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'proses'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// ===== Root =====
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

// ===== Admin Routes (require auth) =====
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    // Dashboard
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Member CRUD
    Route::get('member', [MemberController::class, 'index'])->name('admin.member.index');
    Route::get('member/create', [MemberController::class, 'create'])->name('admin.member.create');
    Route::post('member', [MemberController::class, 'store'])->name('admin.member.store');

    // Cetak / Preview KTA Anggota (A1) — DITEMPATKAN SEBELUM member/{registry} agar URL 2 segmen tidak tumpang-tindih dengan {registry}
    Route::get('member/{registry}/kta/preview', [MemberController::class, 'previewKTA'])->name('admin.member.preview_kta');
    Route::get('member/{registry}/kta/pdf', [MemberController::class, 'cetakKTA'])->name('admin.member.cetak_kta');

    Route::get('member/{registry}', [MemberController::class, 'show'])->name('admin.member.show');
    Route::get('member/{registry}/edit', [MemberController::class, 'edit'])->name('admin.member.edit');
    Route::put('member/{registry}', [MemberController::class, 'update'])->name('admin.member.update');
    Route::delete('member/{registry}', [MemberController::class, 'destroy'])->name('admin.member.destroy');

    // Validasi Anggota (NTR & ALB)
    // Tampilkan halaman validasi berdasarkan tipe anggota (NTR/ALB)
    Route::get('member/{type}/{registry}/validasi', [MemberController::class, 'validasi'])->where(['type' => 'NTR|ALB'])->name('admin.member.validasi');
    // Proses validasi: ubah status + catat tanggal verifikasi
    Route::post('member/{type}/{registry}/validasi/proses', [MemberController::class, 'prosesValidasi'])->where(['type' => 'NTR|ALB'])->name('admin.member.validasi.proses');

    // Laporan Jumlah Anggota (A5)
    // Tampilkan laporan per status (1=Aktif, 2=Bekukan, 3=PD)
    // Default status = 3 (PD) kalau tidak ada di URL
    Route::get('member/laporan/jumlah/{status?}', [MemberController::class, 'lapJumlahAnggota'])->whereIn('status', [1,2,3])->defaults('status', 3)->name('admin.member.lap_jumlah');
    // Export Excel laporan jumlah anggota
    Route::get('member/laporan/jumlah/export/{status?}', [MemberController::class, 'exportExcelLapJumlahAnggota'])->whereIn('status', [1,2,3])->defaults('status', 3)->name('admin.member.lap_jumlah.export');

    // Proses PD (Pengunduran Diri) & Pembekuan Anggota
    // GET form PD / Bekukan — dipanggil dari detail member
    Route::get('member/{action}/{registry}/form', [MemberController::class, 'formPdBekukan'])->whereIn('action', ['pd','bekukan'])->name('admin.member.form');
    // Proses PD (update ACTIVE_STATUS_ID=3)
    Route::post('member/{registry}/pd/proses', [MemberController::class, 'prosesPD'])->name('admin.member.pd.proses');
    // Proses Pembekuan (update ACTIVE_STATUS_ID=2)
    Route::post('member/{registry}/bekukan/proses', [MemberController::class, 'prosesPembekuan'])->name('admin.member.bekukan.proses');

    // A4: Upload berkas (KTP, KK, FOTO, TTD)
    Route::get('member/{registry}/berkas', [MemberController::class, 'berkas'])->name('admin.member.berkas');
    Route::post('member/{registry}/berkas', [MemberController::class, 'prosesBerkas'])->name('admin.member.berkas.proses');

    // Role CRUD
    Route::get('role', [RoleController::class, 'index'])->name('admin.role.index');
    Route::get('role/create', [RoleController::class, 'create'])->name('admin.role.create');
    Route::post('role', [RoleController::class, 'store'])->name('admin.role.store');
    Route::get('role/{roleId}/edit', [RoleController::class, 'edit'])->name('admin.role.edit');
    Route::put('role/{roleId}', [RoleController::class, 'update'])->name('admin.role.update');
    Route::delete('role/{roleId}', [RoleController::class, 'destroy'])->name('admin.role.destroy');

    // Module CRUD
    Route::get('module', [ModuleController::class, 'index'])->name('admin.module.index');
    Route::get('module/create', [ModuleController::class, 'create'])->name('admin.module.create');
    Route::post('module', [ModuleController::class, 'store'])->name('admin.module.store');
    Route::get('module/{moduleId}/edit', [ModuleController::class, 'edit'])->name('admin.module.edit');
    Route::put('module/{moduleId}', [ModuleController::class, 'update'])->name('admin.module.update');
    Route::delete('module/{moduleId}', [ModuleController::class, 'destroy'])->name('admin.module.destroy');

    // Location CRUD (Village, District, City)
    // Village
    Route::get('location/village', [LocationController::class, 'villageIndex'])->name('admin.location.village');
    Route::get('location/village/create', [LocationController::class, 'villageCreate'])->name('admin.location.village.create');
    Route::post('location/village', [LocationController::class, 'villageStore'])->name('admin.location.village.store');
    Route::get('location/village/{id}/edit', [LocationController::class, 'villageEdit'])->name('admin.location.village.edit');
    Route::put('location/village/{id}', [LocationController::class, 'villageUpdate'])->name('admin.location.village.update');
    Route::delete('location/village/{id}', [LocationController::class, 'villageDestroy'])->name('admin.location.village.destroy');

    // District
    Route::get('location/district', [LocationController::class, 'districtIndex'])->name('admin.location.district');
    Route::get('location/district/create', [LocationController::class, 'districtCreate'])->name('admin.location.district.create');
    Route::post('location/district', [LocationController::class, 'districtStore'])->name('admin.location.district.store');
    Route::get('location/district/{id}/edit', [LocationController::class, 'districtEdit'])->name('admin.location.district.edit');
    Route::put('location/district/{id}', [LocationController::class, 'districtUpdate'])->name('admin.location.district.update');
    Route::delete('location/district/{id}', [LocationController::class, 'districtDestroy'])->name('admin.location.district.destroy');

    // City
    Route::get('location/city', [LocationController::class, 'cityIndex'])->name('admin.location.city');
    Route::get('location/city/create', [LocationController::class, 'cityCreate'])->name('admin.location.city.create');
    Route::post('location/city', [LocationController::class, 'cityStore'])->name('admin.location.city.store');
    Route::get('location/city/{id}/edit', [LocationController::class, 'cityEdit'])->name('admin.location.city.edit');
    Route::put('location/city/{id}', [LocationController::class, 'cityUpdate'])->name('admin.location.city.update');
    Route::delete('location/city/{id}', [LocationController::class, 'cityDestroy'])->name('admin.location.city.destroy');

    // Employee CRUD
    Route::get('employee', [EmployeeController::class, 'index'])->name('admin.employee.index');
    Route::get('employee/create', [EmployeeController::class, 'create'])->name('admin.employee.create');
    Route::post('employee', [EmployeeController::class, 'store'])->name('admin.employee.store');
    Route::get('employee/{id}/edit', [EmployeeController::class, 'edit'])->name('admin.employee.edit');
    Route::put('employee/{id}', [EmployeeController::class, 'update'])->name('admin.employee.update');
    Route::delete('employee/{id}', [EmployeeController::class, 'destroy'])->name('admin.employee.destroy');

    // Fee CRUD
    Route::get('fee', [FeeController::class, 'index'])->name('admin.fee.index');
    Route::get('fee/create', [FeeController::class, 'create'])->name('admin.fee.create');
    Route::post('fee', [FeeController::class, 'store'])->name('admin.fee.store');
    Route::get('fee/{id}/edit', [FeeController::class, 'edit'])->name('admin.fee.edit');
    Route::put('fee/{id}', [FeeController::class, 'update'])->name('admin.fee.update');
    Route::delete('fee/{id}', [FeeController::class, 'destroy'])->name('admin.fee.destroy');

    // User (Akun Login) CRUD
    Route::get('user', [UserController::class, 'index'])->name('admin.user.index');
    Route::get('user/create', [UserController::class, 'create'])->name('admin.user.create');
    Route::post('user', [UserController::class, 'store'])->name('admin.user.store');
    Route::get('user/{registry}/edit', [UserController::class, 'edit'])->name('admin.user.edit');
    Route::put('user/{registry}', [UserController::class, 'update'])->name('admin.user.update');
    Route::delete('user/{registry}', [UserController::class, 'destroy'])->name('admin.user.destroy');

    // Constants (Konstanta Simpanan & Pinjaman)
    Route::get('constants', [ConstantsController::class, 'index'])->name('admin.constants.index');
    Route::get('constants/simhar', [ConstantsController::class, 'simhar'])->name('admin.constants.simhar');
    Route::get('constants/ssb', [ConstantsController::class, 'ssb'])->name('admin.constants.ssb');
    Route::get('constants/pinjaman', [ConstantsController::class, 'pinjaman'])->name('admin.constants.pinjaman');
    Route::post('constants/savings-interest', [ConstantsController::class, 'saveSavingsInterest'])->name('admin.constants.save_savings_interest');
    Route::delete('constants/savings-interest/{id}', [ConstantsController::class, 'deleteSavingsInterest'])->name('admin.constants.delete_savings_interest');
    Route::post('constants/simpanyan-product', [ConstantsController::class, 'saveSimpananProduct'])->name('admin.constants.save_simpanan_product');
    Route::post('constants/loan-interest', [ConstantsController::class, 'saveLoanInterest'])->name('admin.constants.save_loan_interest');
    Route::delete('constants/loan-interest/{id}', [ConstantsController::class, 'deleteLoanInterest'])->name('admin.constants.delete_loan_interest');
    Route::post('constants/loan-product', [ConstantsController::class, 'saveLoanProduct'])->name('admin.constants.save_loan_product');

    // Audit Trail
    Route::get('audit', [AuditController::class, 'index'])->name('admin.audit.index');

    // ===== Fase 5: Modul Simpanan (Saving) =====
    // Master Simpanan Harian
    Route::get('savings/harian', [SavingController::class, 'harian'])->name('admin.savings.harian');
    Route::post('savings/harian/store', [SavingController::class, 'storeSimHar'])->name('admin.savings.store_simhar');

    // Cek Saldo
    Route::get('savings/cek-saldo/simhar', [SavingController::class, 'cekSaldoSimhar'])->name('admin.savings.cek_saldo_simhar');
    Route::get('savings/cek-saldo/ntr', [SavingController::class, 'cekSaldoNTR'])->name('admin.savings.cek_saldo_ntr');
    Route::get('savings/cek-saldo/ssb', [SavingController::class, 'cekSaldoSSB'])->name('admin.savings.cek_saldo_ssb');

    // Tutup Rekening
    Route::get('savings/tutup-rekening/simhar', [SavingController::class, 'tutupRekSimhar'])->name('admin.savings.tutup_rek_simhar');
    Route::post('savings/tutup-rekening/simhar/proses', [SavingController::class, 'prosesPenutupanSimHar'])->name('admin.savings.proses_penutupan_simhar');
    Route::get('savings/tutup-rekening/setiaplus', [SavingController::class, 'tutupRekSetiaPlus'])->name('admin.savings.tutup_rek_setiaplus');
    Route::get('savings/tutup-rekening/ssb', [SavingController::class, 'tutupRekAkunSSB'])->name('admin.savings.tutup_rek_ssb');
    Route::post('savings/tutup-rekening/ssb/proses', [SavingController::class, 'prosesPenutupanSSB'])->name('admin.savings.proses_penutupan_ssb');

    // Master NTR
    Route::get('savings/ntr', [SavingController::class, 'masterTransNTR'])->name('admin.savings.ntr');
    Route::get('savings/ntr/transaksi', [SavingController::class, 'transNTR'])->name('admin.savings.trans_ntr');

    // Master SetiaPlus & SSB
    Route::get('savings/setiaplus', [SavingController::class, 'SetiaPlus'])->name('admin.savings.setiaplus');
    Route::get('savings/ssb', [SavingController::class, 'SSB'])->name('admin.savings.ssb');

    // Daftar Akun
    Route::get('savings/list-akun', [SavingController::class, 'listAkunSimpanan'])->name('admin.savings.list_akun');

    // Batch 2: Transaksi Harian
    Route::get('savings/harian/transaksi', [SavingController::class, 'transSimHar'])->name('admin.savings.trans_simhar');
    Route::post('savings/harian/transaksi/proses', [SavingController::class, 'prosesTransSimpHar'])->name('admin.savings.proses_trans_simp_har');

    // Persetujuan
    Route::get('savings/persetujuan', [SavingController::class, 'persetujuanTrans'])->name('admin.savings.persetujuan');
    Route::post('savings/persetujuan/proses', [SavingController::class, 'prosesApprovalTrans'])->name('admin.savings.proses_approval');

    // Cetak BKM / BKK / BU
    Route::get('savings/cetak-bkm', [SavingController::class, 'viewBKM'])->name('admin.savings.cetak_bkm');
    Route::get('savings/cetak-bkk', [SavingController::class, 'viewBKK'])->name('admin.savings.cetak_bkk');
    Route::get('savings/cetak-bu', [SavingController::class, 'viewBU'])->name('admin.savings.cetak_bu');

    // Batch 3: Transaksi NTR form + SSB
    Route::get('savings/ntr/form', [SavingController::class, 'transNtrForm'])->name('admin.savings.trans_ntr_form');
    Route::post('savings/ntr/proses', [SavingController::class, 'prosesTransNtr'])->name('admin.savings.proses_trans_ntr');
    Route::get('savings/ssb/transaksi', [SavingController::class, 'transSsb'])->name('admin.savings.trans_ssb');
    Route::post('savings/ssb/transaksi/proses', [SavingController::class, 'prosesTransSsb'])->name('admin.savings.proses_trans_ssb');

    // Auto Closing
    Route::get('savings/auto-closing', [SavingController::class, 'autoClosing'])->name('admin.savings.auto_closing');
    Route::post('savings/auto-closing/proses', [SavingController::class, 'prosesCloseByUser'])->name('admin.savings.proses_close_by_user');

    // Bayar Jasa
    Route::get('savings/tagihan-jasa/setiaplus', [SavingController::class, 'tagihanJasaSetiaPlus'])->name('admin.savings.tagihan_jasa_setiaplus');
    Route::get('savings/tagihan-jasa/ssb', [SavingController::class, 'tagihanJasaSsb'])->name('admin.savings.tagihan_jasa_ssb');
    Route::post('savings/tagihan-jasa/setiaplus/bayar', [SavingController::class, 'prosesBayarJasaSetiaPlus'])->name('admin.savings.proses_bayar_jasa_setiaplus');

    // ===== Batch 8: Proses Bisnis Lanjutan =====
    // Transaksi generik NTR / update / tarikAll
    Route::post('savings/ntr/transaksi-generik', [SavingController::class, 'prosesTransSimp'])->name('admin.savings.proses_trans_simp');
    Route::post('savings/update-trans', [SavingController::class, 'updateTransSimp'])->name('admin.savings.update_trans_simp');
    Route::post('savings/harian/tarik-all', [SavingController::class, 'tarikAll'])->name('admin.savings.tarik_all');

    // Approval & status
    Route::post('savings/persetujuan/tolak', [SavingController::class, 'setFalseApproval'])->name('admin.savings.set_false_approval');
    Route::post('savings/persetujuan/stat-buku', [SavingController::class, 'prosesStatBuku'])->name('admin.savings.proses_stat_buku');

    // Cek saldo / pokok (JSON)
    Route::post('savings/cek/pokok', [SavingController::class, 'checkSimPokok'])->name('admin.savings.check_sim_pokok');
    Route::post('savings/cek/saldo-pokok-wajib', [SavingController::class, 'checkSaldoSimPokokWajib'])->name('admin.savings.check_saldo_sim_pokok_wajib');

    // Bayar jasa (semua tagihan jatuh tempo)
    Route::post('savings/tagihan-jasa/ssb/bayar', [SavingController::class, 'doBayarJasaSSB'])->name('admin.savings.do_bayar_jasa_ssb');
    Route::post('savings/tagihan-jasa/setiaplus/bayar-semua', [SavingController::class, 'doBayarJasaSetiaplus'])->name('admin.savings.do_bayar_jasa_setiaplus');

    // Simpan master SSB / SetiaPlus (batch 4 sisaan)
    Route::post('savings/ssb/store', [SavingController::class, 'storeSsb'])->name('admin.savings.store_ssb');
    Route::post('savings/setiaplus/store', [SavingController::class, 'storeSetiaplus'])->name('admin.savings.store_setiaplus');

    // ===== Batch 9: Supporting / Utility =====
    // Search / lookup (JSON endpoint)
    Route::post('savings/search/anggota', [SavingController::class, 'searchDataNoOrName'])->name('admin.savings.search_anggota');
    Route::post('savings/search/trans', [SavingController::class, 'searchDataNoOrNameTrans'])->name('admin.savings.search_trans');
    Route::post('savings/search/trans-id', [SavingController::class, 'searchDataTransSimpHar'])->name('admin.savings.search_trans_id');

    // Menu dashboard
    Route::get('savings/menu/kasie', [SavingController::class, 'menuKasie'])->name('admin.savings.menu_kasie');
    Route::get('savings/menu/kasir', [SavingController::class, 'menuKasir'])->name('admin.savings.menu_kasir');
    Route::get('savings/menu/pj', [SavingController::class, 'menuPjSimpanan'])->name('admin.savings.menu_pj');

    // View BU/BI
    Route::get('savings/bukti-informasi', [SavingController::class, 'viewBI'])->name('admin.savings.view_bu');

    // ===== Batch 7: Cetak / PDF (DomPDF) =====
    Route::get('savings/cetak-bkm/pdf', [SavingController::class, 'cetakBKM'])->name('admin.savings.cetak_bkm_pdf');
    Route::get('savings/cetak-bkk/pdf', [SavingController::class, 'cetakBKK'])->name('admin.savings.cetak_bkk_pdf');
    Route::get('savings/cetak-bu/pdf', [SavingController::class, 'cetakBU'])->name('admin.savings.cetak_bu_pdf');

    // Export Excel
    Route::get('savings/export/daftar-akun/xlsx', [SavingController::class, 'exportExcelDaftarAkun'])->name('admin.savings.export_daftar_akun_xlsx');
    Route::get('savings/export/lap-saldo/xlsx', [SavingController::class, 'exportExcelLapSaldo'])->name('admin.savings.export_lap_saldo_xlsx');

    // Tabel BKM/BKK
    Route::get('savings/bukti-kas/masuk', [SavingController::class, 'showTableBuktiKasMasuk'])->name('admin.savings.bukti_kas_masuk');
    Route::get('savings/bukti-kas/keluar', [SavingController::class, 'showTableBuktiKasKeluar'])->name('admin.savings.bukti_kas_keluar');

    // Laporan PD / Pembekuan / Pengajuan
    Route::post('savings/laporan/pd', [SavingController::class, 'tampilLaporanPD'])->name('admin.savings.lap_pd');
    Route::get('savings/laporan/pembekuan', [SavingController::class, 'tampilLapPembekuan'])->name('admin.savings.lap_pembekuan');
    Route::get('savings/pengajuan', [SavingController::class, 'pengajuanSimpanan'])->name('admin.savings.pengajuan');

    // Laporan
    Route::get('savings/laporan/pembukaan/simhar', [SavingController::class, 'lapPembukaanAkunSimhar'])->name('admin.savings.lap_pembukaan_akun_simhar');
    Route::get('savings/laporan/pembukaan/ntr',   [SavingController::class, 'lapPembukaanAkunNTR'])->name('admin.savings.lap_pembukaan_akun_ntr');
    Route::get('savings/laporan/pembukaan/ssb',   [SavingController::class, 'lapPembukaanAkunSSB'])->name('admin.savings.lap_pembukaan_akun_ssb');
    Route::get('savings/laporan/rekap/saldo',     [SavingController::class, 'lapSaldoSimpRekap'])->name('admin.savings.lap_saldo_simp_rekap');
    Route::get('savings/laporan/rinci/saldo',     [SavingController::class, 'lapSaldoSimpRinci'])->name('admin.savings.lap_saldo_simp_rinci');
    Route::get('savings/laporan/buku-besar/ssb',  [SavingController::class, 'lapBukuBesarSSB'])->name('admin.savings.lap_buku_besar_ssb');
    Route::get('savings/laporan/buku-besar/ntr',  [SavingController::class, 'lapBukuBesarNTR'])->name('admin.savings.lap_buku_besar_ntr');
    Route::get('savings/laporan/mutasi/ntr',      [SavingController::class, 'lapMutasiSaldoNTR'])->name('admin.savings.lap_mutasi_saldo_ntr');
    Route::get('savings/laporan/penutupan/simhar',[SavingController::class, 'lapPenutupanAkunSimhar'])->name('admin.savings.lap_penutupan_akun_simhar');
    // Laporan SSB / Jatuh Tempo
    Route::get('savings/laporan/jatem/ssb/rekap',   [SavingController::class, 'lapJatemSSBRekap'])->name('admin.savings.lap_jatem_ssb_rekap');
    Route::get('savings/laporan/jatem/ssb/rinci',    [SavingController::class, 'lapJatemSSBRinci'])->name('admin.savings.lap_jatem_ssb_rinci');
    Route::get('savings/laporan/daftar-akun/ssb',    [SavingController::class, 'lapDaftarAkunRekapSSB'])->name('admin.savings.lap_daftar_akun_ssb');
    Route::get('savings/laporan/daftar-akun/ssb/rinci', [SavingController::class, 'lapDaftarAkunRinciSSB'])->name('admin.savings.lap_daftar_akun_ssb_rinci');

    // ===== Batch 10: Modul Pinjaman (Loan) =====
    // Master Pinjaman
    Route::get('loan/ukm', [LoanController::class, 'pinjamUkm'])->name('admin.loan.ukm');
    Route::get('loan/mikro', [LoanController::class, 'pinjamMikro'])->name('admin.loan.mikro');
    Route::get('loan/jaminan', [LoanController::class, 'pinjamJaminan'])->name('admin.loan.jaminan');
    Route::get('loan/pj-pinjaman', [LoanController::class, 'pjPinjaman'])->name('admin.loan.pj_pinjaman');

    // Form Pengajuan
    Route::get('loan/pengajuan-pp', [LoanController::class, 'pengajuanPP'])->name('admin.loan.pengajuan_pp');
    Route::get('loan/pengajuan-jatem', [LoanController::class, 'pengajuanJatem'])->name('admin.loan.pengajuan_jatem');
    Route::get('loan/pengajuan-rsjatem', [LoanController::class, 'pengajuanRSJatem'])->name('admin.loan.pengajuan_rsjatem');
    Route::get('loan/pengajuan-rsbyreq', [LoanController::class, 'pengajuanRSbyReq'])->name('admin.loan.pengajuan_rsbyreq');

    // JSON Endpoints — Lookup
    Route::get('loan/data-anggota-pinjaman', [LoanController::class, 'getDataAnggotaPinjaman'])->name('admin.loan.data_anggota_pinjaman');
    Route::get('loan/data-anggota-pinjaman/buku-besar', [LoanController::class, 'getDataAnggotaPinjamanBukuBesar'])->name('admin.loan.data_anggota_pinjaman_buku_besar');
    Route::get('loan/data-pinjaman-all-status', [LoanController::class, 'getDataPinjamanAllStatus'])->name('admin.loan.data_pinjaman_all_status');
    Route::get('loan/transaksi-hari-ini', [LoanController::class, 'getTransactionTodayPinjaman'])->name('admin.loan.transaksi_hari_ini');
    Route::post('loan/check-angsuran', [LoanController::class, 'checkAngsuran'])->name('admin.loan.check_angsuran');
    Route::post('loan/detail-pinjaman', [LoanController::class, 'detailPinjamanById'])->name('admin.loan.detail_pinjaman');
    Route::get('loan/tagihan', [LoanController::class, 'loadDataTagihan'])->name('admin.loan.tagihan');

    // Approval Flow
    Route::get('loan/persetujuan-trans', [LoanController::class, 'persetujuanTrans'])->name('admin.loan.persetujuan_trans');
    Route::post('loan/persetujuan-trans/proses', [LoanController::class, 'prosesApprovalTrans'])->name('admin.loan.proses_approval_trans');

    // Cetak BKM / BKK / BU
    Route::get('loan/cetak-bkm', [LoanController::class, 'viewBKM'])->name('admin.loan.cetak_bkm');
    Route::get('loan/cetak-bkk', [LoanController::class, 'viewBKK'])->name('admin.loan.cetak_bkk');
    Route::get('loan/cetak-bu', [LoanController::class, 'viewBI'])->name('admin.loan.cetak_bu');

    // ===== Batch 11: Tagihan, Angsuran Kolektif, Rescheduling =====
    Route::get('loan/tagihan-jatem', [LoanController::class, 'tagihanJatem'])->name('admin.loan.tagihan_jatem');
    Route::get('loan/angsuran-kolektif', [LoanController::class, 'angsuranKolektif'])->name('admin.loan.angsuran_kolektif');
    Route::post('loan/angsuran-kolektif/proses', [LoanController::class, 'prosesAngsuranKolektif'])->name('admin.loan.proses_angsuran_kolektif');
    Route::get('loan/rescheduling-jatem', [LoanController::class, 'reschedulingJatem'])->name('admin.loan.rescheduling_jatem');
    Route::post('loan/rescheduling-jatem/proses', [LoanController::class, 'prosesReschedulingJatem'])->name('admin.loan.proses_rescheduling_jatem');
    Route::get('loan/rescheduling-by-req', [LoanController::class, 'reschedulingByReq'])->name('admin.loan.rescheduling_by_req');
    Route::post('loan/rescheduling-by-req/proses', [LoanController::class, 'prosesReschedulingByReq'])->name('admin.loan.proses_rescheduling_by_req');
    Route::get('loan/detail-rescheduling', [LoanController::class, 'detailRescheduling'])->name('admin.loan.detail_rescheduling');
    Route::get('loan/data-tagihan-kolektif', [LoanController::class, 'dataTagihanKolektif'])->name('admin.loan.data_tagihan_kolektif');

    // ===== Batch 12: Laporan Agunan, Realisasi, Pelunasan, Resheduling =====
    Route::get('loan/laporan-agunan', [LoanController::class, 'laporanAgunan'])->name('admin.loan.laporan_agunan');
    Route::get('loan/laporan-realisisasi', [LoanController::class, 'laporanRealisasi'])->name('admin.loan.laporan_realisasi');
    Route::get('loan/laporan-pelunasan', [LoanController::class, 'laporanPelunasan'])->name('admin.loan.laporan_pelunasan');
    Route::get('loan/laporan-resheduling', [LoanController::class, 'laporanResheduling'])->name('admin.loan.laporan_resheduling');
    Route::get('loan/laporan-angsuran', [LoanController::class, 'laporanAngsuran'])->name('admin.loan.laporan_angsuran');
    Route::get('loan/form-realisisasi', [LoanController::class, 'formRealisasi'])->name('admin.loan.form_realisasi');
    Route::post('loan/proses-realisisasi', [LoanController::class, 'prosesRealisasi'])->name('admin.loan.proses_realisasi');
    Route::post('loan/proses-pelunasan', [LoanController::class, 'prosesPelunasan'])->name('admin.loan.proses_pelunasan');

    // ===== Batch 13: View Payment, Approval Multi-Level, Tunggakan =====
    Route::get('loan/view-payment', [LoanController::class, 'viewPayment'])->name('admin.loan.view_payment');
    Route::get('loan/approval-multi-level', [LoanController::class, 'approvalMultiLevel'])->name('admin.loan.approval_multi_level');
    Route::post('loan/approval-multi-level/proses', [LoanController::class, 'prosesApprovalMultiLevel'])->name('admin.loan.proses_approval_multi_level');
    Route::get('loan/laporan-tunggakan', [LoanController::class, 'laporanTunggakan'])->name('admin.loan.laporan_tunggakan');

    // ===== Batch 14: Cetak PDF & Export Excel (Fase 7) =====
    Route::get('loan/cetak-bkm/pdf', [LoanController::class, 'cetakBKM'])->name('admin.loan.cetak_bkm_pdf');
    Route::get('loan/cetak-bkk/pdf', [LoanController::class, 'cetakBKK'])->name('admin.loan.cetak_bkk_pdf');
    Route::get('loan/cetak-bu/pdf', [LoanController::class, 'cetakBU'])->name('admin.loan.cetak_bu_pdf');
    Route::get('loan/export/laporan-pinjaman/xlsx', [LoanController::class, 'exportExcelLaporanPinjaman'])->name('admin.loan.export_laporan_pinjaman');

    // ===== Batch 15: Fase B — MG Details, Survey, Pelunasan Detail, Agunan, Log AO =====
    // Parameter {no}: LOAN_ACCOUNT_NO (string, mis. CR2026...)
    Route::get('loan/mg-serti/{no}', [LoanController::class, 'mgSerti'])->where('no', '[A-Z]{2}[0-9]{6,}')->name('admin.loan.mg_serti');
    Route::get('loan/mg-simhar/{no}', [LoanController::class, 'mgSimhar'])->where('no', '[A-Z]{2}[0-9]{6,}')->name('admin.loan.mg_simhar');
    Route::get('loan/mg-potong/{no}', [LoanController::class, 'mgPotong'])->where('no', '[A-Z]{2}[0-9]{6,}')->name('admin.loan.mg_potong');

    // Form pengajuan MG — query param noAgt = MEMBER_NO / MEMBER_REGISTRY
    Route::get('loan/pengajuan-mg-emas',     [LoanController::class, 'pengajuanMGEmas'])->name('admin.loan.pengajuan_mg_emas');
    Route::get('loan/pengajuan-mg-simhar',    [LoanController::class, 'pengajuanMGSimhar'])->name('admin.loan.pengajuan_mg_simhar');
    Route::get('loan/pengajuan-mg-potong',    [LoanController::class, 'pengajuanMGPotongGaji'])->name('admin.loan.pengajuan_mg_potong');
    Route::post('loan/pengajuan-mg/proses',   [LoanController::class, 'prosesPengajuanMG'])->name('admin.loan.proses_pengajuan_mg');
    Route::get('loan/survey/{no}', [LoanController::class, 'formSurvey'])->where('no', '[A-Z]{2}[0-9]{6,}')->name('admin.loan.form_survey');
    Route::post('loan/survey/proses', [LoanController::class, 'prosesSurvey'])->name('admin.loan.proses_survey');
    Route::get('loan/pelunasan/{no}', [LoanController::class, 'pelunasan'])->where('no', '[A-Z]{2}[0-9]{6,}')->name('admin.loan.pelunasan');
    Route::post('loan/pelunasan/proses', [LoanController::class, 'prosesPelunasanDetail'])->name('admin.loan.proses_pelunasan_detail');
    Route::get('loan/agunan/{no}', [LoanController::class, 'agunanForm'])->where('no', '[A-Z]{2}[0-9]{6,}')->name('admin.loan.agunan');
    Route::post('loan/agunan/proses', [LoanController::class, 'prosesAgunan'])->name('admin.loan.proses_agunan');
    Route::get('loan/log-ao-pj/{no}', [LoanController::class, 'logAO'])->where('no', '[A-Z]{2}[0-9]{6,}')->name('admin.loan.log_ao');

    // ===== Batch 16: Fase B6 — SPH (Surat Perjanjian Hutang) & Laporan =====
    Route::get('loan/laporan-sph', [LoanController::class, 'laporanSPH'])->name('admin.loan.laporan_sph');
    Route::get('loan/sph/kecil/{no}/{agunanId?}', [LoanController::class, 'cetakSPHKecil'])
        ->where('no', '[A-Z]{2}[0-9]{6,}')->whereNumber('agunanId')
        ->name('admin.loan.cetak_sph_kecil');
    Route::get('loan/sph/besar/{no}/{agunanId?}', [LoanController::class, 'cetakSPHBesar'])
        ->where('no', '[A-Z]{2}[0-9]{6,}')->whereNumber('agunanId')
        ->name('admin.loan.cetak_sph_besar');
    Route::get('loan/sph/pdf/{no}/{agunanId?}/{size?}', [LoanController::class, 'cetakSPHPDF'])
        ->where('no', '[A-Z]{2}[0-9]{6,}')->whereNumber('agunanId')
        ->name('admin.loan.cetak_sph_pdf');
});
