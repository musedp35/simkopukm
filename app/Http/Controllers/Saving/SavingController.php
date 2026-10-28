<?php

namespace App\Http\Controllers\Saving;

use App\Http\Controllers\BaseAdminController;
use App\Services\ExcelService;
use App\Services\PdfService;
use App\Services\SavingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * SavingController
 *
 * Pengganti controller `Simpanan.php` CI3 (2.831 baris).
 * Batch 1: master simpanan (Harian, NTR, SetiaPlus, SSB) + cek saldo + tutup rekening.
 */
class SavingController extends BaseAdminController
{
    // ===== Master Simpanan Harian =====

    /**
     * Form master simpanan harian (pembukaan akun)
     */
    public function harian()
    {
        $jasa = SavingsService::loadJasa();
        $noKtp = request('idHiddenSimHar');
        $data = ['jasa' => $jasa, 'NO_ACC' => '', 'detailSimHar' => null, 'memberRegistry' => ''];

        if ($noKtp) {
            $cekNoUrut = SavingsService::checkNoUrutSimpanan($noKtp);
            $idAgt = SavingsService::cekIdAgt($noKtp);
            if ($cekNoUrut) {
                $noAcc = SavingsService::loadAccNumber($idAgt['MEMBER_REGISTRY'] ?? '');
                $data['NO_ACC'] = $noAcc['noAcc'] ?? '';
            }
            $data['detailSimHar'] = SavingsService::detailSimHar($noKtp);
            $data['memberRegistry'] = $idAgt['MEMBER_REGISTRY'] ?? '';
        }

        return $this->view('admin.savings.form_master_simpanan_harian', $data);
    }

    /**
     * Simpan master simpanan harian (buka / update akun)
     */
    public function storeSimHar()
    {
        $request = request();
        $noAcc   = $request->input('edtNoAcc');
        $member  = $request->input('memberRegistry', '');
        $active  = (int) $request->input('chbStatus', 1);
        $balance = (float) $request->input('blokBalance', 0);

        $ok = SavingsService::addMasterSimpananHarian($noAcc, $member, $active, $balance, $this->userName ?? 'SYSTEM');

        return redirect()->route('admin.savings.harian')
            ->with($ok ? 'success' : 'error', $ok
                ? "Akun simpanan harian {$noAcc} berhasil disimpan."
                : "Gagal menyimpan akun simpanan harian {$noAcc}.");
    }

    // ===== Cek Saldo =====

    public function cekSaldoSimhar()
    {
        return $this->view('admin.savings.view_cetak_saldo_simpanan_harian');
    }

    public function cekSaldoNTR()
    {
        return $this->view('admin.savings.view_cetak_saldo_simpanan_NTR');
    }

    public function cekSaldoSSB()
    {
        return $this->view('admin.savings.view_cetak_saldo_simpanan_ssb');
    }

    /**
     * Cetak saldo harian (data untuk view)
     */
    public function dataSaldoSimHar()
    {
        $noKtp = request('idHiddenSimHar');
        $rows = \DB::table('mu_savings_account as sa')
            ->join('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('sa.*', 'm.MEMBER_NAME')
            ->where('sa.SAVINGS_PRODUCT_ID', SavingsService::PRODUCT_HARIAN)
            ->where(fn ($q) => $q->where('m.MEMBER_REGISTRY', $noKtp)->orWhere('m.MEMBER_NO', $noKtp))
            ->get();
        return $this->view('admin.savings.view_cetak_saldo_simpanan_harian', ['rows' => $rows]);
    }

    // ===== Tutup Rekening =====

    public function tutupRekSimhar()
    {
        $noKtp = request('idHiddenSimHar');
        $jasa = SavingsService::loadJasa();
        $detail = SavingsService::detailSimHar($noKtp ?? '');
        $transaksiAcc = SavingsService::loadTransaksiAcc();

        return $this->view('admin.savings.form_penutupan_simpanan_harian', [
            'jasa' => $jasa,
            'detailSimHar' => $detail,
            'transaksiAcc' => $transaksiAcc,
        ]);
    }

    public function prosesPenutupanSimHar()
    {
        $noAcc = request('edtNoAcc');
        SavingsService::deactivateAcc($noAcc, $this->userName ?? 'SYSTEM');
        return redirect()->route('admin.savings.harian')
            ->with('success', "Rekening {$noAcc} berhasil ditutup.");
    }

    public function tutupRekSetiaPlus()
    {
        return $this->view('admin.savings.form_penutupan_simpanan_setiaplus');
    }

    public function tutupRekAkunSSB()
    {
        $noKtp = request('idHiddenSSB');
        $detail = SavingsService::detailSimHar($noKtp ?? '');
        $pembulatan = '';
        $saldoSsb = \DB::table('mu_savings_account')
            ->where('SAVINGS_ACCOUNT_NO', $noKtp)
            ->first()?->SAVINGS_BALANCE;
        if ($saldoSsb !== null) {
            $pembulatan = (float) $saldoSsb;
        }

        return $this->view('admin.savings.form_penutupan_simpanan_ssb', [
            'jasa' => SavingsService::loadJasaSSB(),
            'detailSimSSB' => $detail,
            'transaksiAcc' => SavingsService::loadTransaksiAcc(),
            'pembulatan' => $pembulatan,
        ]);
    }

    public function prosesPenutupanSSB()
    {
        $noAcc = request('edtNoAcc');
        SavingsService::deactivateAcc($noAcc, $this->userName ?? 'SYSTEM');
        return redirect()->route('admin.savings.harian')
            ->with('success', "Rekening SSB {$noAcc} berhasil ditutup.");
    }

    // ===== Master NTR =====

    public function masterTransNTR()
    {
        return $this->view('admin.savings.form_master_simpanan_NTR');
    }

    /**
     * Form transaksi NTR
     */
    public function transNTR()
    {
        $noKtp = request('idHiddenSimNTR');
        $idAgt = SavingsService::cekIdAgt($noKtp ?? '')['MEMBER_REGISTRY'] ?? '';
        $result = $idAgt ? SavingsService::cekAnggotaNTR($idAgt) : [];

        $data = [
            'transaksiAcc' => SavingsService::loadTransaksiAcc(),
            'statAgt' => !empty($result) ? '1' : '2',
            'besaranSimpPokokWjb' => SavingsService::besarSimpWajibPokok(),
            'detailTransNTR' => $result,
            'edtSimWajibAkum' => SavingsService::cekSimpWajibAkum($noKtp ?? ''),
        ];

        return $this->view('admin.savings.form_transaksi_simpanan_NTR', $data);
    }

    // ===== Master SetiaPlus & SSB =====

    public function SetiaPlus()
    {
        $noKtp = request('idHiddenSetPlus');
        $data = ['NO_ACC' => '', 'detailSetPlus' => null, 'transaksiAcc' => SavingsService::loadTransaksiAcc()];

        if ($noKtp) {
            $cekNoUrut = SavingsService::checkNoUrutSimpananSetPlus($noKtp);
            if ($cekNoUrut) {
                $noUrut = $cekNoUrut['noUrut'];
                $idAgt = SavingsService::cekIdAgt($noKtp)['MEMBER_REGISTRY'] ?? '';
                $noAcc = SavingsService::loadAccNumber($idAgt, $noUrut, 4);
                $data['NO_ACC'] = $noAcc['noAcc'] ?? '';
            }
            $data['detailSetPlus'] = SavingsService::detailSimHar($noKtp);
        }

        return $this->view('admin.savings.form_master_simpanan_setiaplus', $data);
    }

    public function SSB()
    {
        $noKtp = request('idHiddenSSB');
        $data = ['NO_ACC' => '', 'detailSSB' => null, 'transaksiAcc' => SavingsService::loadTransaksiAcc()];

        if ($noKtp) {
            $cekNoUrut = SavingsService::checkNoUrutSimpananSSB($noKtp);
            if ($cekNoUrut) {
                $noUrut = $cekNoUrut['noUrut'];
                $idAgt = SavingsService::cekIdAgt($noKtp)['MEMBER_REGISTRY'] ?? '';
                $noAcc = SavingsService::loadAccNumber($idAgt, $noUrut, 3);
                $data['NO_ACC'] = $noAcc['noAcc'] ?? '';
            }
            $data['detailSSB'] = SavingsService::detailSimHar($noKtp);
            $data['jasa'] = SavingsService::loadJasaSSB();
        }

        return $this->view('admin.savings.form_master_simpanan_ssb', $data);
    }

    // ===== Daftar Akun Simpanan =====

    public function listAkunSimpanan()
    {
        $rows = SavingsService::daftarPembukaanAkun();
        return $this->view('admin.savings.view_data_simpanan_wajib', ['rows' => $rows]);
    }

    // ===== Batch 2: Transaksi Harian =====

    /**
     * Form transaksi simpanan harian
     */
    public function transSimHar()
    {
        $noKtp = request('idHiddenSimhar');
        $noTrans = SavingsService::loadTransaksiNumberSimHar();

        $detail = $noKtp
            ? \DB::table('mu_savings_account as sa')
                ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
                ->select('sa.*', 'm.MEMBER_NAME')
                ->where('sa.SAVINGS_ACCOUNT_NO', $noKtp)
                ->first()?->toArray()
            : null;

        $data = [
            'transaksiAcc' => SavingsService::loadTransaksiAcc(),
            'noTrans'      => $noTrans['transNo'] ?? '',
            'detailTransSimHar' => $detail,
        ];

        return $this->view('admin.savings.form_transaksi_simpanan_harian', $data);
    }

    /**
     * Proses simpan transaksi harian
     */
    public function prosesTransSimpHar()
    {
        $data = request()->all();
        $username = $this->userName ?? 'SYSTEM';
        SavingsService::prosesTransSimpHar($data, $username);
        return redirect()->route('admin.savings.harian')
            ->with('success', 'Transaksi harian berhasil diproses.');
    }

    /**
     * Halaman persetujuan transaksi harian
     */
    public function persetujuanTrans()
    {
        $rows = SavingsService::getTransactionTodayApproval();
        return $this->view('admin.savings.view_persetujuan_transaksi_simpanan_harian', [
            'rows' => $rows,
        ]);
    }

    /**
     * Proses approval transaksi harian
     */
    public function prosesApprovalTrans()
    {
        $idTrans = (int) request('idTrans');
        $data = ['chbApproval' => request('chbApproval', 0)];
        $username = $this->userName ?? 'SYSTEM';
        SavingsService::approvalTrans($data, $idTrans, 'TSH', $username);
        return redirect()->route('admin.savings.persetujuan')
            ->with('success', "Transaksi {$idTrans} berhasil di-approve.");
    }

    /**
     * Cetak BKM (Bukti Kas Masuk)
     */
    public function viewBKM()
    {
        $transID = request('transID');
        $row = $transID ? SavingsService::getApprovedTransaksi((int) $transID) : null;
        return $this->view('admin.savings.cetak_BKM_simpanan', ['row' => $row]);
    }

    /**
     * Cetak BKK (Bukti Kas Keluar)
     */
    public function viewBKK()
    {
        $transID = request('transID');
        $row = $transID ? SavingsService::getApprovedTransaksi((int) $transID) : null;
        return $this->view('admin.savings.cetak_BKK_simpanan', ['row' => $row]);
    }

    /**
     * Cetak BU (Bukti Urutan)
     */
    public function viewBU()
    {
        $transID = request('transID');
        $row = $transID ? SavingsService::getApprovedTransaksi((int) $transID) : null;
        return $this->view('admin.savings.cetak_BU_simpanan', ['row' => $row]);
    }

    // ===== Batch 7: Cetak / PDF (DomPDF) =====

    /**
     * Cetak BKM PDF (download)
     */
    public function cetakBKM()
    {
        $transID = (int) request('transID', 0);
        $row = $transID ? SavingsService::getApprovedTransaksi($transID) : null;
        // Guard: kalau transaksi tidak ditemukan, redirect ke list alih-alih
        // crash dengan "array offset on null" di view PDF.
        if (!$row) {
            return redirect()->route('admin.savings.persetujuan')->with('alert-error', 'Transaksi tidak ditemukan untuk dicetak.');
        }
        $pdf = PdfService::generate('pdf.savings.bkm', ['row' => (array) $row], 'BKM_' . ($row['TRANSACTION_NO'] ?? $transID));
        return $pdf;
    }

    /**
     * Cetak BKK PDF (download)
     */
    public function cetakBKK()
    {
        $transID = (int) request('transID', 0);
        $row = $transID ? SavingsService::getApprovedTransaksi($transID) : null;
        if (!$row) {
            return redirect()->route('admin.savings.persetujuan')->with('alert-error', 'Transaksi tidak ditemukan untuk dicetak.');
        }
        return PdfService::generate('pdf.savings.bkk', ['row' => (array) $row], 'BKK_' . ($row['TRANSACTION_NO'] ?? $transID));
    }

    /**
     * Cetak BU PDF (download)
     */
    public function cetakBU()
    {
        $transID = (int) request('transID', 0);
        $row = $transID ? SavingsService::getApprovedTransaksi($transID) : null;
        if (!$row) {
            return redirect()->route('admin.savings.persetujuan')->with('alert-error', 'Transaksi tidak ditemukan untuk dicetak.');
        }
        return PdfService::generate('pdf.savings.bu', ['row' => (array) $row], 'BU_' . ($row['TRANSACTION_NO'] ?? $transID));
    }

    /**
     * Export Excel: daftar akun simpanan (lap_pembukaan_akun)
     */
    public function exportExcelDaftarAkun()
    {
        $rows  = SavingsService::daftarPembukaanAkun();
        $resp  = ExcelService::exportRows($rows, 'daftar_akun_simpanan');
        return $resp;
    }

    /**
     * Export Excel: laporan saldo rinci
     */
    public function exportExcelLapSaldo()
    {
        $tglAwal = request('dtpTgl');
        $tglAkhir = request('dtpTglAkhir');
        $status  = (int) request('cmbStatus', 0);
        $rows = SavingsService::lapSaldoSimpRinci($tglAwal, $tglAkhir, $status);
        return ExcelService::exportRows($rows, 'lap_saldo_rinci_' . ($tglAkhir ?: date('Y-m-d')));
    }

    // ===== Batch 3: Transaksi NTR, Auto Closing, Bayar Jasa =====

    /**
     * Form transaksi NTR (setor/tarik) — versi form, berbeda dari transNTR() di batch 1 (master)
     */
    public function transNtrForm()
    {
        $idAgt = request('idAgt');
        $noTrans = SavingsService::loadTransaksiNumberNTR();
        $checkTransNtr = $idAgt ? SavingsService::checkTransNtrFirst($idAgt) : null;

        $data = [
            'noTrans' => $noTrans['transNo'] ?? '',
            'checkTransNtr' => $checkTransNtr,
        ];

        return $this->view('admin.savings.form_transaksi_ntr', $data);
    }

    /**
     * Proses simpan transaksi NTR
     */
    public function prosesTransNtr()
    {
        $data = request()->all();
        $username = $this->userName ?? 'SYSTEM';
        SavingsService::prosesTransNTR($data, $username);
        return redirect()->route('admin.savings.ntr')
            ->with('success', 'Transaksi NTR berhasil diproses.');
    }

    /**
     * Form transaksi SSB
     */
    public function transSsb()
    {
        $noAcc = request('idNoAccSSB');
        $noTrans = SavingsService::loadTransaksiNumberSimHar();
        $detail = $noAcc
            ? \DB::table('mu_savings_account as sa')
                ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
                ->select('sa.*', 'm.MEMBER_NAME')
                ->where('sa.SAVINGS_ACCOUNT_NO', $noAcc)
                ->first()?->toArray()
            : null;

        $data = [
            'noTrans' => $noTrans['transNo'] ?? '',
            'detailSsb' => $detail,
        ];

        return $this->view('admin.savings.form_transaksi_ssb', $data);
    }

    /**
     * Proses simpan transaksi SSB
     */
    public function prosesTransSsb()
    {
        $data = request()->all();
        $username = $this->userName ?? 'SYSTEM';
        SavingsService::prosesTransSSB($data, $username);
        return redirect()->route('admin.savings.ssb')
            ->with('success', 'Transaksi SSB berhasil diproses.');
    }

    /**
     * Auto Closing — daftar rekening yang perlu ditutup
     */
    public function autoClosing()
    {
        $tglAwal = request('tglAwal');
        $rows = SavingsService::autoClosing($tglAwal);
        return $this->view('admin.savings.auto_closing', ['rows' => $rows]);
    }

    /**
     * Proses close-by-user (manual closing per rekening)
     */
    public function prosesCloseByUser()
    {
        $noAcc = request('noAcc');
        SavingsService::deactivateAcc($noAcc, $this->userName ?? 'SYSTEM');
        return redirect()->route('admin.savings.auto_closing')
            ->with('success', "Rekening {$noAcc} berhasil ditutup.");
    }

    /**
     * Tagihan jasa SetiaPlus (jatuh tempo)
     */
    public function tagihanJasaSetiaPlus()
    {
        $rows = SavingsService::loadTagihanSetPlusJatemToday();
        return $this->view('admin.savings.tagihan_jasa_setiaplus', ['rows' => $rows]);
    }

    /**
     * Tagihan jasa SSB (jatuh tempo)
     */
    public function tagihanJasaSsb()
    {
        $rows = SavingsService::loadTagihanSSBJatemToday();
        return $this->view('admin.savings.tagihan_jasa_ssb', ['rows' => $rows]);
    }

    /**
     * Proses bayar jasa SetiaPlus
     */
    public function prosesBayarJasaSetiaPlus()
    {
        $data = request()->all();
        $username = $this->userName ?? 'SYSTEM';
        $ok = SavingsService::bayarJasaSetiaPlus($data, $username);
        return redirect()->route('admin.savings.tagihan_jasa_setiaplus')
            ->with('success', $ok ? 'Jasa SetiaPlus berhasil dibayar.' : 'Gagal memproses bayar jasa.');
    }

    // ===== Batch 4: Laporan =====

    /**
     * Laporan pembukaan akun simpanan harian
     */
    public function lapPembukaanAkunSimhar()
    {
        $tglAwal = request('dtpTgl');
        $tglAkhir = request('dtpTglAkhir');
        $rows = $tglAwal && $tglAkhir
            ? SavingsService::lapPembukaanAkunSimhar($tglAwal, $tglAkhir)
            : [];
        return $this->view('admin.savings.lap_pembukaan_akun_simhar', ['rows' => $rows]);
    }

    /**
     * Laporan pembukaan akun NTR
     */
    public function lapPembukaanAkunNTR()
    {
        $tglAwal = request('dtpTgl');
        $tglAkhir = request('dtpTglAkhir');
        $rows = $tglAwal && $tglAkhir
            ? SavingsService::lapPembukaanAkunNTR($tglAwal, $tglAkhir)
            : [];
        return $this->view('admin.savings.lap_pembukaan_akun_ntr', ['rows' => $rows]);
    }

    /**
     * Laporan pembukaan akun SSB
     */
    public function lapPembukaanAkunSSB()
    {
        $tglAwal = request('dtpTgl');
        $tglAkhir = request('dtpTglAkhir');
        $rows = $tglAwal && $tglAkhir
            ? SavingsService::lapPembukaanAkunSSB($tglAwal, $tglAkhir)
            : [];
        return $this->view('admin.savings.lap_pembukaan_akun_ssb', ['rows' => $rows]);
    }

    /**
     * Laporan penutupan akun simpanan harian
     */
    public function lapPenutupanAkunSimhar()
    {
        $tglAwal = request('dtpTgl');
        $tglAkhir = request('dtpTglAkhir');
        $rows = $tglAwal && $tglAkhir
            ? SavingsService::lapPenutupanAkunSimhar($tglAwal, $tglAkhir)
            : [];
        return $this->view('admin.savings.lap_penutupan_akun_simhar', ['rows' => $rows]);
    }

    /**
     * Laporan rekap saldo simpanan harian
     */
    public function lapSaldoSimpRekap()
    {
        $tglAwal = request('dtpTgl');
        $tglAkhir = request('dtpTglAkhir');
        $status = (int) request('cmbStatus', 0);
        $rows = $tglAwal && $tglAkhir
            ? SavingsService::lapSaldoSimpRekap($tglAwal, $tglAkhir, $status)
            : [];
        return $this->view('admin.savings.lap_saldo_simp_rekap', ['rows' => $rows]);
    }

    /**
     * Laporan rinci saldo simpanan harian
     */
    public function lapSaldoSimpRinci()
    {
        $tglAwal = request('dtpTgl');
        $tglAkhir = request('dtpTglAkhir');
        $status = (int) request('cmbStatus', 0);
        $rows = $tglAwal && $tglAkhir
            ? SavingsService::lapSaldoSimpRinci($tglAwal, $tglAkhir, $status)
            : [];
        return $this->view('admin.savings.lap_saldo_simp_rinci', ['rows' => $rows]);
    }

    /**
     * Buku besar SSB
     */
    public function lapBukuBesarSSB()
    {
        $noAcc = request('edtNoaccAwal');
        $rows = $noAcc ? SavingsService::lapBukuBesarSSB($noAcc) : [];
        return $this->view('admin.savings.lap_buku_besar_ssb', ['rows' => $rows]);
    }

    /**
     * Buku besar NTR
     */
    public function lapBukuBesarNTR()
    {
        $idAgt = request('edtNoReg');
        $rows = $idAgt ? SavingsService::lapBukuBesarNTR($idAgt) : [];
        return $this->view('admin.savings.lap_buku_besar_ntr', ['rows' => $rows]);
    }

    /**
     * Mutasi saldo NTR (rekap setor & tarik)
     */
    public function lapMutasiSaldoNTR()
    {
        $tglAwal = request('dtpTgl');
        $tglAkhir = request('dtpTglAkhir');
        $rekapSetor = $tglAwal && $tglAkhir ? SavingsService::lapMutasiSaldoNTRRekapSetor($tglAwal, $tglAkhir) : [];
        $rekapTarik = $tglAwal && $tglAkhir ? SavingsService::lapMutasiSaldoNTRRekapTarik($tglAwal, $tglAkhir) : [];
        return $this->view('admin.savings.lap_mutasi_saldo_ntr', ['rekapSetor' => $rekapSetor, 'rekapTarik' => $rekapTarik]);
    }

    // ===== Laporan SSB / Jatuh Tempo =====

    /**
     * Jatuh tempo SSB — rekap
     */
    public function lapJatemSSBRekap()
    {
        $tglAwal  = request('dtpTgl');
        $tglAkhir = request('dtpTglAkhir');
        $status   = (int) request('cmbStatus', 0);
        $rows = $tglAwal || $tglAkhir
            ? SavingsService::lapJatemSSBRekap($tglAwal, $tglAkhir, $status)
            : [];
        return $this->view('admin.savings.lap_jatem_ssb', ['rows' => $rows, 'rekap' => false]);
    }

    /**
     * Jatuh tempo SSB — rinci
     */
    public function lapJatemSSBRinci()
    {
        $tglAwal  = request('dtpTgl');
        $tglAkhir = request('dtpTglAkhir');
        $rows = $tglAwal || $tglAkhir
            ? SavingsService::lapJatemSSBRinci($tglAwal, $tglAkhir)
            : [];
        return $this->view('admin.savings.lap_jatem_ssb', ['rows' => $rows, 'rekap' => true]);
    }

    /**
     * Daftar akun SSB — rekap
     */
    public function lapDaftarAkunRekapSSB()
    {
        $tglAwal  = request('dtpTgl');
        $tglAkhir = request('dtpTglAkhir');
        $status   = (int) request('cmbStatus', 0);
        $rekap = SavingsService::lapDaftarAkunRekapSSB($tglAwal, $tglAkhir, $status);
        $rows  = SavingsService::lapDaftarAkunRinciSSB($tglAwal, $tglAkhir, $status);
        return $this->view('admin.savings.lap_daftar_akun_ssb', ['rekap' => $rekap, 'rows' => $rows]);
    }

    /**
     * Daftar akun SSB — rinci
     */
    public function lapDaftarAkunRinciSSB()
    {
        $tglAwal  = request('dtpTgl');
        $tglAkhir = request('dtpTglAkhir');
        $status   = (int) request('cmbStatus', 0);
        $rows = SavingsService::lapDaftarAkunRinciSSB($tglAwal, $tglAkhir, $status);
        return $this->view('admin.savings.lap_daftar_akun_ssb', ['rekap' => ['JML_AKUN' => count($rows), 'TOTAL_SALDO' => array_sum(array_map(fn($r) => (float)($r['SAVINGS_BALANCE'] ?? 0), $rows))], 'rows' => $rows]);
    }

    // ===== Batch 8: Proses Bisnis Lanjutan =====

    /**
     * Proses transaksi generik NTR (buka akun + setoran/tarikan).
     * Ganti `prosesTransSimp` CI3 (colom legacy tak ada di skema nyata).
     */
    public function prosesTransSimp()
    {
        $data = request()->all();
        $username = $this->userName ?? 'SYSTEM';
        $result = SavingsService::prosesTransSimp($data, $username);
        return redirect()->route('admin.savings.ntr')
            ->with('success', "Transaksi NTR #{$result['transNo']} berhasil diproses.");
    }

    /**
     * Update status transaksi (aman: update auditor + logtime).
     */
    public function updateTransSimp()
    {
        $data = request()->all();
        $username = $this->userName ?? 'SYSTEM';
        $ok = SavingsService::updateTransSimp($data, $username);
        return redirect()->route('admin.savings.persetujuan')
            ->with($ok ? 'success' : 'error', $ok ? 'Status transaksi di-update.' : 'Gagal update transaksi.');
    }

    /**
     * Tarik semua saldo harian (alias prosesTransSimpHar dengan type tarik).
     */
    public function tarikAll()
    {
        $data = request()->all();
        $username = $this->userName ?? 'SYSTEM';
        SavingsService::tarikAll($data, $username);
        return redirect()->route('admin.savings.harian')
            ->with('success', 'Saldo harian ditarik semua.');
    }

    /**
     * Tolak approval (reject) transaksi.
     */
    public function setFalseApproval()
    {
        $transId = (string) request('transID');
        $username = $this->userName ?? 'SYSTEM';
        $ok = SavingsService::setFalseApproval($transId, $username);
        return redirect()->route('admin.savings.persetujuan')
            ->with($ok ? 'success' : 'error', $ok ? "Transaksi {$transId} ditolak." : 'Gagal tolak transaksi.');
    }

    /**
     * Tandai transaksi selesai/dicetak buku.
     */
    public function prosesStatBuku()
    {
        $data = request()->all();
        $username = $this->userName ?? 'SYSTEM';
        $ok = SavingsService::prosesStatBuku($data, $username);
        return redirect()->route('admin.savings.persetujuan')
            ->with('success', 'Status buku di-update.');
    }

    /**
     * Cek simpanan pokok & wajib (JSON endpoint).
     */
    public function checkSimPokok()
    {
        $result = SavingsService::checkSimPokok(request()->all());
        return response()->json(['status' => 200, 'data' => $result]);
    }

    /**
     * Cek saldo simpanan pokok & wajib (JSON endpoint).
     */
    public function checkSaldoSimPokokWajib()
    {
        $result = SavingsService::checkSaldoSimPokokWajib(request()->all());
        return response()->json(['status' => 200, 'data' => $result]);
    }

    /**
     * Bayar jasa SSB (semua tagihan jatuh tempo hari ini).
     */
    public function doBayarJasaSSB()
    {
        $data = request()->all();
        $username = $this->userName ?? 'SYSTEM';
        $count = SavingsService::doBayarJasaSSB($data, $username);
        return redirect()->route('admin.savings.tagihan_jasa_ssb')
            ->with('success', "{$count} tagihan jasa SSB berhasil dibayar.");
    }

    /**
     * Bayar jasa SetiaPlus (semua tagihan jatuh tempo hari ini).
     */
    public function doBayarJasaSetiaplus()
    {
        $data = request()->all();
        $username = $this->userName ?? 'SYSTEM';
        $count = SavingsService::doBayarJasaSetiaplus($data, $username);
        return redirect()->route('admin.savings.tagihan_jasa_setiaplus')
            ->with('success', "{$count} tagihan jasa SetiaPlus berhasil dibayar.");
    }

    /**
     * Simpan master akun SSB (buka / update).
     */
    public function storeSsb()
    {
        $noAcc   = (string) request('edtNoAcc');
        $member  = (string) request('memberRegistry', '');
        $active  = (int) request('chbStatus', 1);
        $balance = (float) request('blokBalance', 0);
        $ok = SavingsService::addMasterSimpananSSB($noAcc, $member, $active, $balance);
        return redirect()->route('admin.savings.ssb')
            ->with($ok ? 'success' : 'error', $ok ? "Akun SSB {$noAcc} berhasil disimpan." : 'Gagal menyimpan akun SSB.');
    }

    /**
     * Simpan master akun SetiaPlus (buka / update).
     */
    public function storeSetiaplus()
    {
        $noAcc   = (string) request('edtNoAcc');
        $member  = (string) request('memberRegistry', '');
        $active  = (int) request('chbStatus', 1);
        $balance = (float) request('blokBalance', 0);
        $ok = SavingsService::addMasterSimpananSetiaPlus($noAcc, $member, $active, $balance);
        return redirect()->route('admin.savings.setiaplus')
            ->with($ok ? 'success' : 'error', $ok ? "Akun SetiaPlus {$noAcc} berhasil disimpan." : 'Gagal menyimpan akun SetiaPlus.');
    }

    // ===== Batch 9: Supporting / Utility (endpoints JSON) =====

    /**
     * Search data anggota per type (NTR/Harian/SSB/Pinjaman/potGaji, dll).
     * JSON endpoint — dipakai AJAX form.
     */
    public function searchDataNoOrName()
    {
        $searchKey = (string) request('search_value', '');
        $type      = (string) request('search_type', 'default');
        $row = SavingsService::searchDataNoOrName($searchKey, $type);
        $isActive = $row ? SavingsService::checkMemberActive($searchKey) : false;
        return response()->json([
            'status'  => $row ? 200 : 404,
            'data'    => $row,
            'isActive'=> $isActive,
        ]);
    }

    /**
     * Search data transaksi per anggota / type.
     */
    public function searchDataNoOrNameTrans()
    {
        $searchKey = (string) request('search_value', '');
        $type      = (string) request('search_type', 'default');
        $row = SavingsService::searchDataNoOrNameTrans($searchKey, $type);
        return response()->json(['status' => $row ? 200 : 404, 'data' => $row]);
    }

    /**
     * Search transaksi per transID (noTrans).
     */
    public function searchDataTransSimpHar()
    {
        $transId   = (int) request('trans_id', 0);
        $typeTrans = (string) request('type_trans', 'TSH');
        $row = SavingsService::searchTransactionByTransId($transId, $typeTrans);
        return response()->json(['status' => $row ? 200 : 404, 'data' => $row]);
    }

    /**
     * Menu dashboard Kasie.
     */
    public function menuKasie()
    {
        $data = SavingsService::menuKasieData();
        return $this->view('admin.savings.view_menu_kasie', $data);
    }

    /**
     * Menu dashboard Kasir (data hari ini).
     */
    public function menuKasir()
    {
        $data = SavingsService::menuKasirData();
        return $this->view('admin.savings.view_menu_kasir', $data);
    }

    /**
     * Menu PJ Simpanan (approval pending).
     */
    public function menuPjSimpanan()
    {
        $rows = SavingsService::menuPjSimpananData();
        return $this->view('admin.savings.view_menu_pj_simpanan', ['rows' => $rows]);
    }

    /**
     * View BI (Debit Nota) per transaksi.
     */
    public function viewBI()
    {
        $transId = (int) request('transID', 0);
        $row = SavingsService::viewBI($transId);
        return $this->view('admin.savings.view_bukti_informasi', ['row' => $row]);
    }

    /**
     * Tabel BKM hari ini.
     */
    public function showTableBuktiKasMasuk()
    {
        $rows = SavingsService::showTableBuktiKasMasuk();
        return response()->json(['status' => 200, 'data' => $rows]);
    }

    /**
     * Tabel BKK hari ini.
     */
    public function showTableBuktiKasKeluar()
    {
        $rows = SavingsService::showTableBuktiKasKeluar();
        return response()->json(['status' => 200, 'data' => $rows]);
    }

    /**
     * Laporan PD (pengunduran diri) — JSON endpoint.
     */
    public function tampilLaporanPD()
    {
        $rows = SavingsService::tampilLaporanPD(
            request('tglAwal'),
            request('tglAkhir')
        );
        return response()->json(['status' => 200, 'data' => $rows]);
    }

    /**
     * Laporan Pembekuan — JSON endpoint.
     */
    public function tampilLapPembekuan()
    {
        $rows = SavingsService::tampilLapPembekuan();
        return response()->json(['status' => 200, 'data' => $rows]);
    }

    /**
     * Pengajuan Simpanan (data pendukung, JSON).
     */
    public function pengajuanSimpanan()
    {
        $data = SavingsService::pengajuanSimpananData();
        return response()->json(['status' => 200, 'data' => $data]);
    }
}
