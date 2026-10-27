<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $hash = Hash::make('admin123');

        DB::table('ma_role')->insertOrIgnore([
            ['ROLE_ID' => 'A', 'ROLE_NAME' => 'Admin'],
            ['ROLE_ID' => 'D', 'ROLE_NAME' => 'Dispatcher'],
            ['ROLE_ID' => 'M', 'ROLE_NAME' => 'Manager'],
            ['ROLE_ID' => 'P', 'ROLE_NAME' => 'Penanggung Jawab'],
        ]);

        DB::table('ma_module')->insertOrIgnore([
            ['MODULE_ID' => '01', 'MODULE_NAME' => 'Master'],
            ['MODULE_ID' => '02', 'MODULE_NAME' => 'Savings'],
            ['MODULE_ID' => '03', 'MODULE_NAME' => 'Loan'],
            ['MODULE_ID' => '04', 'MODULE_NAME' => 'Report'],
        ]);

        DB::table('ma_submodule')->insertOrIgnore([
            ['SUBMODULE_ID' => '0101', 'SUBMODULE_NAME' => 'Member', 'MODULE_ID' => '01'],
            ['SUBMODULE_ID' => '0102', 'SUBMODULE_NAME' => 'Saving Account', 'MODULE_ID' => '01'],
            ['SUBMODULE_ID' => '0201', 'SUBMODULE_NAME' => 'Savings Transaction', 'MODULE_ID' => '02'],
            ['SUBMODULE_ID' => '0301', 'SUBMODULE_NAME' => 'Loan Approval', 'MODULE_ID' => '03'],
            ['SUBMODULE_ID' => '0401', 'SUBMODULE_NAME' => 'Member Report', 'MODULE_ID' => '04'],
        ]);

        DB::table('ma_member')->insertOrIgnore([
            ['MEMBER_REGISTRY' => 'MEM001', 'MEMBER_NO' => 'M000001', 'MEMBER_NAME' => 'Budi Santoso', 'ACTIVE_STATUS_ID' => 1, 'LOGTIME' => now()->format('Y-m-d H:i:s')],
            ['MEMBER_REGISTRY' => 'MEM002', 'MEMBER_NO' => 'M000002', 'MEMBER_NAME' => 'Siti Aminah', 'ACTIVE_STATUS_ID' => 1, 'LOGTIME' => now()->format('Y-m-d H:i:s')],
            ['MEMBER_REGISTRY' => 'MEM003', 'MEMBER_NO' => 'M000003', 'MEMBER_NAME' => 'Ahmad Fauzi', 'ACTIVE_STATUS_ID' => 1, 'LOGTIME' => now()->format('Y-m-d H:i:s')],
            ['MEMBER_REGISTRY' => 'MEM004', 'MEMBER_NO' => 'M000004', 'MEMBER_NAME' => 'Dewi Lestari', 'ACTIVE_STATUS_ID' => 1, 'LOGTIME' => now()->format('Y-m-d H:i:s')],
        ]);

        DB::table('ma_user')->insertOrIgnore([
            ['MEMBER_REGISTRY' => 'ADMIN001', 'ROLE_ID' => 'A', 'USERNAME' => 'admin', 'USER_STATUS' => 1, 'PASSWORD' => $hash, 'LOGTIME' => now()->format('Y-m-d H:i:s')],
            ['MEMBER_REGISTRY' => 'MEM001', 'ROLE_ID' => 'M', 'USERNAME' => 'budi', 'USER_STATUS' => 1, 'PASSWORD' => $hash, 'LOGTIME' => now()->format('Y-m-d H:i:s')],
        ]);

        DB::table('mu_simpanan_product')->insertOrIgnore([
            ['SAVINGS_PRODUCT_ID' => 'SIM001', 'PRODUCT_NAME' => 'Simpanan Harian', 'INTEREST_RATE' => 0.05, 'MIN_BALANCE' => 100000],
            ['SAVINGS_PRODUCT_ID' => 'SIM002', 'PRODUCT_NAME' => 'Nest Reno Tambah', 'INTEREST_RATE' => 0.04, 'MIN_BALANCE' => 50000],
        ]);

        DB::table('mu_loan_product')->insertOrIgnore([
            ['LOAN_PRODUCT_ID' => 'LOAN001', 'PRODUCT_NAME' => 'Kredit Mikro', 'INTEREST_RATE' => 0.10, 'MAX_AMOUNT' => 50000000, 'DURATION_MONTHS' => 24],
            ['LOAN_PRODUCT_ID' => 'LOAN002', 'PRODUCT_NAME' => 'Kredit Modal Kerja', 'INTEREST_RATE' => 0.08, 'MAX_AMOUNT' => 100000000, 'DURATION_MONTHS' => 36],
        ]);

        DB::table('mu_savings_account')->insertOrIgnore([
            ['SAVINGS_ACCOUNT_NO' => 'SAV0001', 'MEMBER_REGISTRY' => 'MEM001', 'SAVINGS_PRODUCT_ID' => 'SIM001', 'ACTIVE_ACCOUNT_ID' => 1, 'SAVINGS_BALANCE' => 0, 'LOGTIME' => now()->format('Y-m-d H:i:s')],
        ]);

        echo "Database seeder completed successfully." . PHP_EOL;
    }
}
