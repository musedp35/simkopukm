<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Uji runtime pembukaan akun simpanan harian (addMasterSimpananHarian).
 * Setiap test rollback setelah selesai agar DB tidak terpengaruh.
 */
class SavingsAccountOpeningTest extends TestCase
{
    public function test_open_new_account_generates_and_inserts(): void
    {
        DB::beginTransaction();
        try {
            // Ambil no. akun yang akan dibuat (seharusnya SAV0002 karena SAV0001 ada)
            $no = \App\Services\SavingsService::loadAccNumber('MEM002')['noAcc'];
            $this->assertMatchesRegularExpression('/^SAV\d{4}$/', $no);

            $ok = \App\Services\SavingsService::addMasterSimpananHarian(
                $no, 'MEM002', 1, 0.0, 'tester'
            );
            $this->assertTrue($ok, 'insert akun baru harus sukses');

            $acc = DB::table('mu_savings_account')->where('SAVINGS_ACCOUNT_NO', $no)->first();
            $this->assertNotNull($acc, 'akun harus tercatat di DB');
            $this->assertSame('MEM002', $acc->MEMBER_REGISTRY);
            $this->assertSame(\App\Services\SavingsService::PRODUCT_HARIAN, $acc->SAVINGS_PRODUCT_ID);
            $this->assertSame(1, (int) $acc->ACTIVE_ACCOUNT_ID);

        } finally {
            DB::rollBack();
        }
    }

    public function test_open_existing_account_updates_not_duplicates(): void
    {
        DB::beginTransaction();
        try {
            // SAV0001 sudah ada di seed → update harus sukses, tidak gagal
            $ok = \App\Services\SavingsService::addMasterSimpananHarian(
                'SAV0001', 'MEM001', 0, 50000.0, 'tester'
            );
            $this->assertTrue($ok, 'update akun yang sudah ada harus sukses');

            $count = DB::table('mu_savings_account')->where('SAVINGS_ACCOUNT_NO', 'SAV0001')->count();
            $this->assertSame(1, $count, 'tidak boleh duplikat');

            $acc = DB::table('mu_savings_account')->where('SAVINGS_ACCOUNT_NO', 'SAV0001')->first();
            $this->assertSame(0, (int) $acc->ACTIVE_ACCOUNT_ID);
            $this->assertSame(50000.0, (float) $acc->SAVINGS_BALANCE);

        } finally {
            DB::rollBack();
        }
    }
}
