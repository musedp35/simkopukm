<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LoanService
{
    public const PRODUCT_UKM     = 'LOAN001';
    public const PRODUCT_MICRO   = 'LOAN002';
    public const PRODUCT_JAMINAN = 'LOAN003';
    public const PRODUCT_PP      = 'LOAN004';
    public const PRODUCT_EMAS    = 'LOAN005';
    public const PRODUCT_SIMHAR  = 'LOAN006';
    public const PRODUCT_POTONG  = 'LOAN007';

    public static function loadDataPeminjam(?string $role = null): array
    {
        $base = DB::table('mu_loan_account as mla')
            ->leftJoin('mu_loan as ml', 'ml.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->select('mla.*', 'ml.LOAN_ID', 'ml.APPROVAL_STATUS_ID', 'ml.UPDATE_BY',
                     'm.MEMBER_NAME', 'm.MEMBER_NO', 'm.ADDRESS_STAY');

        $query = match ($role) {
            'ukm'      => $base->where('mla.LOAN_PRODUCT_ID', self::PRODUCT_UKM)->where('mla.LOAN_STATUS_ID', 0),
            'mikro'    => $base->where('mla.LOAN_PRODUCT_ID', self::PRODUCT_MICRO)->where('mla.LOAN_STATUS_ID', 0),
            'jaminan'  => $base->where('mla.LOAN_PRODUCT_ID', self::PRODUCT_JAMINAN)->where('mla.LOAN_STATUS_ID', 0),
            'rescheduling', 'jatem' => $base->where('mla.LOAN_STATUS_ID', 8),
            'noGold'   => $base->whereIn('mla.LOAN_PRODUCT_ID', [self::PRODUCT_UKM, self::PRODUCT_MICRO])
                              ->whereIn('mla.LOAN_STATUS_ID', [0, 1]),
            'PJ'       => $base->whereIn('mla.LOAN_PRODUCT_ID', [self::PRODUCT_UKM, self::PRODUCT_MICRO, self::PRODUCT_JAMINAN])
                              ->whereIn('mla.LOAN_STATUS_ID', [0, 1, 2, 3]),
            'PJGlobal' => $base->whereIn('mla.LOAN_PRODUCT_ID', [self::PRODUCT_UKM, self::PRODUCT_MICRO, self::PRODUCT_JAMINAN]),
            'AO3'      => $base->whereIn('mla.LOAN_STATUS_ID', [3, 4]),
            'AO4'      => $base->whereIn('mla.LOAN_STATUS_ID', [4, 5]),
            'AO6'      => $base->where('mla.LOAN_STATUS_ID', '>', 5),
            'kasie'    => $base,
            default    => $base->whereIn('mla.LOAN_STATUS_ID', [0, 1]),
        };

        return $query->orderByDesc('mla.LOGTIME')->get()->toArray();
    }

    public static function loadAllLoanProducts(): array
    {
        return DB::table('mu_loan_product')->get()->toArray();
    }

    public static function detailPinjamanMG(string $memberRegistry): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('mu_loan_account as mla', 'mla.MEMBER_REGISTRY', '=', 'm.MEMBER_REGISTRY')
            ->select('m.*', 'mla.LOAN_ACCOUNT_NO', 'mla.LOAN_AMOUNT', 'mla.REMAINING_AMOUNT')
            ->where('m.MEMBER_REGISTRY', $memberRegistry)
            ->first();
        return $row ? (array) $row : null;
    }

    public static function getNoCredit(string $memberRegistry, ?array $dataAgt = null, string $type = 'PP'): array
    {
        $date   = date('Ym');
        $lastNo = DB::table('mu_loan_account')
            ->where('MEMBER_REGISTRY', $memberRegistry)
            ->where('LOAN_ACCOUNT_NO', 'like', "CR{$date}%")
            ->max('LOAN_ACCOUNT_NO');
        $seq      = $lastNo ? ((int) substr($lastNo, -4)) + 1 : 1;
        $noCredit = 'CR' . $date . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
        return ['noCredit' => $noCredit];
    }

    public static function checkAngsuran(string $memberRegistry): ?array
    {
        $row = DB::table('mu_trans_installment as ti')
            ->leftJoin('mu_loan_account as mla', 'mla.LOAN_ACCOUNT_NO', '=', 'ti.LOAN_ACCOUNT_NO')
            ->where('ti.MEMBER_REGISTRY', $memberRegistry)
            ->where('ti.STATUS_ID', 0)
            ->orderByDesc('ti.DUE_DATE')
            ->first();
        return $row ? (array) $row : null;
    }

    public static function rincianAngsuran(string $loanAccountNo): array
    {
        return DB::table('mu_trans_installment')
            ->where('LOAN_ACCOUNT_NO', $loanAccountNo)
            ->orderBy('INSTALLMENT_NO')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    public static function detailAngsuran(string $loanAccountNo): ?array
    {
        $row = DB::table('mu_trans_installment')
            ->where('LOAN_ACCOUNT_NO', $loanAccountNo)
            ->orderByDesc('INSTALLMENT_NO')
            ->first();
        return $row ? (array) $row : null;
    }

    public static function loadDataTagihan(): array
    {
        return DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->select('mla.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->whereIn('mla.LOAN_STATUS_ID', [7, 8])
            ->orderByDesc('mla.LOGTIME')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    public static function getDataAnggotaPinjaman(): array
    {
        return DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->select('mla.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->whereNotNull('mla.LOAN_ACCOUNT_NO')
            ->orderByDesc('mla.LOGTIME')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    public static function getDataAnggotaPinjamanBukuBesar(): array
    {
        return DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->select('mla.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->whereNotNull('mla.LOAN_ACCOUNT_NO')
            ->orderBy('mla.LOAN_ACCOUNT_NO')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    public static function getDataPinjamanAllStatus(): array
    {
        return DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->select('mla.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->orderByDesc('mla.LOAN_STATUS_ID')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    public static function getTransactionTodayPinjaman(): array
    {
        return DB::table('mu_trans_realization as tr')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'tr.MEMBER_REGISTRY')
            ->select('tr.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->whereDate('tr.TRANSACTION_DATE', today()->toDateString())
            ->orderByDesc('tr.TRANSACTION_DATE')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    public static function getTransactionTodayApproval(): array
    {
        return DB::table('mu_loan as ml')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'ml.MEMBER_REGISTRY')
            ->select('ml.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('ml.APPROVAL_STATUS_ID', 0)
            ->orderByDesc('ml.LOAN_DATE')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    public static function approvalTrans(int $chbApprove, int $transId, string $typeTrans = 'TPJ', string $username = 'SYSTEM'): bool
    {
        $status = $chbApprove ? 1 : 2;
        $now    = now()->toDateTimeString();
        DB::transaction(function () use ($status, $transId, $now) {
            DB::table('mu_loan')->where('LOAN_ID', $transId)
                ->update(['APPROVAL_STATUS_ID' => $status, 'LOGTIME' => $now]);
            DB::table('mu_trans_realization')->where('TRANS_ID', $transId)
                ->update(['STATUS_ID' => $status, 'LOGTIME' => $now]);
        });
        return true;
    }

    public static function viewBKM(int $transId): ?array
    {
        $row = DB::table('mu_trans_realization as tr')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'tr.MEMBER_REGISTRY')
            ->select('tr.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('tr.TRANS_ID', $transId)
            ->first();
        return $row ? (array) $row : null;
    }

    public static function viewBKK(int $transId): ?array
    {
        return self::viewBKM($transId);
    }

    public static function viewBI(int $transId): ?array
    {
        $row = DB::table('mu_trans_installment as ti')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'ti.MEMBER_REGISTRY')
            ->select('ti.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('ti.TRANS_ID', $transId)
            ->first();
        return $row ? (array) $row : null;
    }

    // ===== Batch 11: Tagihan, Angsuran Kolektif, Rescheduling =====

    /**
     * loadTagihanJatem — daftar tagihan jatuh tempo (angsuran belum dibayar).
     * LOAN_STATUS_ID 7 = jatuh tempo, 8 = rescheduling.
     * mu_trans_installment STATUS_ID=0 → belum dibayar.
     *
     * @param string|null $dateJatem filter tanggal jatuh tempo (YYYY-MM-DD), null = semua
     */
    public static function loadTagihanJatem(?string $dateJatem = null): array
    {
        $q = DB::table('mu_trans_installment as ti')
            ->leftJoin('mu_loan_account as mla', 'mla.LOAN_ACCOUNT_NO', '=', 'ti.LOAN_ACCOUNT_NO')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'ti.MEMBER_REGISTRY')
            ->select('ti.*', 'mla.LOAN_ACCOUNT_NO', 'mla.LOAN_AMOUNT', 'mla.REMAINING_AMOUNT',
                     'm.MEMBER_NAME', 'm.MEMBER_NO', 'm.ADDRESS_STAY')
            ->where('ti.STATUS_ID', 0);

        if ($dateJatem) {
            $q->whereDate('ti.DUE_DATE', $dateJatem);
        } else {
            $q->where('ti.DUE_DATE', '<=', today()->toDateString());
        }

        return $q->orderBy('ti.DUE_DATE')->get()->map(fn ($r) => (array) $r)->all();
    }

    /**
     * loadAngsuranKolektif — daftar angsuran kolektif (STATUS_ID=0, due date ≤ hari ini).
     * Digunakan untuk form batch pembayaran.
     */
    public static function loadAngsuranKolektif(): array
    {
        return DB::table('mu_trans_installment as ti')
            ->leftJoin('mu_loan_account as mla', 'mla.LOAN_ACCOUNT_NO', '=', 'ti.LOAN_ACCOUNT_NO')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'ti.MEMBER_REGISTRY')
            ->select('ti.*', 'mla.LOAN_ACCOUNT_NO', 'mla.LOAN_PRODUCT_ID',
                     'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('ti.STATUS_ID', 0)
            ->where('ti.DUE_DATE', '<=', today()->toDateString())
            ->orderBy('ti.DUE_DATE')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    /**
     * doBayarAngsuranKolektif — proses pembayaran angsuran kolektif (batch).
     * Update mu_trans_installment STATUS_ID=1 + PAID_DATE,
     * update mu_loan_account REMAINING_AMOUNT.
     *
     * @param int[] $transIds daftar TRANS_ID yang dibayar
     * @param string $username
     * @return int jumlah angsuran yang berhasil dibayar
     */
    public static function doBayarAngsuranKolektif(array $transIds, string $username = 'SYSTEM'): int
    {
        $count = 0;
        $now   = now()->toDateTimeString();

        DB::transaction(function () use ($transIds, $now, $username, &$count) {
            foreach ($transIds as $transId) {
                $row = DB::table('mu_trans_installment')->where('TRANS_ID', $transId)->first();
                if (! $row || $row->STATUS_ID != 0) {
                    continue; // skip yang sudah dibayar
                }

                DB::table('mu_trans_installment')->where('TRANS_ID', $transId)->update([
                    'STATUS_ID'  => 1,
                    'PAID_DATE'  => $now,
                    'LOGTIME'    => $now,
                ]);

                // Update sisa angsuran
                $remaining = (float) $row->REMAINING_AMOUNT ?? 0;
                DB::table('mu_loan_account')->where('LOAN_ACCOUNT_NO', $row->LOAN_ACCOUNT_NO)->update([
                    'REMAINING_AMOUNT' => $remaining - (float) $row->AMOUNT,
                    'LOGTIME'         => $now,
                ]);

                $count++;
            }
        });

        return $count;
    }

    /**
     * loadReschedulingJatem — daftar pinjaman yang dalam kondisi rescheduling (STATUS=8).
     */
    public static function loadReschedulingJatem(): array
    {
        return DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->leftJoin('mu_loan as ml', 'ml.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->select('mla.*', 'ml.LOAN_ID', 'ml.APPROVAL_STATUS_ID',
                     'm.MEMBER_NAME', 'm.MEMBER_NO', 'm.ADDRESS_STAY')
            ->where('mla.LOAN_STATUS_ID', 8)
            ->orderByDesc('mla.LOGTIME')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    /**
     * doReschedulingJatem — proses rescheduling jatuh tempo.
     * Ubah LOAN_STATUS_ID=8 → 7 (aktif lagi), reset due date angsuran,
     * update approval status.
     *
     * @param int $loanAccountNo
     * @param int $newInstallmentTotal jumlah angsuran baru
     * @param string $username
     */
    public static function doReschedulingJatem(string $loanAccountNo, int $newInstallmentTotal, string $username = 'SYSTEM'): bool
    {
        $now = now()->toDateTimeString();

        DB::transaction(function () use ($loanAccountNo, $newInstallmentTotal, $now, $username) {
            // Update status loan account
            DB::table('mu_loan_account')->where('LOAN_ACCOUNT_NO', $loanAccountNo)->update([
                'LOAN_STATUS_ID' => 7,
                'LOGTIME'        => $now,
            ]);

            // Reset angsuran yang belum dibayar (set due date baru mulai dari sekarang)
            $today = now()->toDateString();
            $angsuran = DB::table('mu_trans_installment')
                ->where('LOAN_ACCOUNT_NO', $loanAccountNo)
                ->where('STATUS_ID', 0)
                ->orderBy('INSTALLMENT_NO')
                ->get();

            $nextDue = now();
            foreach ($angsuran as $i => $ang) {
                $newDue = $nextDue->copy()->addMonths(1, $i + 1)->toDateString();
                DB::table('mu_trans_installment')->where('TRANS_ID', $ang->TRANS_ID)->update([
                    'DUE_DATE'  => $newDue,
                    'LOGTIME'   => $now,
                ]);
            }
        });

        return true;
    }

    /**
     * doReschedulingByReq — rescheduling berdasarkan request.
     * Mirip doReschedulingJatem tapi dari form request spesifik.
     */
    public static function doReschedulingByReq(string $loanAccountNo, ?string $requestDate, string $username = 'SYSTEM'): bool
    {
        $now = now()->toDateTimeString();

        DB::transaction(function () use ($loanAccountNo, $requestDate, $now) {
            DB::table('mu_loan_account')->where('LOAN_ACCOUNT_NO', $loanAccountNo)->update([
                'LOAN_STATUS_ID' => 7,
                'LOGTIME'        => $now,
            ]);

            $angsuran = DB::table('mu_trans_installment')
                ->where('LOAN_ACCOUNT_NO', $loanAccountNo)
                ->where('STATUS_ID', 0)
                ->orderBy('INSTALLMENT_NO')
                ->get();

            $base = $requestDate ? \Carbon\Carbon::parse($requestDate) : now();
            $i = 0;
            foreach ($angsuran as $ang) {
                $newDue = $base->copy()->addMonths(1, $i + 1)->toDateString();
                DB::table('mu_trans_installment')->where('TRANS_ID', $ang->TRANS_ID)->update([
                    'DUE_DATE' => $newDue,
                    'LOGTIME'  => $now,
                ]);
                $i++;
            }
        });

        return true;
    }

    /**
     * detailRescheduling — detail data rescheduling per loan account.
     */
    public static function detailRescheduling(string $loanAccountNo): ?array
    {
        $row = DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->select('mla.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('mla.LOAN_ACCOUNT_NO', $loanAccountNo)
            ->first();

        if (! $row) {
            return null;
        }

        $angsuran = DB::table('mu_trans_installment')
            ->where('LOAN_ACCOUNT_NO', $loanAccountNo)
            ->orderBy('INSTALLMENT_NO')
            ->get()->map(fn ($r) => (array) $r)->all();

        return ['account' => (array) $row, 'angsuran' => $angsuran];
    }

    /**
     * loadDataTagihanKolektif — data untuk form tagihan kolektif.
     */
    public static function loadDataTagihanKolektif(): array
    {
        return DB::table('mu_trans_installment as ti')
            ->leftJoin('mu_loan_account as mla', 'mla.LOAN_ACCOUNT_NO', '=', 'ti.LOAN_ACCOUNT_NO')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'ti.MEMBER_REGISTRY')
            ->select('ti.*', 'mla.LOAN_ACCOUNT_NO', 'mla.LOAN_PRODUCT_ID',
                     'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('ti.STATUS_ID', 0)
            ->orderBy('ti.DUE_DATE')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    // ===== Batch 12: Laporan Agunan, Realisasi, Pelunasan, Resheduling =====

    /**
     * loadLaporanAgunan — laporan rekap agunan per jenis (BPKB, Emas, Gaji, Simpanan, Sertifikat).
     */
    public static function loadLaporanAgunan(string $jenis = 'BPKB'): array
    {
        $tableMap = [
            'BPKB'       => 'mu_collateral_bpkb',
            'Emas'       => 'mu_collateral_gold',
            'Gaji'       => 'mu_collateral_salary',
            'Simpanan'   => 'mu_collateral_savings',
            'Sertifikat' => 'mu_collateral_certificate',
        ];
        $table = $tableMap[$jenis] ?? 'mu_collateral_bpkb';

        return DB::table($table . ' as c')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'c.MEMBER_REGISTRY')
            ->leftJoin('mu_loan as ml', 'ml.LOAN_ID', '=', 'c.LOAN_ID')
            ->select('c.*', 'm.MEMBER_NAME', 'm.MEMBER_NO', 'ml.LOAN_AMOUNT', 'ml.LOAN_STATUS_ID')
            ->orderByDesc('c.LOGTIME')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    /**
     * loadLaporanRealisasi — laporan realisasi pinjaman (mu_trans_realization).
     */
    public static function loadLaporanRealisasi(): array
    {
        return DB::table('mu_trans_realization as tr')
            ->leftJoin('mu_loan_account as mla', 'mla.LOAN_ACCOUNT_NO', '=', 'tr.LOAN_ACCOUNT_NO')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'tr.MEMBER_REGISTRY')
            ->select('tr.*', 'mla.LOAN_PRODUCT_ID', 'mla.LOAN_AMOUNT',
                     'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->orderByDesc('tr.TRANSACTION_DATE')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    /**
     * loadLaporanPelunasan — laporan pelunasan pinjaman.
     * Pelunasan = mu_loan_account dengan REMAINING_AMOUNT = 0 atau LOAN_STATUS_ID = pelunas.
     * Status: 0=aktif, 1=approved, 2=processing, 3=AO3, 4=AO4, 5=AO6, 6=pelunas, 7=jatem, 8=rescheduling
     */
    public static function loadLaporanPelunasan(): array
    {
        return DB::table('mu_loan_account as mla')
            ->leftJoin('mu_loan as ml', 'ml.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->select('mla.*', 'ml.LOAN_ID', 'ml.LOAN_DATE', 'ml.APPROVAL_STATUS_ID',
                     'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('mla.LOAN_STATUS_ID', 6)
            ->orderByDesc('mla.LOGTIME')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    /**
     * loadLaporanResheduling — laporan resheduling (LOAN_STATUS_ID = 8).
     */
    public static function loadLaporanResheduling(): array
    {
        return DB::table('mu_loan_account as mla')
            ->leftJoin('mu_loan as ml', 'ml.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->select('mla.*', 'ml.LOAN_ID', 'ml.LOAN_DATE', 'ml.APPROVAL_STATUS_ID',
                     'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('mla.LOAN_STATUS_ID', 8)
            ->orderByDesc('mla.LOGTIME')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    /**
     * loadLaporanAngsuran — laporan angsuran per periode.
     *
     * @param string $periodStart YYYY-MM-DD
     * @param string $periodEnd   YYYY-MM-DD
     */
    public static function loadLaporanAngsuran(string $periodStart, string $periodEnd): array
    {
        return DB::table('mu_trans_installment as ti')
            ->leftJoin('mu_loan_account as mla', 'mla.LOAN_ACCOUNT_NO', '=', 'ti.LOAN_ACCOUNT_NO')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'ti.MEMBER_REGISTRY')
            ->select('ti.*', 'mla.LOAN_ACCOUNT_NO', 'mla.LOAN_PRODUCT_ID',
                     'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->whereBetween('ti.DUE_DATE', [$periodStart, $periodEnd])
            ->orderBy('ti.DUE_DATE')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    /**
     * loadLaporanSPH — daftar peminjam yang sudah selesai (LOAN_STATUS_ID > 6).
     * Status: 6=pelunas, 7=jatem, 8=rescheduling.
     * Query aman: hanya pakai tabel yang ada di schema baru (mu_loan_account + ma_member).
     */
    public static function loadLaporanSPH(): array
    {
        return DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->leftJoin('mu_loan as ml', 'ml.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->select(
                'mla.*',
                'ml.LOAN_ID',
                'm.MEMBER_NAME',
                'm.MEMBER_NO',
                'm.ADDRESS_STAY'
            )
            ->where('mla.LOAN_STATUS_ID', '>', 6)
            ->orderBy('mla.LOAN_ACCOUNT_NO')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    /**
     * detailSPH — data lengkap peminjam untuk pencetakan SPH (kecil / besar).
     * Query aman: mu_loan_account + ma_member + mu_loan + mu_survey (jika ada).
     *
     * @param string $loanAccountNo LOAN_ACCOUNT_NO
     */
    public static function detailSPH(string $loanAccountNo, int $collateralId = 0): ?array
    {
        $row = DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->leftJoin('mu_loan as ml', 'ml.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->where('mla.LOAN_ACCOUNT_NO', $loanAccountNo)
            ->select(
                'mla.*',
                'ml.LOAN_ID', 'ml.APPROVAL_STATUS_ID',
                'm.MEMBER_NAME', 'm.MEMBER_NO',
                'm.ADDRESS_STAY',
                'm.MEMBER_EMAIL',
                'm.MEMBER_STATUS_ID'
            )
            ->first();

        if (! $row) return null;

        $result = (array) $row;

        // Muat data survey jika ada di mu_survey (new schema)
        try {
            $survey = DB::table('mu_survey')
                ->where('LOAN_ID', $row->LOAN_ID ?? 0)
                ->first();
            if ($survey) {
                $result['SURVEY_RESULT'] = $survey->SURVEY_RESULT;
                $result['SURVEYOR']      = $survey->SURVEYOR;
            }
        } catch (\Illuminate\Database\QueryException) {
            // mu_survey tidak ada — skip
        }

        return $result;
    }

    /**
     * doRealisasi — proses realisasi pinjaman (pencairan).
     * Insert ke mu_trans_realization.
     *
     * @param string $loanAccountNo
     * @param float  $amount
     * @param string $username
     * @return int TRANS_ID
     */
    public static function doRealisasi(string $loanAccountNo, float $amount, string $username = 'SYSTEM'): int
    {
        $now = now()->toDateTimeString();

        $transId = DB::table('mu_trans_realization')->insertGetId([
            'LOAN_ACCOUNT_NO'  => $loanAccountNo,
            'MEMBER_REGISTRY'  => '', // filled below
            'AMOUNT'           => $amount,
            'TRANSACTION_DATE' => $now,
            'STATUS_ID'        => 0,
            'LOGTIME'          => $now,
        ]);

        // Get MEMBER_REGISTRY from loan account
        $memberRegistry = DB::table('mu_loan_account')
            ->where('LOAN_ACCOUNT_NO', $loanAccountNo)
            ->value('MEMBER_REGISTRY');

        if ($memberRegistry) {
            DB::table('mu_trans_realization')->where('TRANS_ID', $transId)
                ->update(['MEMBER_REGISTRY' => $memberRegistry]);
        }

        return (int) $transId;
    }

    /**
     * doPelunasan — proses pelunasan pinjaman.
     * Update mu_loan_account LOAN_STATUS_ID=6, REMAINING_AMOUNT=0.
     *
     * @param string $loanAccountNo
     * @param string $username
     */
    public static function doPelunasan(string $loanAccountNo, string $username = 'SYSTEM'): bool
    {
        $now = now()->toDateTimeString();

        DB::transaction(function () use ($loanAccountNo, $now) {
            DB::table('mu_loan_account')->where('LOAN_ACCOUNT_NO', $loanAccountNo)->update([
                'LOAN_STATUS_ID'   => 6,
                'REMAINING_AMOUNT' => 0,
                'LOGTIME'          => $now,
            ]);

            DB::table('mu_loan')->where('MEMBER_REGISTRY',
                DB::table('mu_loan_account')->where('LOAN_ACCOUNT_NO', $loanAccountNo)->value('MEMBER_REGISTRY')
            )->update([
                'LOAN_STATUS_ID'     => 6,
                'APPROVAL_STATUS_ID' => 1,
                'LOGTIME'            => $now,
            ]);
        });

        return true;
    }

    // ===== Batch 13: View Payment, doRealisasi (lanjutan), Approval Multi-Level =====

    /**
     * loadViewPayment — data untuk view pembayaran pinjaman (transaksi hari ini + angsuran).
     */
    public static function loadViewPayment(): array
    {
        $realisasi = DB::table('mu_trans_realization as tr')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'tr.MEMBER_REGISTRY')
            ->select('tr.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->whereDate('tr.TRANSACTION_DATE', today()->toDateString())
            ->orderByDesc('tr.TRANSACTION_DATE')
            ->get()->map(fn ($r) => (array) $r)->all();

        $angsuran = DB::table('mu_trans_installment as ti')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'ti.MEMBER_REGISTRY')
            ->select('ti.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->whereDate('ti.PAID_DATE', today()->toDateString())
            ->orderByDesc('ti.PAID_DATE')
            ->get()->map(fn ($r) => (array) $r)->all();

        return ['realisasi' => $realisasi, 'angsuran' => $angsuran];
    }

    /**
     * approvalMultiLevel — proses approval multi-level (AO→PJ→Kasie).
     * Setiap level update APPROVAL_STATUS_ID dan LOAN_STATUS_ID:
     *   AO3: APPROVAL_STATUS_ID=1, LOAN_STATUS_ID=3
     *   AO4: APPROVAL_STATUS_ID=1, LOAN_STATUS_ID=4
     *   AO6: APPROVAL_STATUS_ID=1, LOAN_STATUS_ID=5
     *   Kasie: APPROVAL_STATUS_ID=1, LOAN_STATUS_ID=1 (approved final)
     *
     * @param string $loanAccountNo
     * @param string $level ao3|ao4|ao6|kasie
     * @param int $approve 1=approve, 0=reject
     * @param string $username
     */
    public static function approvalMultiLevel(string $loanAccountNo, string $level, int $approve, string $username = 'SYSTEM'): bool
    {
        $statusMap = ['ao3' => 3, 'ao4' => 4, 'ao6' => 5, 'kasie' => 1];
        $nextStatus = $statusMap[$level] ?? 0;

        $now = now()->toDateTimeString();

        DB::transaction(function () use ($loanAccountNo, $nextStatus, $approve, $now) {
            $newApproval = $approve ? 1 : 2;
            $newLoanStatus = $approve ? $nextStatus : $nextStatus + 10; // reject = offset 10

            DB::table('mu_loan_account')->where('LOAN_ACCOUNT_NO', $loanAccountNo)->update([
                'LOAN_STATUS_ID' => $newLoanStatus,
                'LOGTIME'        => $now,
            ]);

            DB::table('mu_loan')->where('MEMBER_REGISTRY',
                DB::table('mu_loan_account')->where('LOAN_ACCOUNT_NO', $loanAccountNo)->value('MEMBER_REGISTRY')
            )->update([
                'APPROVAL_STATUS_ID' => $newApproval,
                'LOAN_STATUS_ID'     => $newLoanStatus,
                'UPDATE_BY'          => $username,
                'LOGTIME'            => $now,
            ]);
        });

        return true;
    }

    /**
     * loadPendingApproval — daftar pinjaman yang menunggu approval per level.
     *
     * @param string $level ao3|ao4|ao6|kasie
     */
    public static function loadPendingApproval(string $level): array
    {
        $statusMap = ['ao3' => 2, 'ao4' => 3, 'ao6' => 4, 'kasie' => 5];
        $pendingStatus = $statusMap[$level] ?? 2;

        return DB::table('mu_loan_account as mla')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'mla.MEMBER_REGISTRY')
            ->select('mla.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('mla.LOAN_STATUS_ID', $pendingStatus)
            ->orderByDesc('mla.LOGTIME')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    /**
     * doTunggakan — hitung total tunggakan per anggota.
     */
    public static function doTunggakan(): array
    {
        return DB::table('mu_trans_installment as ti')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'ti.MEMBER_REGISTRY')
            ->select(
                'ti.MEMBER_REGISTRY',
                'm.MEMBER_NAME', 'm.MEMBER_NO',
                DB::raw('COUNT(*) as TOTAL_TUNGGAKAN'),
                DB::raw('SUM(ti.AMOUNT) as TOTAL_JUMLAH')
            )
            ->where('ti.STATUS_ID', 0)
            ->groupBy('ti.MEMBER_REGISTRY', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->orderByDesc('TOTAL_JUMLAH')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    // ===== Batch B1: Pengajuan Pinjaman Multiguna (MG) =====

    /**
     * detailPeminjamMG — ambil data anggota + alamat lengkap untuk form pengajuan MG.
     * Logika: CI3 Loan::detailPinjamanMG (join ma_city/district/village/type_city + foto).
     *
     * @param string $noAgt MEMBER_NO atau MEMBER_REGISTRY
     * @return array|null
     */
    public static function detailPeminjamMG(string $noAgt): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('ma_city as c', 'c.CITY_ID', '=', 'm.CITY_ID')
            ->leftJoin('ma_district as d', 'd.DISTRICT_ID', '=', 'm.DISTRICT_ID')
            ->leftJoin('ma_village as v', 'v.VILLAGE_ID', '=', 'm.VILLAGE_ID')
            ->leftJoin('ma_type_city as t', 't.TYPE_CITY_ID', '=', 'c.TYPE_CITY_ID')
            ->leftJoin('ma_images as img', 'img.USER_ID', '=', 'm.MEMBER_REGISTRY')
            ->select(
                'm.*',
                'c.CITY_NAME AS KOTA_DOM',
                't.TYPE_CITY_CODE',
                DB::raw("CONCAT(m.ADDRESS_STAY, ', ', v.VILLAGE_NAME, ', ', d.DISTRICT_NAME, ', ', t.TYPE_CITY_CODE, ' ', c.CITY_NAME) AS MEMBER_ADDRESS"),
                'img.IMAGE_FILE'
            )
            ->where('img.IMAGE_TYPE', 'PHOTO')
            ->where(function ($q) use ($noAgt) {
                $q->where('m.MEMBER_NO', $noAgt)
                  ->orWhere('m.MEMBER_REGISTRY', $noAgt);
            })
            ->whereIn('m.MEMBER_STATUS_ID', [2, 4])
            ->first();

        return $row ? (array) $row : null;
    }

    /**
     * getNoCreditMG — generate nomor kredit MG berdasarkan jenis agunan.
     * Logika: CI3 LoanAccount::getNoCredit, parameter $type menentukan COLLATERAL_ID.
     *
     * @param string $memberRegistry
     * @param int $countLoan jumlah pinjaman aktif saat ini
     * @param string $type 'Emas'|'Simhar'|'Serti'|'PP'|'PotongGaji'
     * @return array ['noCredit' => string]
     */
    public static function getNoCreditMG(string $memberRegistry, int $countLoan, string $type = 'Emas'): array
    {
        // Ambil product ID untuk MG (default LOAN004)
        $productRow = DB::table('mu_loan_product')
            ->where('LOAN_CODE', 'LIKE', 'LOAN%')
            ->where('LOAN_PRODUCT_ID', '>', 3)
            ->orderBy('LOAN_PRODUCT_ID')
            ->first();
        $loancode = $productRow?->LOAN_CODE ?? 'CR';

        // Mapping jenis agunan → COLLATERAL_ID
        $collateralMap = ['Emas' => 4, 'Simhar' => 3, 'Serti' => 2, 'PP' => 4, 'PotongGaji' => 5];
        $colId = $collateralMap[$type] ?? 1;

        $row = DB::table('ma_member as m')
            ->leftJoin('mu_loan_product as lpd', 'lpd.LOAN_PRODUCT_ID', '=', 4)
            ->select('m.MEMBER_NO', 'm.MEMBER_STATUS_ID')
            ->where('m.MEMBER_REGISTRY', $memberRegistry)
            ->first();

        $statusId   = $row?->MEMBER_STATUS_ID ?? 0;
        $memberNo   = $row?->MEMBER_NO ?? '';

        // Format: LOAN_CODE + collateral_id + status_id + 4 + MEMBER_NO + (COUNT_LOAN + 1)
        $noCredit = $loancode . $colId . $statusId . $colId . $memberNo . ($countLoan + 1);

        return ['noCredit' => $noCredit];
    }

    /**
     * getJasaMG — ambil interest rate produk MG per jenis agunan.
     * Logika: CI3 LoanAccount::getJasa (mu_loan_interest).
     *
     * @param string $memberRegistry
     * @param string $type 'Emas'|'Simhar'|'Serti'|'PP'
     * @return array
     */
    public static function getJasaMG(string $memberRegistry, string $type = ''): array
    {
        $member = DB::table('ma_member')->where('MEMBER_REGISTRY', $memberRegistry)->first();
        $statusId = $member?->MEMBER_STATUS_ID ?? 0;

        return DB::table('mu_loan_interest')
            ->where('LOAN_PRODUCT_ID', 4)
            ->where('MEMBER_STATUS_ID', $statusId)
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    /**
     * getProvisiMG — ambil provision rate produk MG per jenis agunan.
     * Logika: CI3 LoanAccount::getProvisi (mu_loan_provision).
     *
     * @param string $memberRegistry
     * @param string $type 'Emas'|'Serti'|'Simhar'|'PP'
     * @return array
     */
    public static function getProvisiMG(string $memberRegistry, string $type = ''): array
    {
        $colIdMap = ['Emas' => 4, 'Serti' => 2, 'Simhar' => 3, 'PP' => 1];
        $colId = $colIdMap[$type] ?? 1;

        return DB::table('mu_loan_provision')
            ->where('LOAN_PRODUCT_ID', 4)
            ->where('LOAN_COLLATERAL_ID', $colId)
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    /**
     * savePengajuanMG — simpan pengajuan pinjaman MG baru.
     * Insert ke mu_loan + mu_loan_account (STATUS_ID=0 → menunggu approval).
     *
     * @param string $memberRegistry
     * @param array $data form data
     * @param string $username
     * @return bool
     */
    public static function savePengajuanMG(string $memberRegistry, array $data, string $username = 'SYSTEM'): bool
    {
        $now = now()->toDateTimeString();

        DB::transaction(function () use ($memberRegistry, $data, $username, $now) {
            // Insert mu_loan (data utama pinjaman)
            $loanId = DB::table('mu_loan')->insertGetId([
                'MEMBER_REGISTRY'    => $memberRegistry,
                'LOAN_PRODUCT_ID'    => 4, // MG
                'LOAN_AMOUNT'        => $data['LOAN_AMOUNT'] ?? 0,
                'LOAN_PERIOD'       => $data['LOAN_PERIOD'] ?? 0,
                'LOAN_STATUS_ID'     => 0,
                'APPROVAL_STATUS_ID' => 0,
                'LOAN_DATE'          => $now,
                'UPDATE_BY'          => $username,
                'LOGTIME'            => $now,
            ]);

            // Insert mu_loan_account
            $noCredit = $data['NO_CREDIT'] ?? '';
            DB::table('mu_loan_account')->insert([
                'LOAN_ID'          => $loanId,
                'MEMBER_REGISTRY'  => $memberRegistry,
                'LOAN_ACCOUNT_NO'  => $noCredit,
                'LOAN_PRODUCT_ID'  => 4,
                'LOAN_AMOUNT'      => $data['LOAN_AMOUNT'] ?? 0,
                'REMAINING_AMOUNT' => $data['LOAN_AMOUNT'] ?? 0,
                'LOAN_STATUS_ID'   => 0,
                'LOGTIME'          => $now,
            ]);
        });

        return true;
    }
}