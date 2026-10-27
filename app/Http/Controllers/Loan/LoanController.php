<?php

namespace App\Http\Controllers\Loan;

use App\Http\Controllers\BaseAdminController;
use App\Http\Requests\LoanApprovalRequest;
use App\Http\Requests\LoanRealisasiRequest;
use App\Services\ExcelService;
use App\Services\LoanService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


/**
 * LoanController — pengganti `Pinjaman.php` CI3 (2.052 baris, 134 method).
 *
 * Batch 10 (Laravel Fase 6 P1): Master pinjaman + form pengajuan PP/Jatem/RSJatem/RSbyReq
 *   + JSON endpoint data peminjam + approval flow dasar (BKM/BKK/BI).
 *
 * Batch 11–13 menyusul: tagihan, angsuran kolektif, rescheduling, laporan agunan,
 *   realisasi + pelunasan, resheduling, view pembayaran, doRealisasi, dll.
 */
class LoanController extends BaseAdminController
{
    // ===== Master Pinjaman =====

    /**
     * Form master pinjaman UKM
     */
    public function pinjamUkm()
    {
        $dataPeminjam = LoanService::loadDataPeminjam('ukm');
        return $this->view('admin.loans.form_pinjaman_ukm', [
            'pinjaman' => $dataPeminjam,
        ]);
    }

    /**
     * Form master pinjaman Mikro
     */
    public function pinjamMikro()
    {
        $dataPeminjam = LoanService::loadDataPeminjam('mikro');
        return $this->view('admin.loans.form_pinjaman_mikro', [
            'pinjaman' => $dataPeminjam,
        ]);
    }

    /**
     * Form master pinjaman Jaminan
     */
    public function pinjamJaminan()
    {
        $dataPeminjam = LoanService::loadDataPeminjam('jaminan');
        return $this->view('admin.loans.form_pinjaman_jaminan', [
            'pinjaman' => $dataPeminjam,
        ]);
    }

    /**
     * PJ Pinjaman (dashboard Pinjaman)
     */
    public function pjPinjaman()
    {
        $rows = LoanService::getDataPinjamanAllStatus();
        return $this->view('admin.loans.view_pj_pinjaman_home', ['rows' => $rows]);
    }

    // ===== Form Pengajuan (Batch 10) =====

    /**
     * Form pengajuan Pinjaman Payroll (PP)
     */
    public function pengajuanPP()
    {
        $idPP = request('idPP');
        $dataPeminjam = $idPP ? LoanService::detailPinjamanMG($idPP) : null;
        $noCredit = $idPP && $dataPeminjam
            ? (LoanService::getNoCredit($idPP, $dataPeminjam, 'PP') ?? [])
            : null;

        return $this->view('admin.loans.form_pengajuan_pinjaman_pp_pj', [
            'dataPeminjam'  => $dataPeminjam,
            'statData'      => 'Insert',
            'noCredit'      => $noCredit['noCredit'] ?? '',
        ]);
    }

    /**
     * Form pengajuan Jatuh Tempo (Jatem)
     */
    public function pengajuanJatem()
    {
        return $this->view('admin.loans.form_pengajuan_pinjaman_jatem_pj');
    }

    /**
     * Form pengajuan Rescheduling Jatuh Tempo
     */
    public function pengajuanRSJatem()
    {
        return $this->view('admin.loans.form_pengajuan_pinjaman_rescheduling_jt_pj');
    }

    /**
     * Form pengajuan Rescheduling by Request
     */
    public function pengajuanRSbyReq()
    {
        return $this->view('admin.loans.form_pengajuan_pinjaman_rescheduling_request_pj');
    }

    // ===== JSON Endpoint — Lookup (Batch 10) =====

    /**
     * Data anggota pinjaman (semua)
     */
    public function getDataAnggotaPinjaman()
    {
        $rows = LoanService::getDataAnggotaPinjaman();
        return response()->json(['status' => 200, 'data' => $rows]);
    }

    /**
     * Data anggota pinjaman untuk Buku Besar
     */
    public function getDataAnggotaPinjamanBukuBesar()
    {
        $rows = LoanService::getDataAnggotaPinjamanBukuBesar();
        return response()->json(['status' => 200, 'data' => $rows]);
    }

    /**
     * Data pinjaman per status
     */
    public function getDataPinjamanAllStatus()
    {
        $rows = LoanService::getDataPinjamanAllStatus();
        return response()->json(['status' => 200, 'data' => $rows]);
    }

    /**
     * Transaksi pinjaman hari ini
     */
    public function getTransactionTodayPinjaman()
    {
        $rows = LoanService::getTransactionTodayPinjaman();
        return response()->json(['status' => 200, 'data' => $rows]);
    }

    /**
     * Cek angsuran per peminjam
     */
    public function checkAngsuran()
    {
        $idAgt = (string) request('idAgt', '');
        $result = LoanService::checkAngsuran($idAgt);
        return response()->json(['status' => $result ? 200 : 404, 'data' => $result]);
    }

    /**
     * Detail angsuran per nomor kredit (rincian + detail)
     */
    public function detailPinjamanById()
    {
        $noCredit = (string) request('noCredit', '');
        $detail = LoanService::detailAngsuran($noCredit);
        $rincian = LoanService::rincianAngsuran($noCredit);
        return response()->json(['status' => 200, 'detail' => $detail, 'angsuran' => $rincian]);
    }

    /**
     * Cek tagihan jatuh tempo hari ini
     */
    public function loadDataTagihan()
    {
        $rows = LoanService::loadDataTagihan();
        return response()->json(['status' => 200, 'data' => $rows]);
    }

    // ===== Approval Flow (Batch 10) =====

    /**
     * Form persetujuan transaksi pinjaman
     */
    public function persetujuanTrans()
    {
        $rows = LoanService::getTransactionTodayApproval();
        return $this->view('admin.loans.view_persetujuan_transaksi_pinjaman', [
            'rows' => $rows,
        ]);
    }

    /**
     * Proses approval transaksi pinjaman
     */
    public function prosesApprovalTrans(LoanApprovalRequest $request)
    {
        $transId  = (int) $request->input('transId', 0);
        $chbApprove = (int) $request->input('chbApproval', 0);
        $username = $this->userName ?? 'SYSTEM';
        $ok = LoanService::approvalTrans($chbApprove, $transId, 'TPJ', $username);
        return redirect()->route('admin.loan.persetujuan_trans')
            ->with($ok ? 'success' : 'error', $ok ? "Transaksi {$transId} berhasil di-approve." : 'Gagal approve.');
    }

    /**
     * Cetak BKM Pinjaman
     */
    public function viewBKM()
    {
        $transID = request('transID');
        $row = $transID ? LoanService::viewBKM((int) $transID) : null;
        return $this->view('admin.loans.cetak_BKM_pinjaman', ['row' => $row]);
    }

    /**
     * Cetak BKK Pinjaman
     */
    public function viewBKK()
    {
        $transID = request('transID');
        $row = $transID ? LoanService::viewBKK((int) $transID) : null;
        return $this->view('admin.loans.cetak_BKK_pinjaman', ['row' => $row]);
    }

    /**
     * Cetak BI Pinjaman
     */
    public function viewBI()
    {
        $transID = request('transID');
        $row = $transID ? LoanService::viewBI((int) $transID) : null;
        return $this->view('admin.loans.cetak_BU_pinjaman', ['row' => $row]);
    }

    // ===== Batch 11: Tagihan, Angsuran Kolektif, Rescheduling =====

    /**
     * Daftar tagihan jatuh tempo
     */
    public function tagihanJatem()
    {
        $dateJatem = request('date');
        $rows = LoanService::loadTagihanJatem($dateJatem);
        return $this->view('admin.loans.view_tagihan_jatem', ['rows' => $rows]);
    }

    /**
     * Form tagihan kolektif (pembayaran angsuran batch)
     */
    public function angsuranKolektif()
    {
        $rows = LoanService::loadAngsuranKolektif();
        return $this->view('admin.loans.form_angsuran_kolektif', ['rows' => $rows]);
    }

    /**
     * Proses pembayaran angsuran kolektif
     */
    public function prosesAngsuranKolektif()
    {
        $transIds = request('transIds', []);
        $username = $this->userName ?? 'SYSTEM';
        $count = LoanService::doBayarAngsuranKolektif($transIds, $username);

        return redirect()->route('admin.loan.angsuran_kolektif')
            ->with('success', "{$count} angsuran berhasil dibayar.");
    }

    /**
     * Form rescheduling jatuh tempo
     */
    public function reschedulingJatem()
    {
        $rows = LoanService::loadReschedulingJatem();
        return $this->view('admin.loans.form_rescheduling_jatem', ['rows' => $rows]);
    }

    /**
     * Proses rescheduling jatuh tempo
     */
    public function prosesReschedulingJatem()
    {
        $loanAccountNo = request('loanAccountNo', '');
        $newTotal = (int) request('newInstallmentTotal', 0);
        $username  = $this->userName ?? 'SYSTEM';

        $ok = LoanService::doReschedulingJatem($loanAccountNo, $newTotal, $username);
        return redirect()->route('admin.loan.rescheduling_jatem')
            ->with($ok ? 'success' : 'error', $ok ? "Rescheduling {$loanAccountNo} berhasil." : 'Gagal.');
    }

    /**
     * Form rescheduling by request
     */
    public function reschedulingByReq()
    {
        $rows = LoanService::loadReschedulingJatem();
        return $this->view('admin.loans.form_rescheduling_request', ['rows' => $rows]);
    }

    /**
     * Proses rescheduling by request
     */
    public function prosesReschedulingByReq()
    {
        $loanAccountNo = request('loanAccountNo', '');
        $requestDate   = request('requestDate');
        $username      = $this->userName ?? 'SYSTEM';

        $ok = LoanService::doReschedulingByReq($loanAccountNo, $requestDate, $username);
        return redirect()->route('admin.loan.rescheduling_by_req')
            ->with($ok ? 'success' : 'error', $ok ? "Rescheduling {$loanAccountNo} berhasil." : 'Gagal.');
    }

    /**
     * Detail rescheduling per loan account
     */
    public function detailRescheduling()
    {
        $loanAccountNo = request('loanAccountNo', '');
        $detail = LoanService::detailRescheduling($loanAccountNo);
        return response()->json(['status' => $detail ? 200 : 404, 'data' => $detail]);
    }

    /**
     * Data tagihan kolektif (JSON)
     */
    public function dataTagihanKolektif()
    {
        $rows = LoanService::loadDataTagihanKolektif();
        return response()->json(['status' => 200, 'data' => $rows]);
    }

    // ===== Batch 12: Laporan Agunan, Realisasi, Pelunasan, Resheduling =====

    /**
     * Laporan agunan per jenis
     */
    public function laporanAgunan()
    {
        $jenis = request('jenis', 'BPKB');
        $rows = LoanService::loadLaporanAgunan($jenis);
        return $this->view('admin.loans.lap_agunan', [
            'rows'   => $rows,
            'jenis'  => $jenis,
        ]);
    }

    /**
     * Laporan realisasi pinjaman
     */
    public function laporanRealisasi()
    {
        $rows = LoanService::loadLaporanRealisasi();
        return $this->view('admin.loans.lap_realisasi', ['rows' => $rows]);
    }

    /**
     * Laporan pelunasan
     */
    public function laporanPelunasan()
    {
        $rows = LoanService::loadLaporanPelunasan();
        return $this->view('admin.loans.lap_pelunasan', ['rows' => $rows]);
    }

    /**
     * Laporan resheduling
     */
    public function laporanResheduling()
    {
        $rows = LoanService::loadLaporanResheduling();
        return $this->view('admin.loans.lap_resheduling', ['rows' => $rows]);
    }

    /**
     * Laporan angsuran per periode
     */
    public function laporanAngsuran()
    {
        $start = request('start', today()->startOfMonth()->toDateString());
        $end   = request('end',   today()->toDateString());
        $rows = LoanService::loadLaporanAngsuran($start, $end);
        return $this->view('admin.loans.lap_angsuran', [
            'rows'  => $rows,
            'start' => $start,
            'end'   => $end,
        ]);
    }

    /**
     * Proses realisasi pinjaman
     */
    public function prosesRealisasi(LoanRealisasiRequest $request)
    {
        $loanAccountNo = $request->input('loanAccountNo', '');
        $amount        = (float) $request->input('amount', 0);
        $username      = $this->userName ?? 'SYSTEM';

        $transId = LoanService::doRealisasi($loanAccountNo, $amount, $username);
        return redirect()->route('admin.loan.laporan_realisasi')
            ->with('success', "Realisasi transaksi #{$transId} berhasil dicatat.");
    }

    /**
     * Proses pelunasan
     */
    public function prosesPelunasan()
    {
        $loanAccountNo = request('loanAccountNo', '');
        $username      = $this->userName ?? 'SYSTEM';

        $ok = LoanService::doPelunasan($loanAccountNo, $username);
        return redirect()->route('admin.loan.laporan_pelunasan')
            ->with($ok ? 'success' : 'error', $ok ? "Pelunasan {$loanAccountNo} berhasil." : 'Gagal.');
    }

    /**
     * Form realisasi pinjaman
     */
    public function formRealisasi()
    {
        return $this->view('admin.loans.form_realisasi');
    }

    // ===== Batch 13: View Payment, Approval Multi-Level, Tunggakan =====

    /**
     * View pembayaran pinjaman (hari ini)
     */
    public function viewPayment()
    {
        $data = LoanService::loadViewPayment();
        return $this->view('admin.loans.view_payment', $data);
    }

    /**
     * Form approval multi-level
     */
    public function approvalMultiLevel()
    {
        $level = request('level', 'ao3');
        $rows = LoanService::loadPendingApproval($level);
        return $this->view('admin.loans.view_approval_multi_level', [
            'rows'  => $rows,
            'level' => $level,
        ]);
    }

    /**
     * Proses approval multi-level
     */
    public function prosesApprovalMultiLevel(LoanApprovalRequest $request)
    {
        $loanAccountNo = $request->input('loanAccountNo', '');
        $level         = $request->input('level', 'ao3');
        $approve       = (int) $request->input('approve', 1);
        $username      = $this->userName ?? 'SYSTEM';

        $ok = LoanService::approvalMultiLevel($loanAccountNo, $level, $approve, $username);
        return redirect()->route('admin.loan.approval_multi_level', ['level' => $level])
            ->with($ok ? 'success' : 'error', $ok ? "Approval {$level} {$loanAccountNo} berhasil." : 'Gagal.');
    }

    /**
     * Laporan tunggakan
     */
    public function laporanTunggakan()
    {
        $rows = LoanService::doTunggakan();
        return $this->view('admin.loans.lap_tunggakan', ['rows' => $rows]);
    }

    // ===== Batch 14: Cetak PDF & Export Excel (Fase 7) =====

    /**
     * Cetak BKM Pinjaman (PDF)
     */
    public function cetakBKM()
    {
        $transID = request('transID');
        $row = $transID ? LoanService::viewBKM((int) $transID) : null;
        if (! $row) {
            return redirect()->route('admin.loan.cetak_bkm')->with('error', 'Data tidak ditemukan.');
        }
        return PdfService::generate('pdf.loans.bkm', ['row' => $row], 'BKM_Pinjaman_' . ($row['TRANS_ID'] ?? $transID));
    }

    /**
     * Cetak BKK Pinjaman (PDF)
     */
    public function cetakBKK()
    {
        $transID = request('transID');
        $row = $transID ? LoanService::viewBKK((int) $transID) : null;
        if (! $row) {
            return redirect()->route('admin.loan.cetak_bkk')->with('error', 'Data tidak ditemukan.');
        }
        return PdfService::generate('pdf.loans.bkk', ['row' => $row], 'BKK_Pinjaman_' . ($row['TRANS_ID'] ?? $transID));
    }

    /**
     * Cetak BU Pinjaman (PDF)
     */
    public function cetakBU()
    {
        $transID = request('transID');
        $row = $transID ? LoanService::viewBI((int) $transID) : null;
        if (! $row) {
            return redirect()->route('admin.loan.cetak_bu')->with('error', 'Data tidak ditemukan.');
        }
        return PdfService::generate('pdf.loans.bu', ['row' => $row], 'BU_Pinjaman_' . ($row['TRANS_ID'] ?? $transID));
    }

    /**
     * Export laporan pinjaman ke Excel
     */
    public function exportExcelLaporanPinjaman()
    {
        $rows = LoanService::getDataPinjamanAllStatus();
        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'No. Anggota'   => $r['MEMBER_NO'] ?? '',
                'Nama'         => $r['MEMBER_NAME'] ?? '',
                'No. Kredit'   => $r['LOAN_ACCOUNT_NO'] ?? '',
                'Produk'       => $r['LOAN_PRODUCT_ID'] ?? '',
                'Pokok'        => (float) ($r['LOAN_AMOUNT'] ?? 0),
                'Sisa'         => (float) ($r['REMAINING_AMOUNT'] ?? 0),
                'Status'       => $r['LOAN_STATUS_ID'] ?? '',
            ];
        }
        $resp = ExcelService::exportRows($data ?: [['No. Anggota' => '-', 'Nama' => '-', 'No. Kredit' => '-', 'Produk' => '-', 'Pokok' => 0, 'Sisa' => 0, 'Status' => '-']], 'laporan_pinjaman');
        return $resp;
    }

    // ===== Batch B1: Pengajuan Pinjaman Multiguna (MG) =====

    /**
     * Form pengajuan pinjaman MG — Emas
     * @param string $noAgt (query param) MEMBER_NO yang dicari
     */
    public function pengajuanMGEmas()
    {
        $noAgt   = request('noAgt', '');
        $dataAgt = $noAgt ? LoanService::detailPeminjamMG($noAgt) : null;
        $countLoan = 0;

        if ($dataAgt) {
            $noCredit  = LoanService::getNoCreditMG($dataAgt['MEMBER_REGISTRY'], $countLoan, 'Emas');
            $jasa      = LoanService::getJasaMG($dataAgt['MEMBER_REGISTRY'], 'Emas');
            $provisi   = LoanService::getProvisiMG($dataAgt['MEMBER_REGISTRY'], 'Emas');
        } else {
            $noCredit = ['noCredit' => ''];
            $jasa     = [];
            $provisi  = [];
        }

        return $this->view('admin.loans.form_pengajuan_mg_emas', [
            'dataAgt' => $dataAgt,
            'noCredit' => $noCredit,
            'jasa'   => $jasa,
            'provisi' => $provisi,
            'type'   => 'Emas',
            'noAgt'  => $noAgt,
        ]);
    }

    /**
     * Form pengajuan pinjaman MG — SimHar
     */
    public function pengajuanMGSimhar()
    {
        $noAgt   = request('noAgt', '');
        $dataAgt = $noAgt ? LoanService::detailPeminjamMG($noAgt) : null;
        $countLoan = 0;

        if ($dataAgt) {
            $noCredit = LoanService::getNoCreditMG($dataAgt['MEMBER_REGISTRY'], $countLoan, 'Simhar');
            $jasa     = LoanService::getJasaMG($dataAgt['MEMBER_REGISTRY'], 'Simhar');
            $provisi  = LoanService::getProvisiMG($dataAgt['MEMBER_REGISTRY'], 'Simhar');
        } else {
            $noCredit = ['noCredit' => ''];
            $jasa     = [];
            $provisi  = [];
        }

        return $this->view('admin.loans.form_pengajuan_mg_simhar', [
            'dataAgt' => $dataAgt,
            'noCredit' => $noCredit,
            'jasa'   => $jasa,
            'provisi' => $provisi,
            'type'   => 'Simhar',
            'noAgt'  => $noAgt,
        ]);
    }

    /**
     * Form pengajuan pinjaman MG — Sertifikat (Potong Gaji)
     */
    public function pengajuanMGPotongGaji()
    {
        $noAgt   = request('noAgt', '');
        $dataAgt = $noAgt ? LoanService::detailPeminjamMG($noAgt) : null;
        $countLoan = 0;

        if ($dataAgt) {
            $noCredit = LoanService::getNoCreditMG($dataAgt['MEMBER_REGISTRY'], $countLoan, 'PotongGaji');
            $jasa     = LoanService::getJasaMG($dataAgt['MEMBER_REGISTRY'], 'PotongGaji');
            $provisi  = LoanService::getProvisiMG($dataAgt['MEMBER_REGISTRY'], 'PP');
        } else {
            $noCredit = ['noCredit' => ''];
            $jasa     = [];
            $provisi  = [];
        }

        return $this->view('admin.loans.form_pengajuan_mg_potong_gaji', [
            'dataAgt' => $dataAgt,
            'noCredit' => $noCredit,
            'jasa'   => $jasa,
            'provisi' => $provisi,
            'type'   => 'PotongGaji',
            'noAgt'  => $noAgt,
        ]);
    }

    /**
     * Proses simpan pengajuan pinjaman MG.
     */
    public function prosesPengajuanMG()
    {
        $registry = request('memberRegistry');
        $data = [
            'LOAN_AMOUNT'  => (float) request('LOAN_AMOUNT', 0),
            'LOAN_PERIOD' => (int) request('LOAN_PERIOD', 0),
            'NO_CREDIT'  => request('NO_CREDIT', ''),
        ];
        $username = $this->userName ?? 'SYSTEM';

        $ok = LoanService::savePengajuanMG($registry, $data, $username);

        return redirect()->route('admin.loan.pj_pinjaman')
            ->with($ok ? 'success' : 'error', $ok
                ? 'Pengajuan pinjaman MG berhasil disimpan.'
                : 'Gagal menyimpan pengajuan.');
    }

    /**
     * B1: Detail MG Serti — form + riwayat angsuran
     * @param string $no LOAN_ACCOUNT_NO
     */
    public function mgSerti(string $no)
    {
        $account = DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->where('mla.LOAN_ACCOUNT_NO', $no)
            ->select('mla.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->first();

        $installments = DB::table('mu_trans_installment as ti')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'ti.MEMBER_REGISTRY')
            ->where('ti.LOAN_ACCOUNT_NO', $no)
            ->select('ti.*', 'm.MEMBER_NAME')
            ->orderByDesc('ti.DUE_DATE')
            ->orderByDesc('ti.TRANS_ID')
            ->get();

        return $this->view('admin.loans.mg_serti', compact('no', 'account', 'installments'));
    }

    /**
     * B1: Detail MG Simhar
     */
    public function mgSimhar(string $no)
    {
        $account = DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->where('mla.LOAN_ACCOUNT_NO', $no)
            ->select('mla.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->first();

        $installments = DB::table('mu_trans_installment as ti')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'ti.MEMBER_REGISTRY')
            ->where('ti.LOAN_ACCOUNT_NO', $no)
            ->select('ti.*', 'm.MEMBER_NAME')
            ->orderByDesc('ti.DUE_DATE')
            ->orderByDesc('ti.TRANS_ID')
            ->get();

        return $this->view('admin.loans.mg_simhar', compact('no', 'account', 'installments'));
    }

    /**
     * B1: Detail MG Potong Gaji
     */
    public function mgPotong(string $no)
    {
        $account = DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->where('mla.LOAN_ACCOUNT_NO', $no)
            ->select('mla.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->first();

        $installments = DB::table('mu_trans_installment as ti')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'ti.MEMBER_REGISTRY')
            ->where('ti.LOAN_ACCOUNT_NO', $no)
            ->select('ti.*', 'm.MEMBER_NAME')
            ->orderByDesc('ti.DUE_DATE')
            ->orderByDesc('ti.TRANS_ID')
            ->get();

        return $this->view('admin.loans.mg_potong', compact('no', 'account', 'installments'));
    }

    /**
     * B2: Form input Survey
     * Survei disimpan ke mu_survey (FK: MEMBER_REGISTRY + LOAN_ID)
     * File upload disimpan ke ma_images (IMAGE_TYPE='SURVEY' / 'AGUNAN')
     */
    public function formSurvey(string $no)
    {
        // Ambil account + member
        $account = DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->where('mla.LOAN_ACCOUNT_NO', $no)
            ->select('mla.*', 'm.MEMBER_NAME', 'm.MEMBER_NO', 'm.MEMBER_EMAIL')
            ->first();

        if (!$account) {
            abort(404, 'Akun pinjaman tidak ditemukan.');
        }

        // Ambil LOAN_ID dari mu_loan (via MEMBER_REGISTRY)
        $loan = DB::table('mu_loan')
            ->where('MEMBER_REGISTRY', $account->MEMBER_REGISTRY)
            ->orderByDesc('LOAN_ID')
            ->first();

        // Ambil data survey yang sudah ada
        $survey = null;
        if ($loan) {
            $survey = DB::table('mu_survey')
                ->where('LOAN_ID', $loan->LOAN_ID)
                ->orderByDesc('SURVEY_ID')
                ->first();
        }

        // Ambil file survey + agunan yang sudah ada
        $imgSurvey = DB::table('ma_images')
            ->where('USER_ID', $account->MEMBER_REGISTRY)
            ->where('IMAGE_TYPE', 'SURVEY')
            ->latest('IMAGE_ID')
            ->first();
        $imgAgunan = DB::table('ma_images')
            ->where('USER_ID', $account->MEMBER_REGISTRY)
            ->where('IMAGE_TYPE', 'AGUNAN')
            ->latest('IMAGE_ID')
            ->first();

        return $this->view('admin.loans.survey_form', compact(
            'no', 'account', 'loan', 'survey', 'imgSurvey', 'imgAgunan'
        ));
    }

    /**
     * B2: Proses submit survey — simpan metadata ke mu_survey + file ke ma_images
     */
    public function prosesSurvey(Request $request)
    {
        $data = $request->validate([
            'loan_account_no' => 'required|string|max:50',
            'survey_result'   => 'nullable|string|max:255',
            'surveyor'        => 'nullable|string|max:100',
            'file_survey_01'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_survey_02'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [], [
            'loan_account_no' => 'No. Akun Pinjaman',
            'survey_result'   => 'Hasil Survey',
            'surveyor'        => 'Surveyor',
        ]);

        $username  = $this->userName ?? 'SYSTEM';
        $noAgt    = $data['loan_account_no'];

        // Ambil MEMBER_REGISTRY dari mu_loan_account
        $account = DB::table('mu_loan_account')
            ->where('LOAN_ACCOUNT_NO', $noAgt)
            ->first();
        if (!$account) {
            return redirect()->route('admin.loan.form_survey', ['no' => $noAgt])
                ->with('error', 'Akun tidak ditemukan.');
        }
        $memberRegistry = $account->MEMBER_REGISTRY;

        // Ambil / buat LOAN_ID
        $loan = DB::table('mu_loan')
            ->where('MEMBER_REGISTRY', $memberRegistry)
            ->orderByDesc('LOAN_ID')
            ->first();

        // ---- Simpan file survey (IMAGE_TYPE='SURVEY') ----
        if ($request->hasFile('file_survey_01')) {
            $path = $request->file('file_survey_01')->store('surveys', 'public');
            // Hapus file lama
            $old = DB::table('ma_images')->where('USER_ID', $memberRegistry)->where('IMAGE_TYPE', 'SURVEY')->latest('IMAGE_ID')->first();
            if ($old) DB::table('ma_images')->where('IMAGE_ID', $old->IMAGE_ID)->delete();
            DB::table('ma_images')->insert([
                'USER_ID'    => $memberRegistry,
                'IMAGE_TYPE' => 'SURVEY',
                'IMAGE_FILE' => $path,
                'LOGTIME'    => now(),
            ]);
        }

        // ---- Simpan file agunan (IMAGE_TYPE='AGUNAN') ----
        if ($request->hasFile('file_survey_02')) {
            $path = $request->file('file_survey_02')->store('agungans', 'public');
            $old = DB::table('ma_images')->where('USER_ID', $memberRegistry)->where('IMAGE_TYPE', 'AGUNAN')->latest('IMAGE_ID')->first();
            if ($old) DB::table('ma_images')->where('IMAGE_ID', $old->IMAGE_ID)->delete();
            DB::table('ma_images')->insert([
                'USER_ID'    => $memberRegistry,
                'IMAGE_TYPE' => 'AGUNAN',
                'IMAGE_FILE' => $path,
                'LOGTIME'    => now(),
            ]);
        }

        // ---- Simpan metadata survey ke mu_survey ----
        if ($loan) {
            $surveyData = [
                'MEMBER_REGISTRY' => $memberRegistry,
                'LOAN_ID'         => $loan->LOAN_ID,
                'SURVEY_DATE'     => now()->toDateString(),
                'SURVEY_RESULT'   => $data['survey_result'] ?? '',
                'SURVEYOR'        => $data['surveyor'] ?? $username,
                'LOGTIME'         => now(),
            ];

            $existing = DB::table('mu_survey')->where('LOAN_ID', $loan->LOAN_ID)->first();
            if ($existing) {
                DB::table('mu_survey')->where('SURVEY_ID', $existing->SURVEY_ID)->update($surveyData);
            } else {
                DB::table('mu_survey')->insert($surveyData);
            }
        }

        return redirect()->route('admin.loan.form_survey', ['no' => $noAgt])
            ->with('success', 'Data survey berhasil disimpan.');
    }

    /**
     * B3: Form Pelunasan Detail
     */
    public function pelunasan(string $no)
    {
        $account = DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->where('mla.LOAN_ACCOUNT_NO', $no)
            ->select('mla.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->first();

        if (!$account) {
            abort(404, 'Akun pinjaman tidak ditemukan.');
        }

        // Sudah lunas?
        if ($account->REMAINING_AMOUNT == 0 || $account->LOAN_STATUS_ID == 6) {
            return redirect()->route('admin.loan.laporan_pelunasan')
                ->with('info', "Akun {$no} sudah status Pelunas.");
        }

        $totalAngsuran = (float) DB::table('mu_trans_installment')
            ->where('LOAN_ACCOUNT_NO', $no)
            ->where('STATUS_ID', 1)
            ->sum('AMOUNT');

        $sisa = max(0, $account->LOAN_AMOUNT - $totalAngsuran);

        return $this->view('admin.loans.pelunasan', compact('no', 'account', 'totalAngsuran', 'sisa'));
    }

    /**
     * B3: Proses Pelunasan (update mu_loan_account REMAINING_AMOUNT = 0, LOAN_STATUS_ID = 6)
     */
    public function prosesPelunasanDetail(Request $request)
    {
        $data = $request->validate([
            'loan_account_no' => 'required|string|max:50',
            'note'            => 'nullable|string|max:500',
        ], [], [
            'loan_account_no' => 'No. Akun Pinjaman',
        ]);

        $username = $this->userName ?? 'SYSTEM';
        $no       = $data['loan_account_no'];

        $ok = LoanService::doPelunasan($no, $username);

        return redirect()->route('admin.loan.laporan_pelunasan')
            ->with($ok ? 'success' : 'error', $ok
                ? "Pelunasan {$no} berhasil diproses."
                : 'Gagal memproses pelunasan.');
    }

    /**
     * B4: Form Agunan (upload foto agunan — disimpan di ma_images IMAGE_TYPE='AGUNAN')
     */
    public function agunanForm(string $no)
    {
        $account = DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->where('mla.LOAN_ACCOUNT_NO', $no)
            ->select('mla.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->first();

        // Ambil data survey + foto agunan dari ma_images
        $survey    = null;
        $imgAgunan = null;
        if ($account) {
            $loan = DB::table('mu_loan')
                ->where('MEMBER_REGISTRY', $account->MEMBER_REGISTRY)
                ->orderByDesc('LOAN_ID')
                ->first();

            if ($loan) {
                $survey = DB::table('mu_survey')
                    ->where('LOAN_ID', $loan->LOAN_ID)
                    ->orderByDesc('SURVEY_ID')
                    ->first();
            }

            $imgAgunan = DB::table('ma_images')
                ->where('USER_ID', $account->MEMBER_REGISTRY)
                ->where('IMAGE_TYPE', 'AGUNAN')
                ->latest('IMAGE_ID')
                ->first();
        }

        return $this->view('admin.loans.agunan', compact('no', 'account', 'survey', 'imgAgunan'));
    }

    /**
     * B4: Proses upload agunan — simpan ke ma_images (IMAGE_TYPE='AGUNAN')
     */
    public function prosesAgunan(Request $request)
    {
        $data = $request->validate([
            'loan_account_no' => 'required|string|max:50',
            'foto_agunan'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [], [
            'loan_account_no' => 'No. Akun Pinjaman',
            'foto_agunan'     => 'Foto Agunan',
        ]);

        $username = $this->userName ?? 'SYSTEM';
        $no       = $data['loan_account_no'];

        $account = DB::table('mu_loan_account')->where('LOAN_ACCOUNT_NO', $no)->first();
        if (!$account) {
            return redirect()->route('admin.loan.agunan', ['no' => $no])
                ->with('error', 'Akun pinjaman tidak ditemukan.');
        }

        if ($request->hasFile('foto_agunan')) {
            $path = $request->file('foto_agunan')->store('agungans', 'public');

            // Hapus gambar agunan lama
            $old = DB::table('ma_images')
                ->where('USER_ID', $account->MEMBER_REGISTRY)
                ->where('IMAGE_TYPE', 'AGUNAN')
                ->latest('IMAGE_ID')
                ->first();
            if ($old) {
                DB::table('ma_images')->where('IMAGE_ID', $old->IMAGE_ID)->delete();
            }

            DB::table('ma_images')->insert([
                'USER_ID'    => $account->MEMBER_REGISTRY,
                'IMAGE_TYPE' => 'AGUNAN',
                'IMAGE_FILE' => $path,
                'LOGTIME'    => now(),
            ]);

            return redirect()->route('admin.loan.agunan', ['no' => $no])
                ->with('success', 'Foto agunan tersimpan.');
        }

        return redirect()->route('admin.loan.agunan', ['no' => $no])
            ->with('info', 'Pilih file foto agunan terlebih dahulu.');
    }

    /**
     * B5: Log Aksi Operator & PJ (mu_history_loan)
     * Catatan: kolom FK bisa LOAN_ACCOUNT_NO (baru) atau LOAN_ACCOUNT_ID (CI3 lama).
     * Query memakai COALESCE agar aman.
     */
    public function logAO(string $no)
    {
        $account = DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->where('mla.LOAN_ACCOUNT_NO', $no)
            ->select('mla.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->first();

        // Coba dua nama kolom (kompatibel CI3 & Laravel baru)
        try {
            $logs = DB::table('mu_history_loan as mlh')
                ->leftJoin('mu_loan_tracing_status as mlts',
                    'mlts.LOAN_TRACING_STATUS_ID', '=', 'mlh.LOAN_TRACING_STATUS_ID_BEFORE')
                ->where('mlh.LOAN_ACCOUNT_NO', $no)
                ->select('mlh.*', 'mlts.LOAN_TRACING_STATUS_NAME as STATUS_BEFORE')
                ->orderByDesc(DB::raw('mlh.ID'))
                ->get();
        } catch (\Illuminate\Database\QueryException) {
            $logs = DB::table('mu_history_loan as mlh')
                ->leftJoin('mu_loan_tracing_status as mlts',
                    'mlts.LOAN_TRACING_STATUS_ID', '=', 'mlh.LOAN_TRACING_STATUS_ID_BEFORE')
                ->where('mlh.LOAN_ACCOUNT_ID', $no)
                ->select('mlh.*', 'mlts.LOAN_TRACING_STATUS_NAME as STATUS_BEFORE')
                ->orderByDesc(DB::raw('mlh.ID'))
                ->get();
        }

        return $this->view('admin.loans.log_ao', compact('no', 'account', 'logs'));
    }

    /**
     * B6: Laporan SPH — daftar peminjam status > 6 (pelunas & rescheduling)
     */
    public function laporanSPH()
    {
        $rows = LoanService::loadLaporanSPH();
        return $this->view('admin.loans.lap_sph', compact('rows'));
    }

    /**
     * B6: Cetak SPH Kecil
     * @param string $no        LOAN_ACCOUNT_NO
     * @param int    $agunanId  LOAN_COLLATERAL_ID (opsional, default 0 = semua)
     */
    public function cetakSPHKecil(string $no, int $agunanId = 0)
    {
        $data = LoanService::detailSPH($no, $agunanId);
        return $this->view('admin.loans.cetak_sph_kecil', compact('data'));
    }

    /**
     * B6: Cetak SPH Besar
     */
    public function cetakSPHBesar(string $no, int $agunanId = 0)
    {
        $data = LoanService::detailSPH($no, $agunanId);
        return $this->view('admin.loans.cetak_sph_besar', compact('data'));
    }

    /**
     * B6: Cetak PDF SPH Kecil (via dompdf/dompdf)
     */
    public function cetakSPHPDF(string $no, int $agunanId = 0, string $size = 'kecil')
    {
        $data = LoanService::detailSPH($no, $agunanId);
        if (!$data) {
            abort(404, 'Data peminjam SPH tidak ditemukan.');
        }

        $view = $size === 'besar'
            ? 'admin.loans.cetak_sph_besar'
            : 'admin.loans.cetak_sph_kecil';

        return \App\Services\PdfService::generate($view, compact('data'), "SPH_{$no}_{$size}");
    }
}
