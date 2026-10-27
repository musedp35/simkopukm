<?php

namespace App\Services;

use App\Models\SimpananProduct;
use Illuminate\Support\Facades\DB;

/**
 * SavingsService - pengganti method-method model `Saving` CI3.
 * Query disesuaikan skema database simkopukm.
 * Kode produk: SIM001 = Harian, SIM002 = NTR.
 */
class SavingsService
{
    public const PRODUCT_HARIAN = 'SIM001';
    public const PRODUCT_NTR    = 'SIM002';

    public static function transTableForProduct(string $productId): string
    {
        return match ($productId) {
            self::PRODUCT_HARIAN => 'mu_trans_daily_savings',
            self::PRODUCT_NTR    => 'mu_trans_ntr_savings',
            default              => 'mu_trans_ssb_savings',
        };
    }

    /**
     * Generate no. akun baru berformat SAV0001 (seq global 4-digit).
     * Sesuai sample data nyata di mu_savings_account.
     */
    public static function loadAccNumber(string $memberRegistry = '', int $noUrut = 0, ?string $productId = null): ?array
    {
        $row = DB::table('mu_savings_account')
            ->select(DB::raw("MAX(CAST(SUBSTRING(SAVINGS_ACCOUNT_NO, 4) AS UNSIGNED)) AS maxNo"))
            ->where('SAVINGS_ACCOUNT_NO', 'like', 'SAV%')
            ->first();
        $maxNo = $row ? (int) $row->maxNo : 0;
        $next  = str_pad((string) ($maxNo + 1), 4, '0', STR_PAD_LEFT);
        return ['noAcc' => 'SAV' . $next];
    }

    public static function cekIdAgt(string $noKTP): ?array
    {
        $row = DB::table('ma_member as m')
            ->select('m.MEMBER_REGISTRY', 'm.MEMBER_NO', 'm.MEMBER_NAME')
            ->where('m.MEMBER_REGISTRY', $noKTP)
            ->orWhere('m.MEMBER_NO', $noKTP)
            ->orWhere('m.MEMBER_NAME', $noKTP)
            ->first();
        if ($row) {
            return (array) $row;
        }
        $acc = DB::table('mu_savings_account as sa')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('m.MEMBER_REGISTRY', 'm.MEMBER_NO', 'm.MEMBER_NAME')
            ->where('sa.SAVINGS_ACCOUNT_NO', $noKTP)
            ->first();
        return $acc ? (array) $acc : null;
    }

    public static function checkNoUrutSimpanan(string $id): ?array
    {
        $count = DB::table('mu_savings_account')
            ->where('SAVINGS_PRODUCT_ID', self::PRODUCT_HARIAN)
            ->where('MEMBER_REGISTRY', $id)->count();
        return ['noUrut' => $count + 1];
    }

    public static function checkNoUrutSimpananSetPlus(string $id): ?array
    {
        $count = DB::table('mu_savings_account')
            ->where('SAVINGS_PRODUCT_ID', self::PRODUCT_NTR)
            ->where('MEMBER_REGISTRY', $id)->count();
        return ['noUrut' => $count + 1];
    }

    public static function checkNoUrutSimpananSSB(string $id): ?array
    {
        return self::checkNoUrutSimpananSetPlus($id);
    }

    /**
     * Buka / update akun simpanan.
     * - Jika SAVINGS_ACCOUNT_NO belum ada → INSERT (pembukaan rekening baru).
     * - Jika sudah ada → UPDATE status aktif + LOGTIME (rekening sudah dibuka).
     * Skema nyata mu_savings_account hanya punya kolom:
     *   SAVINGS_ACCOUNT_NO, MEMBER_REGISTRY, SAVINGS_PRODUCT_ID,
     *   ACTIVE_ACCOUNT_ID, SAVINGS_BALANCE, LOGTIME
     *
     * @return bool true jika berhasil (insert atau update)
     */
    public static function addMasterSimpananHarian(
        string $noAcc,
        string $memberRegistry,
        int $active,
        float $balance = 0.0,
        string $username = 'SYSTEM'
    ): bool {
        $exists = DB::table('mu_savings_account')
            ->where('SAVINGS_ACCOUNT_NO', $noAcc)
            ->exists();

        $now = now()->toDateTimeString();

        if ($exists) {
            return DB::table('mu_savings_account')
                ->where('SAVINGS_ACCOUNT_NO', $noAcc)
                ->update([
                    'ACTIVE_ACCOUNT_ID' => $active,
                    'SAVINGS_BALANCE'   => $balance,
                    'LOGTIME'           => $now,
                ]) >= 0;
        }

        return DB::table('mu_savings_account')->insert([
            'SAVINGS_ACCOUNT_NO' => $noAcc,
            'MEMBER_REGISTRY'    => $memberRegistry,
            'SAVINGS_PRODUCT_ID' => self::PRODUCT_HARIAN,
            'ACTIVE_ACCOUNT_ID'  => $active,
            'SAVINGS_BALANCE'    => $balance,
            'LOGTIME'            => $now,
        ]) === true;
    }

    public static function detailSimHar(string $noKtp): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('ma_city as c', 'm.CITY_ID', '=', 'c.CITY_ID')
            ->leftJoin('ma_district as d', 'm.DISTRICT_ID', '=', 'd.DISTRICT_ID')
            ->leftJoin('ma_village as v', 'm.VILLAGE_ID', '=', 'v.VILLAGE_ID')
            ->select(
                'm.MEMBER_REGISTRY', 'm.MEMBER_NO', 'm.MEMBER_NAME', 'm.MEMBER_EMAIL',
                'm.ADDRESS_STAY', 'm.MEMBER_STATUS_ID', 'm.ACTIVE_STATUS_ID',
                'm.MEMBER_DATE_REGISTRY', 'm.MEMBER_DATE_VERIFICATION', 'm.BANKING_ACCOUNT_NO',
                'c.CITY_NAME', 'd.DISTRICT_NAME', 'v.VILLAGE_NAME',
            )
            ->where(fn ($q) => $q->where('m.MEMBER_REGISTRY', $noKtp)->orWhere('m.MEMBER_NO', $noKtp))
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * Detail SSB / SetiaPlus per anggota (by MEMBER_REGISTRY / MEMBER_NO).
     * Padanan CI3 detailSSB / detailSetPlus (disederhanakan ke skema nyata).
     */
    public static function detailSSB(string $noKtp): ?array
    {
        return self::detailByMember($noKtp, function ($q) {
            // SSB tidak difilter produk spesifik di skema nyata; ambil member + photo
        });
    }

    public static function detailSetPlus(string $noKtp): ?array
    {
        return self::detailByMember($noKtp);
    }

    /**
     * Detail untuk form penutupan (SimHar / SSB): ambil member + semua akun simpanannya.
     * Padanan CI3 detailPenutupanSimHar / detailPenutupanSSB.
     */
    public static function detailPenutupanSimHar(string $noKtp): ?array
    {
        return self::detailPenutupan($noKtp, self::PRODUCT_HARIAN);
    }

    public static function detailPenutupanSSB(string $noKtp): ?array
    {
        return self::detailPenutupan($noKtp, self::PRODUCT_NTR);
    }

    protected static function detailPenutupan(string $noKtp, string $productId): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('mu_savings_account as sa', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select(
                'm.MEMBER_REGISTRY', 'm.MEMBER_NO', 'm.MEMBER_NAME', 'm.MEMBER_STATUS_ID',
                'sa.SAVINGS_ACCOUNT_NO', 'sa.SAVINGS_PRODUCT_ID',
                'sa.ACTIVE_ACCOUNT_ID', 'sa.SAVINGS_BALANCE', 'sa.LOGTIME',
            )
            ->where('sa.SAVINGS_PRODUCT_ID', $productId)
            ->where(fn ($q) => $q
                ->where('sa.SAVINGS_ACCOUNT_NO', $noKtp)
                ->orWhere('m.MEMBER_REGISTRY', $noKtp)
                ->orWhere('m.MEMBER_NO', $noKtp)
            )
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * Detail transaksi SimHar untuk form transaksi: member + akun aktif Harian.
     * Padanan CI3 detailTransSimHar (SAVINGS_PRODUCT_ID = Harian & aktif).
     */
    public static function detailTransSimHar(string $noRek): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('mu_savings_account as sa', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('m.MEMBER_REGISTRY', 'm.MEMBER_NO', 'm.MEMBER_NAME',
                'sa.SAVINGS_ACCOUNT_NO', 'sa.SAVINGS_BALANCE', 'sa.ACTIVE_ACCOUNT_ID')
            ->where(fn ($q) => $q
                ->where('sa.SAVINGS_ACCOUNT_NO', $noRek)
                ->orWhere('m.MEMBER_REGISTRY', $noRek)
                ->orWhere('m.MEMBER_NO', $noRek)
            )
            ->where('sa.SAVINGS_PRODUCT_ID', self::PRODUCT_HARIAN)
            ->where('sa.ACTIVE_ACCOUNT_ID', 1)
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * Riwayat transaksi NTR per anggota (form transaksi NTR).
     * Padanan CI3 detailTransNTR.
     */
    public static function detailTransNTR(string $noKtp): array
    {
        return DB::table('mu_trans_ntr_savings as t')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 't.MEMBER_REGISTRY')
            ->select('m.MEMBER_REGISTRY', 'm.MEMBER_NAME', 't.*')
            ->where(fn ($q) => $q
                ->where('m.MEMBER_REGISTRY', $noKtp)
                ->orWhere('m.MEMBER_NO', $noKtp)
            )
            ->orderByDesc('t.TRANSACTION_DATE')
            ->get()
            ->toArray();
    }

    /**
     * Cek no. akun SimHar (member + keluarga + akun).
     * Padanan CI3 findNoAccSimpHar.
     */
    public static function findNoAccSimpHar(string $noAcc): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('ma_family as fm', 'm.MEMBER_REGISTRY', '=', 'fm.MEMBER_REGISTRY')
            ->leftJoin('mu_savings_account as sa', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->leftJoin('ma_city as c', 'm.CITY_ID', '=', 'c.CITY_ID')
            ->leftJoin('ma_district as d', 'm.DISTRICT_ID', '=', 'd.DISTRICT_ID')
            ->leftJoin('ma_village as v', 'm.VILLAGE_ID', '=', 'v.VILLAGE_ID')
            ->select('m.*', 'sa.*', 'fm.FAMILY_NAME', 'c.CITY_NAME', 'd.DISTRICT_NAME', 'v.VILLAGE_NAME')
            ->where('sa.SAVINGS_ACCOUNT_NO', $noAcc)
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * Cek no. akun NTR (aktif saja). Padanan CI3 findNoAccNTR.
     */
    public static function findNoAccNTR(string $noAcc): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('mu_savings_account as sa', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('m.*', 'sa.*')
            ->where('sa.SAVINGS_ACCOUNT_NO', $noAcc)
            ->where('sa.ACTIVE_ACCOUNT_ID', 1)
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * Cek no. akun SSB. Padanan CI3 findNoAccSSB (di skema nyata pakai SIM002).
     */
    public static function findNoAccSSB(string $noAcc): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('mu_savings_account as sa', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('m.*', 'sa.*')
            ->where('sa.SAVINGS_ACCOUNT_NO', $noAcc)
            ->where('sa.SAVINGS_PRODUCT_ID', self::PRODUCT_NTR)
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * Cek saldo aktif SimHar > MIN_BALANCE (untuk validasi).
     * Padanan CI3 findNoAccSimpHarSaldoExist (DAILY_SAVINGS_BALANCE → SAVINGS_BALANCE).
     */
    public static function findNoAccSimpHarSaldoExist(string $noAcc, float $minBalance = 0.0): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('mu_savings_account as sa', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('m.*', 'sa.*')
            ->where('sa.SAVINGS_ACCOUNT_NO', $noAcc)
            ->where('sa.SAVINGS_PRODUCT_ID', self::PRODUCT_HARIAN)
            ->where('sa.SAVINGS_BALANCE', '>', $minBalance)
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * Detail anggota NTR (untuk form transaksi NTR) — address priority 1.
     * Padanan CI3 findNoAgtNTR.
     */
    public static function findNoAgtNTR(string $id): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('mu_savings_account as sa', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->leftJoin('ma_city as c', 'm.CITY_ID', '=', 'c.CITY_ID')
            ->leftJoin('ma_district as d', 'm.DISTRICT_ID', '=', 'd.DISTRICT_ID')
            ->leftJoin('ma_village as v', 'm.VILLAGE_ID', '=', 'v.VILLAGE_ID')
            ->select('m.*', 'sa.*', 'c.CITY_NAME', 'd.DISTRICT_NAME', 'v.VILLAGE_NAME')
            ->where('sa.MEMBER_REGISTRY', $id)
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * Data anggota untuk form simpanan (member + family + business + akun).
     * Padanan CI3 getDataAnggotaSimpanan (disederhanakan ke kolom yang ada di skema nyata).
     */
    public static function getDataAnggotaSimpanan(string $memberRegistry, string $noAcc): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('ma_family as fm', 'm.MEMBER_REGISTRY', '=', 'fm.MEMBER_REGISTRY')
            ->leftJoin('mu_savings_account as sa', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->leftJoin('ma_city as c', 'm.CITY_ID', '=', 'c.CITY_ID')
            ->leftJoin('ma_district as d', 'm.DISTRICT_ID', '=', 'd.DISTRICT_ID')
            ->leftJoin('ma_village as v', 'm.VILLAGE_ID', '=', 'v.VILLAGE_ID')
            ->select('m.*', 'sa.*', 'fm.FAMILY_NAME', 'c.CITY_NAME', 'd.DISTRICT_NAME', 'v.VILLAGE_NAME')
            ->where('m.MEMBER_REGISTRY', $memberRegistry)
            ->where('sa.SAVINGS_ACCOUNT_NO', $noAcc)
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * Data anggota SSB. Padanan CI3 getDataAnggotaSimpananSSB.
     */
    public static function getDataAnggotaSimpananSSB(string $memberRegistry, string $noAcc): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('mu_savings_account as sa', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('m.*', 'sa.*')
            ->where('m.MEMBER_REGISTRY', $memberRegistry)
            ->where('sa.SAVINGS_ACCOUNT_NO', $noAcc)
            ->where('sa.SAVINGS_PRODUCT_ID', self::PRODUCT_NTR)
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * Data anggota SetiaPlus. Padanan CI3 getDataAnggotaSimpananSetiaPlus.
     */
    public static function getDataAnggotaSimpananSetiaPlus(string $memberRegistry): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('mu_savings_account as sa', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('m.*', 'sa.*')
            ->where('m.MEMBER_REGISTRY', $memberRegistry)
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * Data anggota untuk closing (status: NTR/TR/pending).
     * Padanan CI3 getDataAnggotaSimpananClosing.
     */
    public static function getDataAnggotaSimpananClosing(string $memberRegistry): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('mu_savings_account as sa', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('m.*', 'sa.*')
            ->where('m.MEMBER_REGISTRY', $memberRegistry)
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * Ambil TRANSACATION_ID transaksi harian per akun. Padanan CI3 getIdTrans.
     */
    public static function getIdTrans(array $data): ?array
    {
        $noAcc = $data['edtNoacc'] ?? $data['edtNoAcc'] ?? '';
        $row = DB::table('mu_trans_daily_savings')
            ->select('TRANSACTION_ID')
            ->where('SAVINGS_ACCOUNT_NO', $noAcc)
            ->orderByDesc('TRANSACTION_ID')
            ->first();
        return $row ? ['TRANSACTION_ID' => $row->TRANSACTION_ID] : null;
    }

    /**
     * Query bantu: detail member + photo (padanan umum detailSSB/detailSetPlus).
     */
    protected static function detailByMember(string $noKtp, ?callable $callback = null): ?array
    {
        $q = DB::table('ma_member as m')
            ->leftJoin('ma_images as img', fn ($j) => $j
                ->on('img.USER_ID', '=', 'm.MEMBER_REGISTRY')
                ->where('img.IMAGE_TYPE', 'PHOTO'))
            ->leftJoin('ma_city as c', 'm.CITY_ID', '=', 'c.CITY_ID')
            ->leftJoin('ma_district as d', 'm.DISTRICT_ID', '=', 'd.DISTRICT_ID')
            ->leftJoin('ma_village as v', 'm.VILLAGE_ID', '=', 'v.VILLAGE_ID')
            ->select('m.*', 'img.IMAGE_FILE', 'c.CITY_NAME', 'd.DISTRICT_NAME', 'v.VILLAGE_NAME')
            ->where(fn ($w) => $w->where('m.MEMBER_REGISTRY', $noKtp)->orWhere('m.MEMBER_NO', $noKtp));

        if ($callback) {
            $callback($q);
        }

        $row = $q->first();
        return $row ? (array) $row : null;
    }

    public static function loadJasa(): array
    {
        return SimpananProduct::orderBy('SAVINGS_PRODUCT_ID')->get()->toArray();
    }

    public static function loadJasaSSB(): array
    {
        return SimpananProduct::where(function ($q) {
                $q->where('PRODUCT_NAME', 'like', '%Depo%')
                    ->orWhere('PRODUCT_NAME', 'like', '%SSB%')
                    ->orWhere('PRODUCT_NAME', 'like', '%Harian%');
            })->get()->toArray();
    }

    public static function loadJasaSetiaPlus(): array
    {
        return self::loadJasa();
    }

    // ===== Transaksi Simpanan =====

    public static function loadTransaksiNumberSimHar(): ?array
    {
        $next = (int) DB::table('mu_transaction_account')->max('TRANSACTION_ID') + 1;
        return ['transNo' => $next, 'TRANSACTION_ID' => $next];
    }

    public static function loadTransaksiNumberNTR(): ?array
    {
        return self::loadTransaksiNumberSimHar();
    }

    public static function prosesTransSimpHar(array $data, string $username, string $productId = self::PRODUCT_HARIAN): void
    {
        $noAcc    = $data['edtNoacc'] ?? $data['edtNoAcc'] ?? '';
        $jnsTrans = (int) ($data['cmbJnsTransaksi'] ?? 1);
        $amount   = (float) ($data['edtSimwajib'] ?? 0) + (float) ($data['edtSimpokok'] ?? 0);

        $acc   = DB::table('mu_savings_account')->where('SAVINGS_ACCOUNT_NO', $noAcc)->first();
        $saldo = $acc ? (float) $acc->SAVINGS_BALANCE : 0.0;
        $after = $jnsTrans === 1 ? $saldo + $amount : $saldo - $amount;

        $transNo = $data['edtNotrans'] ?? '';
        $transId = $transNo ? (int) $transNo : self::loadTransaksiNumberSimHar()['TRANSACTION_ID'];

        DB::table('mu_trans_daily_savings')->insert([
            'TRANSACTION_ID'     => $transId,
            'MEMBER_REGISTRY'    => $acc->MEMBER_REGISTRY ?? '',
            'SAVINGS_ACCOUNT_NO' => $noAcc,
            'AMOUNT'             => $amount,
            'BALANCE_AFTER'      => $after,
            'TRANSACTION_DATE'   => now()->toDateTimeString(),
            'LOGTIME'            => now()->toDateTimeString(),
        ]);

        DB::table('mu_savings_account')->where('SAVINGS_ACCOUNT_NO', $noAcc)
            ->update(['SAVINGS_BALANCE' => $after]);

        DB::table('mu_transaction_account')->insert([
            'TRANSACTION_ID'     => $transId,
            'TRANSACTION_NO'     => sprintf('TSH-%s-%s', date('ymd'), $transId),
            'MEMBER_REGISTRY'    => $acc->MEMBER_REGISTRY ?? '',
            'SAVINGS_ACCOUNT_NO' => $noAcc,
            'TRANSACTION_TYPE'   => $jnsTrans === 1 ? 'SETOR' : 'TARIK',
            'AMOUNT'             => $amount,
            'TRANSACTION_DATE'   => now()->toDateTimeString(),
            'STATUS_ID'          => 1,
            'APPROVAL_STATUS_ID' => 0,
            'UPDATE_BY'          => $username,
            'LOGTIME'            => now()->toDateTimeString(),
        ]);
    }

    public static function prosesTransNTR(array $data, string $username): void
    {
        $noAcc    = $data['edtNoacc'] ?? $data['idAgt'] ?? '';
        $jnsTrans = (int) ($data['cmbJnsTransaksi'] ?? 1);
        $amount   = (float) ($data['edtSimwajib'] ?? 0) + (float) ($data['edtSimpokok'] ?? 0);

        $acc   = DB::table('mu_savings_account')->where('SAVINGS_ACCOUNT_NO', $noAcc)->first();
        $saldo = $acc ? (float) $acc->SAVINGS_BALANCE : 0.0;
        $after = $jnsTrans === 1 ? $saldo + $amount : $saldo - $amount;

        $transNo = $data['edtNotrans'] ?? '';
        $transId = $transNo ? (int) $transNo : self::loadTransaksiNumberSimHar()['TRANSACTION_ID'];

        DB::table('mu_trans_ntr_savings')->insert([
            'TRANSACTION_ID'     => $transId,
            'MEMBER_REGISTRY'    => $acc->MEMBER_REGISTRY ?? $noAcc,
            'SAVINGS_ACCOUNT_NO' => $noAcc,
            'AMOUNT'             => $amount,
            'BALANCE_AFTER'      => $after,
            'TRANSACTION_DATE'   => now()->toDateTimeString(),
            'LOGTIME'            => now()->toDateTimeString(),
        ]);

        DB::table('mu_savings_account')->where('SAVINGS_ACCOUNT_NO', $noAcc)
            ->update(['SAVINGS_BALANCE' => $after]);

        DB::table('mu_transaction_account')->insert([
            'TRANSACTION_ID'     => $transId,
            'TRANSACTION_NO'     => sprintf('TSN-%s-%s', date('ymd'), $transId),
            'MEMBER_REGISTRY'    => $acc->MEMBER_REGISTRY ?? $noAcc,
            'SAVINGS_ACCOUNT_NO' => $noAcc,
            'TRANSACTION_TYPE'   => $jnsTrans === 1 ? 'SETOR' : 'TARIK',
            'AMOUNT'             => $amount,
            'TRANSACTION_DATE'   => now()->toDateTimeString(),
            'STATUS_ID'          => 1,
            'APPROVAL_STATUS_ID' => 0,
            'UPDATE_BY'          => $username,
            'LOGTIME'            => now()->toDateTimeString(),
        ]);
    }

    public static function prosesTransSSB(array $data, string $username): void
    {
        $noAcc    = $data['edtNoacc'] ?? $data['edtNoAcc'] ?? '';
        $jnsTrans = (int) ($data['cmbJnsTransaksi'] ?? 1);
        $amount   = (float) ($data['edtSimpokok'] ?? 0);

        $acc   = DB::table('mu_savings_account')->where('SAVINGS_ACCOUNT_NO', $noAcc)->first();
        $saldo = $acc ? (float) $acc->SAVINGS_BALANCE : 0.0;
        $after = $jnsTrans === 1 ? $saldo + $amount : $saldo - $amount;

        $transNo = $data['edtNotrans'] ?? '';
        $transId = $transNo ? (int) $transNo : self::loadTransaksiNumberSimHar()['TRANSACTION_ID'];

        DB::table('mu_trans_ssb_savings')->insert([
            'TRANSACTION_ID'     => $transId,
            'MEMBER_REGISTRY'    => $acc->MEMBER_REGISTRY ?? '',
            'SAVINGS_ACCOUNT_NO' => $noAcc,
            'AMOUNT'             => $amount,
            'BALANCE_AFTER'      => $after,
            'TRANSACTION_DATE'   => now()->toDateTimeString(),
            'LOGTIME'            => now()->toDateTimeString(),
        ]);

        DB::table('mu_savings_account')->where('SAVINGS_ACCOUNT_NO', $noAcc)
            ->update(['SAVINGS_BALANCE' => $after]);

        DB::table('mu_transaction_account')->insert([
            'TRANSACTION_ID'     => $transId,
            'TRANSACTION_NO'     => sprintf('TSD-%s-%s', date('ymd'), $transId),
            'MEMBER_REGISTRY'    => $acc->MEMBER_REGISTRY ?? '',
            'SAVINGS_ACCOUNT_NO' => $noAcc,
            'TRANSACTION_TYPE'   => $jnsTrans === 1 ? 'SETOR' : 'TARIK',
            'AMOUNT'             => $amount,
            'TRANSACTION_DATE'   => now()->toDateTimeString(),
            'STATUS_ID'          => 1,
            'APPROVAL_STATUS_ID' => 0,
            'UPDATE_BY'          => $username,
            'LOGTIME'            => now()->toDateTimeString(),
        ]);
    }

    // ===== Approval & List =====

    public static function approvalTrans(array $data, int $transId, string $typeTrans, string $username): void
    {
        $approve = isset($data['chbApproval']) && (int) $data['chbApproval'] === 1 ? 1 : 0;
        DB::table('mu_transaction_account')
            ->where('TRANSACTION_ID', $transId)
            ->update([
                'APPROVAL_STATUS_ID' => $approve,
                'UPDATE_BY'          => $username,
                'LOGTIME'            => now()->toDateTimeString(),
            ]);
    }

    public static function getTransactionTodayApproval(?string $cetak = null): array
    {
        return DB::table('mu_transaction_account as t')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 't.MEMBER_REGISTRY')
            ->select(
                't.TRANSACTION_ID', 't.TRANSACTION_NO', 't.TRANSACTION_DATE',
                't.SAVINGS_ACCOUNT_NO', 't.TRANSACTION_TYPE', 't.AMOUNT',
                't.APPROVAL_STATUS_ID', 'm.MEMBER_NAME',
            )
            ->where('t.APPROVAL_STATUS_ID', 0)
            ->orderByDesc('t.TRANSACTION_DATE')
            ->get()
            ->toArray();
    }

    public static function loadTransaksiAcc(): array
    {
        return SimpananProduct::orderBy('SAVINGS_PRODUCT_ID')->get()->toArray();
    }

    public static function getApprovedTransaksi(int $transId): ?array
    {
        $row = DB::table('mu_transaction_account as t')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 't.MEMBER_REGISTRY')
            ->select('t.*', 'm.MEMBER_NAME')
            ->where('t.TRANSACTION_ID', $transId)
            ->first();
        return $row ? (array) $row : null;
    }

    // ===== Tutup Rekening =====

    public static function deactivateAcc(string $noAcc, string $username): bool
    {
        return DB::table('mu_savings_account')
            ->where('SAVINGS_ACCOUNT_NO', $noAcc)
            ->update(['ACTIVE_ACCOUNT_ID' => 0, 'LOGTIME' => now()->toDateTimeString()]) > 0;
    }

    public static function autoClosing(?string $tglAwal = null, int $days = 90): array
    {
        return DB::table('mu_savings_account as sa')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->where('sa.ACTIVE_ACCOUNT_ID', 1)
            ->select('sa.SAVINGS_ACCOUNT_NO', 'm.MEMBER_NAME', 'sa.SAVINGS_PRODUCT_ID')
            ->orderBy('sa.SAVINGS_ACCOUNT_NO')
            ->get()
            ->toArray();
    }

    public static function daftarPembukaanAkun(): array
    {
        return DB::table('mu_savings_account as sa')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select(
                'sa.SAVINGS_ACCOUNT_NO', 'sa.SAVINGS_PRODUCT_ID', 'sa.SAVINGS_BALANCE',
                'sa.ACTIVE_ACCOUNT_ID', 'sa.LOGTIME as DATE_REGISTRATION',
                'm.MEMBER_NAME as SAVINGS_ACCOUNT_NAME', 'm.MEMBER_NO',
            )
            ->orderBy('sa.SAVINGS_ACCOUNT_NO')
            ->get()
            ->toArray();
    }

    // ===== Tagihan Jasa =====

    public static function loadTagihanSetPlusJatemToday(): array
    {
        return DB::table('mu_savings_account as msa')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'msa.MEMBER_REGISTRY')
            ->select('msa.SAVINGS_ACCOUNT_NO', 'msa.SAVINGS_BALANCE', 'msa.MEMBER_REGISTRY', 'm.MEMBER_NAME', 'm.BANKING_ACCOUNT_NO')
            ->where('msa.SAVINGS_PRODUCT_ID', self::PRODUCT_NTR)
            ->where('msa.ACTIVE_ACCOUNT_ID', 1)
            ->get()->toArray();
    }

    public static function loadTagihanSSBJatemToday(): array
    {
        return DB::table('mu_savings_account as msa')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'msa.MEMBER_REGISTRY')
            ->select('msa.SAVINGS_ACCOUNT_NO', 'msa.SAVINGS_BALANCE', 'msa.MEMBER_REGISTRY', 'm.MEMBER_NAME', 'm.BANKING_ACCOUNT_NO')
            ->where('msa.ACTIVE_ACCOUNT_ID', 1)
            ->get()->toArray();
    }

    public static function bayarJasaSetiaPlus(array $data, string $username): bool
    {
        $noAcc = $data['edtNoacc'] ?? $data['edtNoAcc'] ?? '';
        $ok = DB::table('mu_trans_setiaplus_savings')->insert([
            'TRANSACTION_ID'     => 0,
            'MEMBER_REGISTRY'    => $data['idAgt'] ?? '',
            'SAVINGS_ACCOUNT_NO' => $noAcc,
            'AMOUNT'             => (float) ($data['amount'] ?? 0),
            'BALANCE_AFTER'      => 0,
            'TRANSACTION_DATE'   => now()->toDateTimeString(),
            'LOGTIME'            => now()->toDateTimeString(),
        ]);
        return $ok !== false;
    }

    // ===== Helper kecil =====

    public static function cekAnggotaNTR(string $memberRegistry): array
    {
        return DB::table('mu_trans_ntr_savings')
            ->where('MEMBER_REGISTRY', $memberRegistry)
            ->orderByDesc('TRANSACTION_DATE')
            ->get()->toArray();
    }

    public static function besarSimpWajibPokok(): ?array
    {
        return SimpananProduct::where('SAVINGS_PRODUCT_ID', self::PRODUCT_NTR)->first()?->toArray();
    }

    public static function cekSimpWajibAkum(string $noKtp): ?array
    {
        $acc = DB::table('mu_savings_account as sa')->where('sa.MEMBER_REGISTRY', $noKtp)->first();
        return $acc ? (array) $acc : null;
    }

    public static function checkTransNtrFirst(string $idAgt): ?array
    {
        $row = DB::table('mu_trans_ntr_savings')->where('MEMBER_REGISTRY', $idAgt)->first();
        return $row ? (array) $row : null;
    }

    // ===== Laporan =====

    public static function lapPembukaanAkunSimhar(?string $tglAwal, ?string $tglAkhir): array
    {
        return self::lapPembukaanBase(self::PRODUCT_HARIAN, $tglAwal, $tglAkhir);
    }

    public static function lapPembukaanAkunNTR(?string $tglAwal, ?string $tglAkhir): array
    {
        return self::lapPembukaanBase(self::PRODUCT_NTR, $tglAwal, $tglAkhir);
    }

    public static function lapPembukaanAkunSSB(?string $tglAwal, ?string $tglAkhir): array
    {
        return DB::table('mu_savings_account as sa')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('sa.SAVINGS_ACCOUNT_NO', 'sa.LOGTIME', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('sa.SAVINGS_PRODUCT_ID', '<>', self::PRODUCT_HARIAN)
            ->where('sa.SAVINGS_PRODUCT_ID', '<>', self::PRODUCT_NTR)
            ->where('sa.ACTIVE_ACCOUNT_ID', 1)
            ->when($tglAwal, fn ($q) => $q->whereDate('sa.LOGTIME', '>=', $tglAwal))
            ->when($tglAkhir, fn ($q) => $q->whereDate('sa.LOGTIME', '<=', $tglAkhir))
            ->orderBy('sa.LOGTIME')
            ->get()->toArray();
    }

    protected static function lapPembukaanBase(string $productId, ?string $tglAwal, ?string $tglAkhir): array
    {
        return DB::table('mu_savings_account as sa')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('sa.SAVINGS_ACCOUNT_NO', 'sa.LOGTIME', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('sa.SAVINGS_PRODUCT_ID', $productId)
            ->where('sa.ACTIVE_ACCOUNT_ID', 1)
            ->when($tglAwal, fn ($q) => $q->whereDate('sa.LOGTIME', '>=', $tglAwal))
            ->when($tglAkhir, fn ($q) => $q->whereDate('sa.LOGTIME', '<=', $tglAkhir))
            ->orderBy('sa.LOGTIME')
            ->get()->toArray();
    }

    public static function lapPenutupanAkunSimhar(?string $tglAwal, ?string $tglAkhir): array
    {
        return self::lapPenutupanBase($tglAwal, $tglAkhir);
    }

    public static function lapPenutupanAkunSSB(?string $tglAwal, ?string $tglAkhir): array
    {
        return self::lapPenutupanBase($tglAwal, $tglAkhir);
    }

    protected static function lapPenutupanBase(?string $tglAwal, ?string $tglAkhir): array
    {
        return DB::table('mu_savings_account as sa')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('sa.SAVINGS_ACCOUNT_NO', 'sa.SAVINGS_BALANCE', 'sa.LOGTIME', 'm.MEMBER_NAME')
            ->where('sa.ACTIVE_ACCOUNT_ID', 0)
            ->when($tglAwal, fn ($q) => $q->whereDate('sa.LOGTIME', '>=', $tglAwal))
            ->when($tglAkhir, fn ($q) => $q->whereDate('sa.LOGTIME', '<=', $tglAkhir))
            ->orderBy('sa.LOGTIME')
            ->get()->toArray();
    }

    public static function lapSaldoSimpRekap(?string $tglAwal, ?string $tglAkhir, int $status = 0): array
    {
        return DB::table('mu_trans_daily_savings as t')
            ->leftJoin('mu_savings_account as sa', 'sa.SAVINGS_ACCOUNT_NO', '=', 't.SAVINGS_ACCOUNT_NO')
            ->select(
                't.TRANSACTION_DATE',
                DB::raw('SUM(t.AMOUNT) as TOTAL_AMOUNT'),
                DB::raw('COUNT(t.TRANS_ID) as JML_TRANS'),
            )
            ->where('sa.ACTIVE_ACCOUNT_ID', 1)
            ->when($tglAwal, fn ($q) => $q->whereDate('t.TRANSACTION_DATE', '>=', $tglAwal))
            ->when($tglAkhir, fn ($q) => $q->whereDate('t.TRANSACTION_DATE', '<=', $tglAkhir))
            ->groupBy('t.TRANSACTION_DATE')
            ->orderBy('t.TRANSACTION_DATE')
            ->get()->toArray();
    }

    public static function lapSaldoSimpRinci(?string $tglAwal, ?string $tglAkhir, int $status = 0): array
    {
        return DB::table('mu_trans_daily_savings as t')
            ->leftJoin('mu_savings_account as sa', 'sa.SAVINGS_ACCOUNT_NO', '=', 't.SAVINGS_ACCOUNT_NO')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('t.TRANSACTION_DATE', 't.SAVINGS_ACCOUNT_NO', 'm.MEMBER_NAME', 't.AMOUNT', 't.BALANCE_AFTER')
            ->when($tglAwal, fn ($q) => $q->whereDate('t.TRANSACTION_DATE', '>=', $tglAwal))
            ->when($tglAkhir, fn ($q) => $q->whereDate('t.TRANSACTION_DATE', '<=', $tglAkhir))
            ->orderBy('t.TRANSACTION_DATE')
            ->get()->toArray();
    }

    public static function lapBukuBesarSSB(string $noAcc): array
    {
        return DB::table('mu_trans_ssb_savings as t')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 't.MEMBER_REGISTRY')
            ->select('t.*', 'm.MEMBER_NAME')
            ->where('t.SAVINGS_ACCOUNT_NO', $noAcc)
            ->orderByDesc('t.TRANSACTION_DATE')
            ->get()->toArray();
    }

    public static function lapBukuBesarNTR(string $idAgt): array
    {
        return DB::table('mu_trans_ntr_savings as t')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 't.MEMBER_REGISTRY')
            ->select('t.*', 'm.MEMBER_NAME')
            ->where('t.MEMBER_REGISTRY', $idAgt)
            ->orderByDesc('t.TRANSACTION_DATE')
            ->get()->toArray();
    }

    public static function lapMutasiSaldoNTRRekapSetor(?string $tglAwal, ?string $tglAkhir): array
    {
        return DB::table('mu_trans_ntr_savings as t')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 't.MEMBER_REGISTRY')
            ->select('m.MEMBER_NO', 'm.MEMBER_NAME', DB::raw('SUM(t.AMOUNT) as TOTAL_SETOR'))
            ->when($tglAwal, fn ($q) => $q->whereDate('t.TRANSACTION_DATE', '>=', $tglAwal))
            ->when($tglAkhir, fn ($q) => $q->whereDate('t.TRANSACTION_DATE', '<=', $tglAkhir))
            ->groupBy('m.MEMBER_NO', 'm.MEMBER_NAME')
            ->get()->toArray();
    }

    public static function lapMutasiSaldoNTRRekapTarik(?string $tglAwal, ?string $tglAkhir): array
    {
        return DB::table('mu_trans_ntr_savings as t')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 't.MEMBER_REGISTRY')
            ->select('m.MEMBER_NO', 'm.MEMBER_NAME', DB::raw('SUM(t.AMOUNT) as TOTAL_TARIK'))
            ->when($tglAwal, fn ($q) => $q->whereDate('t.TRANSACTION_DATE', '>=', $tglAwal))
            ->when($tglAkhir, fn ($q) => $q->whereDate('t.TRANSACTION_DATE', '<=', $tglAkhir))
            ->groupBy('m.MEMBER_NO', 'm.MEMBER_NAME')
            ->get()->toArray();
    }

    // ===== Laporan SSB / Jatuh Tempo =====
    //
    // Catatan: CI3 memakai kolom `SAVINGS_EXPIRED`, `MEMBER_PHONE`,
    // `MEMBER_HANDPHONE`, `pru_deposit_savings.PERIOD_MONTH_YEAR` —
    // semua TIDAK ADA di skema nyata `simkopukm`. Maka implementasi
    // versi "jatuh tempo" memakai proksi: akun SSB (SIM002) yang aktif,
    // saldo saat ini, dan tanggal transaksi terakhir (proxy jatuh tempo).

    /**
     * Laporan jatuh tempo SSB — rekap per akun.
     * Mengambil akun SSB aktif + saldo + transaksi terakhir sebagai proxy
     * tanggal jatuh tempo, per periode (tglAwal–tglAkhir terhadap LOGTIME).
     */
    public static function lapJatemSSBRekap(?string $tglAwal, ?string $tglAkhir, int $status = 0): array
    {
        return DB::table('mu_savings_account as sa')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->leftJoinSub(
                DB::table('mu_trans_ssb_savings as t')
                    ->select('t.SAVINGS_ACCOUNT_NO', DB::raw('MAX(t.TRANSACTION_DATE) as LAST_TRANS'))
                    ->groupBy('t.SAVINGS_ACCOUNT_NO'),
                'lasttrans', 'lasttrans.SAVINGS_ACCOUNT_NO', '=', 'sa.SAVINGS_ACCOUNT_NO'
            )
            ->select(
                'sa.SAVINGS_ACCOUNT_NO', 'sa.SAVINGS_BALANCE', 'sa.ACTIVE_ACCOUNT_ID',
                'm.MEMBER_NO', 'm.MEMBER_NAME', 'm.MEMBER_STATUS_ID',
                'lasttrans.LAST_TRANS',
            )
            ->where('sa.SAVINGS_PRODUCT_ID', self::PRODUCT_NTR)
            ->where('sa.ACTIVE_ACCOUNT_ID', 1)
            ->when($status, fn ($q) => $q->where('m.MEMBER_STATUS_ID', $status))
            ->when($tglAwal, fn ($q) => $q->whereDate('sa.LOGTIME', '>=', $tglAwal))
            ->when($tglAkhir, fn ($q) => $q->whereDate('sa.LOGTIME', '<=', $tglAkhir))
            ->orderBy('sa.SAVINGS_ACCOUNT_NO')
            ->get()->toArray();
    }

    /**
     * Laporan jatuh tempo SSB — rinci (semua akun, tidak filter status).
     */
    public static function lapJatemSSBRinci(?string $tglAwal, ?string $tglAkhir): array
    {
        return self::lapJatemSSBRekap($tglAwal, $tglAkhir, 0);
    }

    /**
     * Laporan daftar akun SSB (rekap) — agregat total saldo per status.
     * Proksi pengganti `pru_deposit_savings` yang tak ada di skema nyata.
     */
    public static function lapDaftarAkunRekapSSB(?string $tglAwal, ?string $tglAkhir, int $status = 0): array
    {
        $base = DB::table('mu_savings_account as sa')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select(
                'sa.SAVINGS_ACCOUNT_NO', 'sa.SAVINGS_BALANCE', 'sa.ACTIVE_ACCOUNT_ID',
                'm.MEMBER_NO', 'm.MEMBER_NAME', 'm.MEMBER_STATUS_ID',
            )
            ->where('sa.SAVINGS_PRODUCT_ID', self::PRODUCT_NTR)
            ->where('sa.ACTIVE_ACCOUNT_ID', 1)
            ->when($status, fn ($q) => $q->where('m.MEMBER_STATUS_ID', $status))
            ->when($tglAwal, fn ($q) => $q->whereDate('sa.LOGTIME', '>=', $tglAwal))
            ->when($tglAkhir, fn ($q) => $q->whereDate('sa.LOGTIME', '<=', $tglAkhir));

        $rows = $base->orderBy('sa.SAVINGS_ACCOUNT_NO')->get();

        $rekap = [
            'JML_AKUN'   => $rows->count(),
            'TOTAL_SALDO'=> $rows->sum('SAVINGS_BALANCE'),
            'MEMBER_STATUS_ID' => $status,
        ];
        return $rekap;
    }

    /**
     * Laporan daftar akun SSB (rinci) — daftar semua akun SSB aktif per periode.
     */
    public static function lapDaftarAkunRinciSSB(?string $tglAwal, ?string $tglAkhir, int $status = 0): array
    {
        return DB::table('mu_savings_account as sa')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select(
                'sa.SAVINGS_ACCOUNT_NO', 'sa.SAVINGS_BALANCE', 'sa.LOGTIME',
                'm.MEMBER_NO', 'm.MEMBER_NAME', 'm.MEMBER_STATUS_ID',
            )
            ->where('sa.SAVINGS_PRODUCT_ID', self::PRODUCT_NTR)
            ->where('sa.ACTIVE_ACCOUNT_ID', 1)
            ->when($status, fn ($q) => $q->where('m.MEMBER_STATUS_ID', $status))
            ->when($tglAwal, fn ($q) => $q->whereDate('sa.LOGTIME', '>=', $tglAwal))
            ->when($tglAkhir, fn ($q) => $q->whereDate('sa.LOGTIME', '<=', $tglAkhir))
            ->orderBy('sa.SAVINGS_ACCOUNT_NO')
            ->get()->toArray();
    }

    // =================================================================
    // ===== Batch 8: Proses Bisnis Lanjutan (method CI3 yang menulis
    //       ke kolom legacy yang tidak ada di skema nyata) ==========
    // =================================================================

    /**
     * updateTransSimp — update status/urutan transaksi di mu_transaction_account.
     * CI3 menulis mu_transaction_index (tidak ada di skema nyata), jadi versi
     * Laravel cukup update UPDATE_BY + LOGTIME (aman).
     */
    public static function updateTransSimp(array $data, string $username): bool
    {
        $transId = (int) ($data['transId'] ?? $data['transID'] ?? 0);
        if ($transId === 0) {
            return false;
        }
        return DB::table('mu_transaction_account')
            ->where('TRANSACTION_ID', $transId)
            ->update([
                'UPDATE_BY' => $username,
                'LOGTIME'   => now()->toDateTimeString(),
            ]) > 0;
    }

    /**
     * tarikAll — proses transaksi tarik semua saldo harian (alias prosesTransSimpHar).
     */
    public static function tarikAll(array $data, string $username): void
    {
        self::prosesTransSimpHar($data, $username, self::PRODUCT_HARIAN);
    }

    /**
     * prosesTransSimp — proses transaksi generik NTR (setor/tarik) saat membuka akun.
     * CI3 menulis kolom legacy (APPROVAL_CODE, CREDIT/DEBIT_MANDATORY,
     * CREDIT/DEBIT_PRINCIPAL, mc_transaction_code, mu_journal, dll) yang tidak
     * ada di skema nyata. Versi Laravel aman: tulis ke mu_transaction_account
     * + tabel transaksi produk dengan kolom nyata.
     * @return array hasil operasi
     */
    public static function prosesTransSimp(array $data, string $username): array
    {
        $noAcc    = (string) ($data['edtNoacc'] ?? $data['edtNoAcc'] ?? '');
        $transNo  = (string) ($data['edtNotrans'] ?? '');
        $jnsTrans = (int) ($data['cmbJnsTransaksi'] ?? 1);
        $setor    = $jnsTrans === 1 ? 'setor' : 'tarik';
        $amount   = (float) str_replace(['Rp. ', '.'], '', (string) ($data['edtSimpokok'] ?? 0));

        // 1) mu_transaction_account (kolom nyata)
        DB::table('mu_transaction_account')->insert([
            'TRANSACTION_NO'     => $transNo,
            'MEMBER_REGISTRY'    => $noAcc,
            'SAVINGS_ACCOUNT_NO' => $noAcc,
            'TRANSACTION_TYPE'   => $setor,
            'AMOUNT'             => $amount,
            'TRANSACTION_DATE'   => now()->toDateTimeString(),
            'STATUS_ID'          => 0,
            'APPROVAL_STATUS_ID' => 0,
            'UPDATE_BY'          => $username,
            'LOGTIME'            => now()->toDateTimeString(),
        ]);

        // 2) tabel transaksi produk NTR (kolom nyata)
        DB::table('mu_trans_ntr_savings')->insert([
            'TRANSACTION_ID'     => 0,
            'MEMBER_REGISTRY'    => $noAcc,
            'SAVINGS_ACCOUNT_NO' => $noAcc,
            'AMOUNT'             => $amount,
            'BALANCE_AFTER'      => 0,
            'TRANSACTION_DATE'   => now()->toDateTimeString(),
            'LOGTIME'            => now()->toDateTimeString(),
        ]);

        return ['transNo' => $transNo, 'noAcc' => $noAcc, 'amount' => $amount];
    }

    /**
     * setJurnalTrans — cari detail transaksi terakhir berdasarkan transID.
     * CI3: query tru_daily_savings (tidak ada di skema) → ganti tabel nyata.
     */
    public static function setJurnalTrans(array $data, string $typeTrans = 'TSH'): ?array
    {
        $transId = (int) ($data['transID'] ?? $data['transId'] ?? 0);
        $table = match ($typeTrans) {
            'TSN' => 'mu_trans_ntr_savings',
            'TSH' => 'mu_trans_daily_savings',
            'TSS' => 'mu_trans_setiaplus_savings',
            'TSD' => 'mu_trans_ssb_savings',
            default => 'mu_transaction_account',
        };
        $row = DB::table($table)->where('TRANSACTION_ID', $transId)->latest('TRANSACTION_DATE')->first();
        return $row ? (array) $row : null;
    }

    /**
     * setFalseApproval — batalkan approval (reject) transaksi.
     * Konvensi reject di skema nyata: APPROVAL_STATUS_ID = 0 + update auditor.
     */
    public static function setFalseApproval(string $transId, string $username): bool
    {
        return DB::table('mu_transaction_account')
            ->where('TRANSACTION_ID', $transId)
            ->update([
                'APPROVAL_STATUS_ID' => 0,
                'UPDATE_BY'          => $username,
                'LOGTIME'            => now()->toDateTimeString(),
            ]) > 0;
    }

    /**
     * prosesStatBuku / updateStatBuku — tandai transaksi selesai/dicetak buku.
     * Konvensi aman: STATUS_ID=1 (selesai) + update auditor.
     */
    public static function prosesStatBuku(array $data, string $username): bool
    {
        $transId = (int) ($data['transId'] ?? $data['transID'] ?? 0);
        if ($transId === 0) {
            return false;
        }
        return DB::table('mu_transaction_account')
            ->where('TRANSACTION_ID', $transId)
            ->update([
                'STATUS_ID' => 1,
                'UPDATE_BY' => $username,
                'LOGTIME'   => now()->toDateTimeString(),
            ]) > 0;
    }

    /**
     * updateStatBuku — alias prosesStatBuku (CI3 memakai method yang sama dua kali).
     */
    public static function updateStatBuku(array $data, string $username): bool
    {
        return self::prosesStatBuku($data, $username);
    }

    /**
     * checkSimPokok — cek apakah anggota sudah bayar simpanan pokok & wajib.
     */
    public static function checkSimPokok(array $data): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('mu_savings_account as sa', 'sa.MEMBER_REGISTRY', '=', 'm.MEMBER_REGISTRY')
            ->select('m.*', 'sa.SAVINGS_ACCOUNT_NO', 'sa.SAVINGS_BALANCE', 'sa.ACTIVE_ACCOUNT_ID')
            ->where('sa.MEMBER_REGISTRY', $data['idAgt'] ?? '')
            ->where('sa.SAVINGS_ACCOUNT_NO', $data['noAcc'] ?? '')
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * checkSaldoSimPokokWajib — cek saldo simpanan pokok & wajib.
     */
    public static function checkSaldoSimPokokWajib(array $data): ?array
    {
        $row = DB::table('ma_member as m')
            ->leftJoin('mu_savings_account as sa', 'sa.MEMBER_REGISTRY', '=', 'm.MEMBER_REGISTRY')
            ->select('m.*', 'sa.SAVINGS_ACCOUNT_NO', 'sa.SAVINGS_BALANCE', 'sa.ACTIVE_ACCOUNT_ID')
            ->where('m.MEMBER_REGISTRY', $data['noAcc'] ?? '')
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * updateSaldoBlokir — update status blokir akun (ACTIVE_ACCOUNT_ID).
     */
    public static function updateSaldoBlokir(string $noAcc, int $blokirStatus): bool
    {
        return DB::table('mu_savings_account')
            ->where('SAVINGS_ACCOUNT_NO', $noAcc)
            ->update([
                'ACTIVE_ACCOUNT_ID' => $blokirStatus,
                'LOGTIME'           => now()->toDateTimeString(),
            ]) > 0;
    }

    /**
     * doBayarJasaSSB — bayar jasa SSB untuk semua tagihan jatuh tempo hari ini.
     * Loop loadTagihanSSBJatemToday() dan insert ke mu_trans_deposit_interest.
     * @return int jumlah tagihan yang dibayar
     */
    public static function doBayarJasaSSB(array $data, string $username): int
    {
        $tagihan  = array_map(fn ($r) => (array) $r, self::loadTagihanSSBJatemToday());
        $tglBayar = $data['dtpTglBayar'] ?? date('Y-m-d');
        $now      = now()->toDateTimeString();
        $count    = 0;
        foreach ($tagihan as $t) {
            DB::table('mu_trans_deposit_interest')->insert([
                'MEMBER_REGISTRY'    => $t['MEMBER_REGISTRY'] ?? '',
                'SAVINGS_ACCOUNT_NO' => $t['SAVINGS_ACCOUNT_NO'] ?? null,
                'INTEREST_AMOUNT'    => (float) ($t['nominal_INTEREST_HOLDING'] ?? 0),
                'INTEREST_DATE'      => $tglBayar,
                'LOGTIME'            => $now,
            ]);
            $count++;
        }
        return $count;
    }

    /**
     * doBayarJasaSetiaplus — bayar jasa SetiaPlus untuk semua tagihan jatuh tempo hari ini.
     * Loop loadTagihanSetPlusJatemToday() dan insert ke mu_trans_setiaplus_interest.
     * @return int jumlah tagihan yang dibayar
     */
    public static function doBayarJasaSetiaplus(array $data, string $username): int
    {
        $tagihan  = array_map(fn ($r) => (array) $r, self::loadTagihanSetPlusJatemToday());
        $tglBayar = $data['dtpTglBayar'] ?? date('Y-m-d');
        $now      = now()->toDateTimeString();
        $count    = 0;
        foreach ($tagihan as $t) {
            DB::table('mu_trans_setiaplus_interest')->insert([
                'MEMBER_REGISTRY'    => $t['MEMBER_REGISTRY'] ?? '',
                'INTEREST_AMOUNT'    => (float) ($t['nominal_INTEREST_HOLDING'] ?? 0),
                'INTEREST_DATE'      => $tglBayar,
                'LOGTIME'            => $now,
            ]);
            $count++;
        }
        return $count;
    }

    /**
     * addMasterSimpananSSB — buka/update akun SSB di mu_savings_account.
     * Sisa dari catatan batch 4: form master SSB masih placeholder.
     */
    public static function addMasterSimpananSSB(
        string $noAcc, string $member, int $active = 1, float $balance = 0.0
    ): bool {
        return DB::table('mu_savings_account')->updateOrInsert(
            ['SAVINGS_ACCOUNT_NO' => $noAcc],
            [
                'MEMBER_REGISTRY'    => $member,
                'SAVINGS_PRODUCT_ID' => self::PRODUCT_NTR,
                'ACTIVE_ACCOUNT_ID'  => $active,
                'SAVINGS_BALANCE'    => $balance,
                'LOGTIME'            => now()->toDateTimeString(),
            ]
        );
    }

    /**
     * addMasterSimpananSetiaPlus — buka/update akun SetiaPlus di mu_savings_account.
     * Sisa dari catatan batch 4: form master SetiaPlus masih placeholder.
     */
    public static function addMasterSimpananSetiaPlus(
        string $noAcc, string $member, int $active = 1, float $balance = 0.0
    ): bool {
        return DB::table('mu_savings_account')->updateOrInsert(
            ['SAVINGS_ACCOUNT_NO' => $noAcc],
            [
                'MEMBER_REGISTRY'    => $member,
                'SAVINGS_PRODUCT_ID' => self::PRODUCT_NTR,
                'ACTIVE_ACCOUNT_ID'  => $active,
                'SAVINGS_BALANCE'    => $balance,
                'LOGTIME'            => now()->toDateTimeString(),
            ]
        );
    }

    // =================================================================
    // ===== Batch 9: Supporting / Utility (method view-only +
    //       lookup, semua aman terhadap skema nyata) ================
    // =================================================================

    /**
     * searchDataNoOrName — cari anggota berdasarkan NO / NAMA / NO_KTP.
     * CI3: query ma_member pakai kolom legacy (MEMBER_CARD_ID, MEMBER_STATUS_ID 2/4/10,
     * MA_SUMMARY_CLOSE, mc_transaction_code, dll) yang tidak ada di skema nyata.
     * Versi Laravel: pakai kolom nyata. Type = variant filter.
     *
     * @param string $searchKey  nilai pencarian (NO_KTP, MEMBER_NO, MEMBER_NAME, atau SAVINGS_ACCOUNT_NO)
     * @param string $type       "Harian"|"tutupSimhar"|"NTR"|"SSB"|"setPlus"|"Pinjaman"|"potGaji"|"default"
     */
    public static function searchDataNoOrName(string $searchKey, string $type = 'default'): ?array
    {
        $base = DB::table('ma_member as m')
            ->leftJoin('mu_savings_account as sa', 'sa.MEMBER_REGISTRY', '=', 'm.MEMBER_REGISTRY')
            ->select('m.*', 'sa.SAVINGS_ACCOUNT_NO', 'sa.SAVINGS_PRODUCT_ID', 'sa.SAVINGS_BALANCE', 'sa.ACTIVE_ACCOUNT_ID');

        $where = fn ($q) => $q
            ->where('m.MEMBER_REGISTRY', $searchKey)
            ->orWhere('m.MEMBER_NO', $searchKey)
            ->orWhere('m.MEMBER_NAME', 'like', '%' . $searchKey . '%');

        $filter = match (strtolower($type)) {
            'hariari', 'default' => fn ($q) => $q,
            'tutupsimhar' => fn ($q) => $q->where('sa.ACTIVE_ACCOUNT_ID', 1)->where('sa.SAVINGS_PRODUCT_ID', self::PRODUCT_HARIAN),
            'ntr' => fn ($q) => $q->where('sa.ACTIVE_ACCOUNT_ID', 1)->where('sa.SAVINGS_PRODUCT_ID', self::PRODUCT_NTR),
            'ssb', 'setplus' => fn ($q) => $q->where('sa.ACTIVE_ACCOUNT_ID', 1),
            'pinjaman' => fn ($q) => $q->whereIn('m.MEMBER_STATUS_ID', [2, 4]),
            'potgaji' => fn ($q) => $q->where('m.member_management_id', '!=', 0),
            default => fn ($q) => $q,
        };

        $row = $base->where($where)->when($type, $filter)->first();
        return $row ? (array) $row : null;
    }

    /**
     * checkMemberActive — cek apakah anggota status aktif (NON-resmi).
     * CI3: pakai MEMBER_CARD_ID / MEMBER_STATUS_ID 1,3,9 (kolom legacy).
     * Versi Laravel: pakai kolom nyata MEMBER_REGISTRY / MEMBER_STATUS_ID.
     * Return: true jika masih aktif (resmi), false jika belum.
     */
    public static function checkMemberActive(string $searchKey): bool
    {
        $row = DB::table('ma_member')
            ->where('MEMBER_REGISTRY', $searchKey)
            ->orWhere('MEMBER_NO', $searchKey)
            ->first();
        return $row !== null && in_array((int) ($row->MEMBER_STATUS_ID ?? 0), [1, 3, 9]);
    }

    /**
     * searchDataNoOrNameTrans — lookup data transaksi berdasarkan anggota.
     * CI3: query tru_* (kolom legacy). Versi Laravel: pakai tabel nyata.
     * @param string $searchKey
     * @param string $type "NTR"|"Harian"|"SetiaPlus"|"SSB"|"default"
     */
    public static function searchDataNoOrNameTrans(string $searchKey, string $type = 'default'): ?array
    {
        $table = match (strtolower($type)) {
            'ntr' => 'mu_trans_ntr_savings',
            'harian' => 'mu_trans_daily_savings',
            'setiaplus' => 'mu_trans_setiaplus_savings',
            'ssb' => 'mu_trans_ssb_savings',
            default => 'mu_transaction_account',
        };
        $row = DB::table($table . ' as t')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 't.MEMBER_REGISTRY')
            ->select('m.*', 't.TRANSACTION_ID', 't.AMOUNT', 't.TRANSACTION_DATE')
            ->where('m.MEMBER_REGISTRY', $searchKey)
            ->orWhere('m.MEMBER_NO', $searchKey)
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * searchTransactionByTransId — cari transaksi per transID di tabel produk.
     * @param int $transId
     * @param string $typeTrans "TSH"|"TSN"|"TSS"|"TSD"|"default"
     */
    public static function searchTransactionByTransId(int $transId, string $typeTrans = 'default'): ?array
    {
        $table = match (strtoupper($typeTrans)) {
            'TSH' => 'mu_trans_daily_savings',
            'TSN' => 'mu_trans_ntr_savings',
            'TSS' => 'mu_trans_setiaplus_savings',
            'TSD' => 'mu_trans_ssb_savings',
            default => 'mu_transaction_account',
        };
        $row = DB::table($table)->where('TRANSACTION_ID', $transId)->first();
        return $row ? (array) $row : null;
    }

    /**
     * menuKasie — data pendukung dashboard Kasie.
     * Return: total akun aktif + total saldo + daftar transaksi belum approval.
     */
    public static function menuKasieData(): array
    {
        $akunAktif = DB::table('mu_savings_account')->where('ACTIVE_ACCOUNT_ID', 1)->count();
        $totalSaldo = (float) (DB::table('mu_savings_account')->where('ACTIVE_ACCOUNT_ID', 1)->sum('SAVINGS_BALANCE') ?? 0);
        $transBelum = DB::table('mu_transaction_account')
            ->where('APPROVAL_STATUS_ID', 0)
            ->orderByDesc('TRANSACTION_DATE')
            ->limit(20)
            ->get()->toArray();
        return [
            'JML_AKUN_AKTIF'   => $akunAktif,
            'TOTAL_SALDO'      => $totalSaldo,
            'TRANS_BELUM_APPROVAL' => array_map(fn ($r) => (array) $r, $transBelum),
        ];
    }

    /**
     * menuKasirData — data pendukung menu Kasir.
     * Return: daftar transaksi hari ini per type (setor/tarik).
     */
    public static function menuKasirData(string $tglHariIni = null): array
    {
        $tgl = $tglHariIni ?: now()->toDateString();
        $setor = DB::table('mu_transaction_account')
            ->whereDate('TRANSACTION_DATE', $tgl)
            ->where('TRANSACTION_TYPE', 'like', '%setor%')
            ->get()->toArray();
        $tarik = DB::table('mu_transaction_account')
            ->whereDate('TRANSACTION_DATE', $tgl)
            ->where('TRANSACTION_TYPE', 'like', '%tarik%')
            ->get()->toArray();
        return [
            'tgl'          => $tgl,
            'SETOR_HARI_INI'  => array_map(fn ($r) => (array) $r, $setor),
            'TARIK_HARI_INI'  => array_map(fn ($r) => (array) $r, $tarik),
        ];
    }

    /**
     * menuPjSimpananData — data PJ Simpanan: daftar approval pending.
     */
    public static function menuPjSimpananData(): array
    {
        return DB::table('mu_transaction_account as t')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 't.MEMBER_REGISTRY')
            ->select('t.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('t.APPROVAL_STATUS_ID', 0)
            ->orderByDesc('t.TRANSACTION_DATE')
            ->limit(50)
            ->get()
            ->map(fn ($r) => (array) $r)
            ->all();
    }

    /**
     * viewBI — display info transaksi (BU/Debit Nota).
     * CI3: query tru_daily_savings + mc_transaction_code (tidak ada di skema).
     * Versi Laravel: query mu_transaction_account + ma_member.
     */
    public static function viewBI(int $transId): ?array
    {
        $row = DB::table('mu_transaction_account as t')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 't.MEMBER_REGISTRY')
            ->select('t.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('t.TRANSACTION_ID', $transId)
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * viewPersetujuanData — data untuk view persetujuan (transaksi belum approval).
     */
    public static function viewPersetujuanData(): array
    {
        return self::getTransactionTodayApproval();
    }

    /**
     * showTableBuktiKasMasuk — data BKM (setoran tunai).
     * CI3: query mu_journal (tidak ada di skema) → pakai mu_transaction_account.
     */
    public static function showTableBuktiKasMasuk(string $tglHariIni = null): array
    {
        $tgl = $tglHariIni ?: now()->toDateString();
        return DB::table('mu_transaction_account as t')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 't.MEMBER_REGISTRY')
            ->select('t.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->whereDate('t.TRANSACTION_DATE', $tgl)
            ->where('t.TRANSACTION_TYPE', 'like', '%setor%')
            ->orderByDesc('t.TRANSACTION_DATE')
            ->get()
            ->map(fn ($r) => (array) $r)
            ->all();
    }

    /**
     * showTableBuktiKasKeluar — data BKK (penarikan tunai).
     */
    public static function showTableBuktiKasKeluar(string $tglHariIni = null): array
    {
        $tgl = $tglHariIni ?: now()->toDateString();
        return DB::table('mu_transaction_account as t')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 't.MEMBER_REGISTRY')
            ->select('t.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->whereDate('t.TRANSACTION_DATE', $tgl)
            ->where('t.TRANSACTION_TYPE', 'like', '%tarik%')
            ->orderByDesc('t.TRANSACTION_DATE')
            ->get()
            ->map(fn ($r) => (array) $r)
            ->all();
    }

    /**
     * tampilLaporanPD — laporan pengunduran diri (filter per tanggal).
     * CI3: query ma_summary_close (tidak ada di skema nyata).
     * Versi Laravel: pakai mu_savings_account yang telah di-close (ACTIVE_ACCOUNT_ID=0).
     */
    public static function tampilLaporanPD(?string $tglAwal = null, ?string $tglAkhir = null): array
    {
        return DB::table('mu_savings_account as sa')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('sa.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('sa.ACTIVE_ACCOUNT_ID', 0)
            ->when($tglAwal, fn ($q) => $q->whereDate('sa.LOGTIME', '>=', $tglAwal))
            ->when($tglAkhir, fn ($q) => $q->whereDate('sa.LOGTIME', '<=', $tglAkhir))
            ->orderByDesc('sa.LOGTIME')
            ->get()
            ->map(fn ($r) => (array) $r)
            ->all();
    }

    /**
     * tampilLapPembekuan — laporan pembekuan akun (semua aktif=0).
     * Sama seperti tampilLaporanPD tapi tanpa filter tanggal (opsional).
     */
    public static function tampilLapPembekuan(): array
    {
        return DB::table('mu_savings_account as sa')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'sa.MEMBER_REGISTRY')
            ->select('sa.*', 'm.MEMBER_NAME', 'm.MEMBER_NO')
            ->where('sa.ACTIVE_ACCOUNT_ID', 0)
            ->orderByDesc('sa.LOGTIME')
            ->get()
            ->map(fn ($r) => (array) $r)
            ->all();
    }

    /**
     * pengajuanSimpananData — data untuk form pengajuan simpanan.
     * CI3: view-only (tanpa logic). Versi Laravel: return data terkait.
     */
    public static function pengajuanSimpananData(): array
    {
        $produk = SimpananProduct::all()->map(fn ($p) => $p->toArray())->all();
        $anggotaAktif = DB::table('ma_member as m')
            ->where('m.ACTIVE_STATUS_ID', 1)
            ->select('m.MEMBER_REGISTRY', 'm.MEMBER_NO', 'm.MEMBER_NAME')
            ->get()->map(fn ($r) => (array) $r)->all();
        return [
            'PRODUK_SIMPANAN'  => $produk,
            'ANGGOTA_AKTIF'    => $anggotaAktif,
        ];
    }
}
