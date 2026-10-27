<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        // ==================== IDEMPOTENCY CHECK ====================
        // If loan data already exists, skip all transactional inserts (member, loan, savings)
        $loanExists = DB::table('mu_loan_account')->where('LOAN_ACCOUNT_NO', 'CR20260001')->exists();
        $skipTransactional = $loanExists;

        // ==================== MASTER DATA ====================
        DB::table('ma_role')->insertOrIgnore([
            ['ROLE_ID' => 'A', 'ROLE_NAME' => 'Admin', 'LOGTIME' => $now],
            ['ROLE_ID' => 'D', 'ROLE_NAME' => 'Dispatcher', 'LOGTIME' => $now],
            ['ROLE_ID' => 'M', 'ROLE_NAME' => 'Manager', 'LOGTIME' => $now],
            ['ROLE_ID' => 'P', 'ROLE_NAME' => 'Penanggung Jawab', 'LOGTIME' => $now],
            ['ROLE_ID' => 'AO3', 'ROLE_NAME' => 'AO3', 'LOGTIME' => $now],
            ['ROLE_ID' => 'AO4', 'ROLE_NAME' => 'AO4', 'LOGTIME' => $now],
            ['ROLE_ID' => 'AO6', 'ROLE_NAME' => 'AO6', 'LOGTIME' => $now],
            ['ROLE_ID' => 'KASIE', 'ROLE_NAME' => 'Kasie', 'LOGTIME' => $now],
        ]);

        DB::table('ma_module')->insertOrIgnore([
            ['MODULE_ID' => '01', 'MODULE_NAME' => 'Master', 'LOGTIME' => $now],
            ['MODULE_ID' => '02', 'MODULE_NAME' => 'Savings', 'LOGTIME' => $now],
            ['MODULE_ID' => '03', 'MODULE_NAME' => 'Loan', 'LOGTIME' => $now],
            ['MODULE_ID' => '04', 'MODULE_NAME' => 'Report', 'LOGTIME' => $now],
        ]);

        DB::table('ma_submodule')->insertOrIgnore([
            ['SUBMODULE_ID' => '0101', 'SUBMODULE_NAME' => 'Member', 'MODULE_ID' => '01', 'LOGTIME' => $now],
            ['SUBMODULE_ID' => '0102', 'SUBMODULE_NAME' => 'Saving Account', 'MODULE_ID' => '01', 'LOGTIME' => $now],
            ['SUBMODULE_ID' => '0201', 'SUBMODULE_NAME' => 'Savings Transaction', 'MODULE_ID' => '02', 'LOGTIME' => $now],
            ['SUBMODULE_ID' => '0301', 'SUBMODULE_NAME' => 'Loan Approval', 'MODULE_ID' => '03', 'LOGTIME' => $now],
            ['SUBMODULE_ID' => '0401', 'SUBMODULE_NAME' => 'Member Report', 'MODULE_ID' => '04', 'LOGTIME' => $now],
        ]);

        DB::table('ma_member_status')->insertOrIgnore([
            ['MEMBER_STATUS_ID' => '1', 'MEMBER_STATUS_NAME' => 'Aktif'],
            ['MEMBER_STATUS_ID' => '2', 'MEMBER_STATUS_NAME' => 'Bekukan'],
            ['MEMBER_STATUS_ID' => '3', 'MEMBER_STATUS_NAME' => 'PD'],
        ]);

        DB::table('ma_type_city')->insertOrIgnore([
            ['TYPE_CITY_ID' => '01', 'TYPE_CITY_CODE' => 'KOTA', 'TYPE_CITY_NAME' => 'Kota'],
            ['TYPE_CITY_ID' => '02', 'TYPE_CITY_CODE' => 'KAB', 'TYPE_CITY_NAME' => 'Kabupaten'],
            ['TYPE_CITY_ID' => '03', 'TYPE_CITY_CODE' => 'PROV', 'TYPE_CITY_NAME' => 'Provinsi'],
        ]);

        DB::table('ma_city')->insertOrIgnore([
            ['CITY_ID' => 'C01', 'CITY_NAME' => 'Bandung', 'TYPE_CITY_ID' => '01'],
            ['CITY_ID' => 'C02', 'CITY_NAME' => 'Cimahi', 'TYPE_CITY_ID' => '01'],
            ['CITY_ID' => 'C03', 'CITY_NAME' => 'Jawa Barat', 'TYPE_CITY_ID' => '03'],
        ]);

        DB::table('ma_district')->insertOrIgnore([
            ['DISTRICT_ID' => 'D01', 'DISTRICT_NAME' => 'Cibeureum', 'CITY_ID' => 'C01'],
            ['DISTRICT_ID' => 'D02', 'DISTRICT_NAME' => 'Babanas', 'CITY_ID' => 'C01'],
            ['DISTRICT_ID' => 'D03', 'DISTRICT_NAME' => 'Cikutra', 'CITY_ID' => 'C02'],
        ]);

        DB::table('ma_village')->insertOrIgnore([
            ['VILLAGE_ID' => 'V01', 'VILLAGE_NAME' => 'Cibeureum', 'DISTRICT_ID' => 'D01'],
            ['VILLAGE_ID' => 'V02', 'VILLAGE_NAME' => 'Sukarasa', 'DISTRICT_ID' => 'D01'],
            ['VILLAGE_ID' => 'V03', 'VILLAGE_NAME' => 'Babakan', 'DISTRICT_ID' => 'D02'],
            ['VILLAGE_ID' => 'V04', 'VILLAGE_NAME' => 'Mekarsari', 'DISTRICT_ID' => 'D03'],
        ]);

        DB::table('ma_member_management')->insertOrIgnore([
            ['MANAGEMENT_ID' => 1, 'MANAGEMENT_NAME' => 'Koperasi'],
            ['MANAGEMENT_ID' => 2, 'MANAGEMENT_NAME' => 'UKM'],
            ['MANAGEMENT_ID' => 3, 'MANAGEMENT_NAME' => 'Perorangan'],
        ]);

        DB::table('mu_simpanan_product')->insertOrIgnore([
            ['SAVINGS_PRODUCT_ID' => 'SIM001', 'PRODUCT_NAME' => 'Simpanan Harian', 'INTEREST_RATE' => 0.05, 'MIN_BALANCE' => 100000],
            ['SAVINGS_PRODUCT_ID' => 'SIM002', 'PRODUCT_NAME' => 'Nest Reno Tambah', 'INTEREST_RATE' => 0.04, 'MIN_BALANCE' => 50000],
            ['SAVINGS_PRODUCT_ID' => 'SIM003', 'PRODUCT_NAME' => 'SSB', 'INTEREST_RATE' => 0.06, 'MIN_BALANCE' => 200000],
            ['SAVINGS_PRODUCT_ID' => 'SIM004', 'PRODUCT_NAME' => 'Setia Plus', 'INTEREST_RATE' => 0.045, 'MIN_BALANCE' => 100000],
        ]);

        $loanProducts = [
            ['LOAN_PRODUCT_ID' => 'LOAN001', 'PRODUCT_NAME' => 'Kredit UKM', 'INTEREST_RATE' => 0.12, 'MAX_AMOUNT' => 100000000, 'DURATION_MONTHS' => 36],
            ['LOAN_PRODUCT_ID' => 'LOAN002', 'PRODUCT_NAME' => 'Kredit Mikro', 'INTEREST_RATE' => 0.15, 'MAX_AMOUNT' => 50000000, 'DURATION_MONTHS' => 24],
            ['LOAN_PRODUCT_ID' => 'LOAN003', 'PRODUCT_NAME' => 'Kredit Jaminan', 'INTEREST_RATE' => 0.10, 'MAX_AMOUNT' => 200000000, 'DURATION_MONTHS' => 48],
            ['LOAN_PRODUCT_ID' => 'LOAN004', 'PRODUCT_NAME' => 'Pengajuan PP', 'INTEREST_RATE' => 0.11, 'MAX_AMOUNT' => 75000000, 'DURATION_MONTHS' => 30],
            ['LOAN_PRODUCT_ID' => 'LOAN005', 'PRODUCT_NAME' => 'MG Emas', 'INTEREST_RATE' => 0.14, 'MAX_AMOUNT' => 100000000, 'DURATION_MONTHS' => 36],
            ['LOAN_PRODUCT_ID' => 'LOAN006', 'PRODUCT_NAME' => 'MG Simhar', 'INTEREST_RATE' => 0.13, 'MAX_AMOUNT' => 80000000, 'DURATION_MONTHS' => 30],
            ['LOAN_PRODUCT_ID' => 'LOAN007', 'PRODUCT_NAME' => 'MG Potong Gaji', 'INTEREST_RATE' => 0.09, 'MAX_AMOUNT' => 150000000, 'DURATION_MONTHS' => 48],
        ];
        DB::table('mu_loan_product')->insertOrIgnore($loanProducts);

        echo "[TestSeeder] Master data: roles, modules, locations, products seeded." . PHP_EOL;

        // ==================== 182 MEMBERS ====================
        if ($skipTransactional) {
            echo "[TestSeeder] Members already exist — skipping member/loan/savings inserts." . PHP_EOL;
            goto seed_summary;
        }

        $names = [
            'Budi','Siti','Ahmad','Dewi','Fauzi','Lestari','Andi','Rina','Hendra','Maya',
            'Rizky','Fitri','Joko','Wati','Bambang','Sari','Agus','Ning','Tono','Yanti',
            'Dodo','Eka','Yusuf','Mina','Gilang','Titi','Hadi','Lia','Iwan','Putri',
            'Slamet','Dina','Raka','Vina','Bayu','Citra','Rudi','Yoga','Nila','Arief',
            'Teguh','Bella','Fajar','Sinta','Dian','Rosa','Jaya','Nuri','Wawan','Sinta',
        ];
        $suffixes = ['Santoso','Aminah','Fauzi','Lestari','Pratama','Susanti','Wibowo','Hartini','Setiawan','Ningsih'];

        $members = [];
        for ($i = 1; $i <= 182; $i++) {
            $registry = sprintf('MEM%04d', $i);
            $memberNo = sprintf('M%06d', $i);
            $nameIdx = ($i - 1) % 50;
            $sfxIdx = intdiv($i - 1, 50) % 10;
            $fullName = $names[$nameIdx] . ' ' . $suffixes[$sfxIdx];
            $status = ($i % 20 === 0) ? 3 : (($i % 10 === 0) ? 2 : 1);

            $members[] = [
                'MEMBER_REGISTRY' => $registry,
                'MEMBER_NO' => $memberNo,
                'MEMBER_NAME' => $fullName,
                'MEMBER_EMAIL' => strtolower(str_replace(' ', '.', $fullName)) . '@test.com',
                'ADDRESS_STAY' => 'Jl. Contoh No. ' . $i,
                'VILLAGE_ID' => ['V01','V02','V03','V04'][$i % 4],
                'DISTRICT_ID' => ['D01','D02','D03'][$i % 3],
                'CITY_ID' => ['C01','C02'][$i % 2],
                'MEMBER_STATUS_ID' => '1',
                'ACTIVE_STATUS_ID' => $status,
                'MEMBER_DATE_REGISTRY' => now()->subDays(30 + ($i % 300))->toDateTimeString(),
                'MEMBER_DATE_VERIFICATION' => now()->subDays(10 + ($i % 20))->toDateTimeString(),
                'BANKING_ACCOUNT_NO' => sprintf('12345678%04d', $i),
                'member_management_id' => 1,
                'LOGTIME' => $now,
            ];
        }

        $countBefore = DB::table('ma_member')->count();
        foreach (array_chunk($members, 50) as $chunk) {
            DB::table('ma_member')->insertOrIgnore($chunk);
        }
        $countAfter = DB::table('ma_member')->count();
        echo "[TestSeeder] Members: {$countAfter} total (baru: " . ($countAfter - $countBefore) . ")" . PHP_EOL;

        // ==================== USERS ====================
        $adminHash = Hash::make('admin123');
        DB::table('ma_user')->insertOrIgnore([
            ['MEMBER_REGISTRY' => 'ADMIN001', 'ROLE_ID' => 'A', 'USERNAME' => 'admin', 'USER_STATUS' => 1, 'PASSWORD' => $adminHash, 'LOGTIME' => $now],
            ['MEMBER_REGISTRY' => 'MEM0001', 'ROLE_ID' => 'M', 'USERNAME' => 'budi', 'USER_STATUS' => 1, 'PASSWORD' => $adminHash, 'LOGTIME' => $now],
            ['MEMBER_REGISTRY' => 'MEM0002', 'ROLE_ID' => 'P', 'USERNAME' => 'siti', 'USER_STATUS' => 1, 'PASSWORD' => $adminHash, 'LOGTIME' => $now],
            ['MEMBER_REGISTRY' => 'MEM0003', 'ROLE_ID' => 'AO3', 'USERNAME' => 'ao3', 'USER_STATUS' => 1, 'PASSWORD' => $adminHash, 'LOGTIME' => $now],
            ['MEMBER_REGISTRY' => 'MEM0004', 'ROLE_ID' => 'AO4', 'USERNAME' => 'ao4', 'USER_STATUS' => 1, 'PASSWORD' => $adminHash, 'LOGTIME' => $now],
            ['MEMBER_REGISTRY' => 'MEM0005', 'ROLE_ID' => 'AO6', 'USERNAME' => 'ao6', 'USER_STATUS' => 1, 'PASSWORD' => $adminHash, 'LOGTIME' => $now],
            ['MEMBER_REGISTRY' => 'MEM0006', 'ROLE_ID' => 'KASIE', 'USERNAME' => 'kasie', 'USER_STATUS' => 1, 'PASSWORD' => $adminHash, 'LOGTIME' => $now],
        ]);
        echo "[TestSeeder] Users: 7 accounts seeded (password: admin123)." . PHP_EOL;

        // ==================== 10 SAVINGS ACCOUNTS ====================
        for ($i = 1; $i <= 10; $i++) {
            DB::table('mu_savings_account')->insertOrIgnore([
                'SAVINGS_ACCOUNT_NO' => sprintf('SAV%04d', $i),
                'MEMBER_REGISTRY' => sprintf('MEM%04d', $i),
                'SAVINGS_PRODUCT_ID' => $i <= 4 ? 'SIM00' . $i : 'SIM001',
                'ACTIVE_ACCOUNT_ID' => 1,
                'SAVINGS_BALANCE' => 1000000 * $i,
                'LOGTIME' => $now,
            ]);

            $txnId = DB::table('mu_transaction_account')->insertGetId([
                'TRANSACTION_NO' => 'TRX' . sprintf('%06d', $i),
                'MEMBER_REGISTRY' => sprintf('MEM%04d', $i),
                'SAVINGS_ACCOUNT_NO' => sprintf('SAV%04d', $i),
                'TRANSACTION_TYPE' => 'DEPOSIT',
                'AMOUNT' => 1000000 * $i,
                'TRANSACTION_DATE' => now()->subDays(30 + $i)->toDateTimeString(),
                'STATUS_ID' => 1,
                'APPROVAL_STATUS_ID' => 1,
                'UPDATE_BY' => 'admin',
                'LOGTIME' => $now,
            ]);
            DB::table('mu_trans_daily_savings')->insert([
                'TRANSACTION_ID' => $txnId,
                'MEMBER_REGISTRY' => sprintf('MEM%04d', $i),
                'SAVINGS_ACCOUNT_NO' => sprintf('SAV%04d', $i),
                'AMOUNT' => 1000000 * $i,
                'BALANCE_AFTER' => 1000000 * $i,
                'TRANSACTION_DATE' => now()->subDays(30 + $i)->toDateTimeString(),
                'LOGTIME' => $now,
            ]);
        }
        echo "[TestSeeder] Savings accounts: 10 seeded (MEM0001-MEM0010)." . PHP_EOL;

        // ==================== 50 LOAN ACCOUNTS ====================
        $prodIds = ['LOAN001','LOAN002','LOAN003','LOAN004','LOAN005','LOAN006','LOAN007'];
        $statuses = array_merge(
            array_fill(0, 10, 0),
            array_fill(0, 10, 1),
            array_fill(0, 10, 2),
            array_fill(0, 10, 6),
            array_fill(0, 5, 7),
            array_fill(0, 5, 8)
        );
        $loanMembers = array_rand(range(11, 160), 50);
        $loanCountBefore = DB::table('mu_loan_account')->count();

        for ($idx = 0; $idx < 50; $idx++) {
            $memIdx = $loanMembers[$idx];
            $registry = sprintf('MEM%04d', $memIdx);
            $status = $statuses[$idx];
            $pid = $prodIds[$idx % 7];
            $amount = [5000000, 10000000, 25000000, 50000000, 75000000][$idx % 5];
            $accNo = sprintf('CR2026%04d', $idx + 1);

            DB::table('mu_loan_account')->insertOrIgnore([
                'LOAN_ACCOUNT_NO' => $accNo,
                'MEMBER_REGISTRY' => $registry,
                'LOAN_PRODUCT_ID' => $pid,
                'LOAN_AMOUNT' => $amount,
                'REMAINING_AMOUNT' => $status === 6 ? 0 : round($amount * 0.5, 2),
                'LOAN_STATUS_ID' => $status,
                'LOGTIME' => $now,
            ]);

            $loanId = DB::table('mu_loan')->insertGetId([
                'MEMBER_REGISTRY' => $registry,
                'LOAN_PRODUCT_ID' => $pid,
                'LOAN_AMOUNT' => $amount,
                'LOAN_STATUS_ID' => $status,
                'APPROVAL_STATUS_ID' => $status >= 1 ? 1 : 0,
                'LOAN_DATE' => now()->subDays(30 + ($idx % 300))->toDateTimeString(),
                'UPDATE_BY' => 'admin',
                'LOGTIME' => $now,
            ]);

            $instCount = in_array($status, [6, 7, 8]) ? 3 : 12;
            for ($n = 1; $n <= $instCount; $n++) {
                $isPaid = ($status === 6) ? 1 : (($status >= 2 && $status <= 8 && $n <= 3) ? 1 : 0);
                DB::table('mu_trans_installment')->insert([
                    'LOAN_ACCOUNT_NO' => $accNo,
                    'MEMBER_REGISTRY' => $registry,
                    'INSTALLMENT_NO' => $n,
                    'AMOUNT' => round($amount / $instCount, 2),
                    'DUE_DATE' => now()->addDays($n * 30 - 30)->format('Y-m-d'),
                    'PAID_DATE' => $isPaid ? now()->subDays(1 + ($n % 20))->toDateTimeString() : null,
                    'STATUS_ID' => $isPaid,
                    'LOGTIME' => $now,
                ]);
            }

            $colType = ['BPKB','GOLD','SALARY','SAVINGS','CERT'][$idx % 5];
            if ($colType === 'BPKB') {
                DB::table('mu_collateral_bpkb')->insert([
                    'MEMBER_REGISTRY' => $registry, 'LOAN_ID' => $loanId,
                    'BPKB_NO' => 'BPKB' . sprintf('%06d', $memIdx),
                    'VEHICLE_PLATE' => sprintf('D%05d', 10000 + ($memIdx * 7) % 90000),
                    'LOGTIME' => $now,
                ]);
            } elseif ($colType === 'GOLD') {
                DB::table('mu_collateral_gold')->insert([
                    'MEMBER_REGISTRY' => $registry, 'LOAN_ID' => $loanId,
                    'GOLD_WEIGHT' => 50 + ($memIdx % 450), 'GOLD_PURITY' => '999',
                    'LOGTIME' => $now,
                ]);
            } elseif ($colType === 'SALARY') {
                DB::table('mu_collateral_salary')->insert([
                    'MEMBER_REGISTRY' => $registry, 'LOAN_ID' => $loanId,
                    'SALARY_AMOUNT' => 5000000 + ($memIdx % 15000000),
                    'LOGTIME' => $now,
                ]);
            } elseif ($colType === 'SAVINGS') {
                DB::table('mu_collateral_savings')->insert([
                    'MEMBER_REGISTRY' => $registry, 'LOAN_ID' => $loanId,
                    'SAVINGS_ACCOUNT_NO' => sprintf('SAV%04d', 1 + ($memIdx % 10)),
                    'AMOUNT' => 10000000,
                    'LOGTIME' => $now,
                ]);
            } else {
                DB::table('mu_collateral_certificate')->insert([
                    'MEMBER_REGISTRY' => $registry, 'LOAN_ID' => $loanId,
                    'CERTIFICATE_NO' => 'SHM' . sprintf('%06d', $memIdx),
                    'LAND_AREA' => 100 + ($memIdx % 400),
                    'LOGTIME' => $now,
                ]);
            }

            DB::table('mu_survey')->insert([
                'MEMBER_REGISTRY' => $registry, 'LOAN_ID' => $loanId,
                'SURVEY_DATE' => now()->subDays(30 + ($idx % 30))->format('Y-m-d'),
                'SURVEY_RESULT' => $status >= 1 ? 'APPROVED' : 'PENDING',
                'SURVEYOR' => 'admin',
                'LOGTIME' => $now,
            ]);

            DB::table('ma_images')->insert([
                ['USER_ID' => $registry, 'IMAGE_TYPE' => 'SURVEY', 'IMAGE_FILE' => 'storage/survey_' . $accNo . '.jpg', 'LOGTIME' => $now],
                ['USER_ID' => $registry, 'IMAGE_TYPE' => 'AGUNAN', 'IMAGE_FILE' => 'storage/agunan_' . $accNo . '.jpg', 'LOGTIME' => $now],
            ]);
        }

        $loanCountAfter = DB::table('mu_loan_account')->count();
        echo "[TestSeeder] Loan accounts: {$loanCountAfter} total (baru: " . ($loanCountAfter - $loanCountBefore) . ")" . PHP_EOL;
        echo "[TestSeeder] Installments, collateral, surveys, images: seeded." . PHP_EOL;

        seed_summary:
        // ==================== SUMMARY ====================
        echo PHP_EOL . "========== TEST DATA SEED SUMMARY ==========" . PHP_EOL;
        echo "  Members       : " . DB::table('ma_member')->count() . PHP_EOL;
        echo "  Loan Accounts : " . DB::table('mu_loan_account')->count() . PHP_EOL;
        echo "  mu_loan       : " . DB::table('mu_loan')->count() . PHP_EOL;
        echo "  Installments  : " . DB::table('mu_trans_installment')->count() . PHP_EOL;
        $colTotal = DB::table('mu_collateral_bpkb')->count()
            + DB::table('mu_collateral_gold')->count()
            + DB::table('mu_collateral_salary')->count()
            + DB::table('mu_collateral_savings')->count()
            + DB::table('mu_collateral_certificate')->count();
        echo "  Collaterals   : " . $colTotal . PHP_EOL;
        echo "  Surveys       : " . DB::table('mu_survey')->count() . PHP_EOL;
        echo "  Images        : " . DB::table('ma_images')->count() . PHP_EOL;
        echo "  Savings Accts : " . DB::table('mu_savings_account')->count() . PHP_EOL;
        echo "===============================================" . PHP_EOL;
        echo "Done. Re-run: php artisan db:seed --class=TestDatabaseSeeder" . PHP_EOL;
    }
}
