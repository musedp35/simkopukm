<?php

namespace Tests\Feature;

use App\Services\SavingsService as S;
use Tests\TestCase;

/**
 * Verifikasi semua method lookup/detail di SavingsService tidak throw
 * (karena DB::table()->first() return stdClass yang tak punya toArray).
 */
class SavingsLookupMethodsTest extends TestCase
{
    public function test_all_lookup_methods_return_array_or_null(): void
    {
        $tests = [
            fn() => S::detailSimHar('MEM001'),
            fn() => S::detailSSB('MEM001'),
            fn() => S::detailSetPlus('MEM001'),
            fn() => S::detailPenutupanSimHar('SAV0001'),
            fn() => S::detailPenutupanSSB('SAV0001'),
            fn() => S::detailTransSimHar('SAV0001'),
            fn() => S::detailTransNTR('MEM001'),
            fn() => S::findNoAccSimpHar('SAV0001'),
            fn() => S::findNoAccNTR('SAV0001'),
            fn() => S::findNoAccSSB('SAV0001'),
            fn() => S::findNoAccSimpHarSaldoExist('SAV0001'),
            fn() => S::findNoAgtNTR('MEM001'),
            fn() => S::getDataAnggotaSimpanan('MEM001','SAV0001'),
            fn() => S::getDataAnggotaSimpananSSB('MEM001','SAV0001'),
            fn() => S::getDataAnggotaSimpananSetiaPlus('MEM001'),
            fn() => S::getDataAnggotaSimpananClosing('MEM001'),
            fn() => S::getIdTrans(['edtNoacc'=>'SAV0001']),
            fn() => S::cekSimpWajibAkum('MEM001'),
            fn() => S::checkTransNtrFirst('MEM001'),
            fn() => S::getApprovedTransaksi(1),
            fn() => S::checkNoUrutSimpanan('MEM001'),
            fn() => S::loadAccNumber('MEM001'),
        ];

        foreach ($tests as $i => $fn) {
            $result = $fn();
            $this->assertTrue(
                $result === null || is_array($result),
                "method index {$i} harus return array|null, got: " . gettype($result)
            );
        }
    }
}
