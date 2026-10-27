<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SavingsRouteSmokeTest extends TestCase
{
    public function test_all_savings_get_routes_do_not_throw_500(): void
    {
        $getRoutes = [
            'admin/savings/harian',
            'admin/savings/ntr',
            'admin/savings/ssb',
            'admin/savings/setiaplus',
            'admin/savings/list-akun',
            'admin/savings/auto-closing',
            'admin/savings/persetujuan',
            'admin/savings/tagihan-jasa/setiaplus',
            'admin/savings/tagihan-jasa/ssb',
            'admin/savings/cetak-bkm',
            'admin/savings/cetak-bkk',
            'admin/savings/cetak-bu',
            'admin/savings/cek-saldo/simhar',
            'admin/savings/cek-saldo/ntr',
            'admin/savings/cek-saldo/ssb',
            'admin/savings/tutup-rekening/simhar',
            'admin/savings/tutup-rekening/setiaplus',
            'admin/savings/tutup-rekening/ssb',
            'admin/savings/harian/transaksi',
            'admin/savings/ntr/transaksi',
            'admin/savings/ntr/form',
            'admin/savings/ssb/transaksi',
            'admin/savings/laporan/pembukaan/simhar',
            'admin/savings/laporan/pembukaan/ntr',
            'admin/savings/laporan/pembukaan/ssb',
            'admin/savings/laporan/penutupan/simhar',
            'admin/savings/laporan/rekap/saldo',
            'admin/savings/laporan/rinci/saldo',
            'admin/savings/laporan/buku-besar/ssb',
            'admin/savings/laporan/buku-besar/ntr',
            'admin/savings/laporan/mutasi/ntr',
            // Prioritas 3
            'admin/savings/laporan/jatem/ssb/rekap',
            'admin/savings/laporan/jatem/ssb/rinci',
            'admin/savings/laporan/daftar-akun/ssb',
            'admin/savings/laporan/daftar-akun/ssb/rinci',
        ];

        foreach ($getRoutes as $uri) {
            $response = $this->get('/' . $uri);
            $this->assertNotEquals(500, $response->getStatusCode(),
                "Route /{$uri} mengembalikan status 500");
        }
    }

    public function test_laporan_routes_with_date_filters_do_not_throw_500(): void
    {
        $dateRoutes = [
            'admin/savings/laporan/pembukaan/simhar?dtpTgl=2024-01-01&dtpTglAkhir=2024-12-31',
            'admin/savings/laporan/pembukaan/ntr?dtpTgl=2024-01-01&dtpTglAkhir=2024-12-31',
            'admin/savings/laporan/pembukaan/ssb?dtpTgl=2024-01-01&dtpTglAkhir=2024-12-31',
            'admin/savings/laporan/penutupan/simhar?dtpTgl=2024-01-01&dtpTglAkhir=2024-12-31',
            'admin/savings/laporan/rekap/saldo?dtpTgl=2024-01-01&dtpTglAkhir=2024-12-31',
            'admin/savings/laporan/rinci/saldo?dtpTgl=2024-01-01&dtpTglAkhir=2024-12-31',
            // Prioritas 3
            'admin/savings/laporan/jatem/ssb/rekap?dtpTgl=2024-01-01&dtpTglAkhir=2024-12-31&cmbStatus=0',
            'admin/savings/laporan/jatem/ssb/rinci?dtpTgl=2024-01-01&dtpTglAkhir=2024-12-31',
            'admin/savings/laporan/daftar-akun/ssb?dtpTgl=2024-01-01&dtpTglAkhir=2024-12-31&cmbStatus=4',
            'admin/savings/laporan/daftar-akun/ssb/rinci?dtpTgl=2024-01-01&dtpTglAkhir=2024-12-31',
        ];

        foreach ($dateRoutes as $uri) {
            $response = $this->get('/' . $uri);
            $this->assertNotEquals(500, $response->getStatusCode(),
                "Route {$uri} mengembalikan status 500");
        }
    }

    /**
     * Uji langsung method service SSB (Prioritas 3) — pastikan tidak throw.
     */
    public function test_ssb_report_service_methods_do_not_throw(): void
    {
        DB::beginTransaction();
        try {
            // Buat akun SSB sementara untuk diuji
            DB::table('mu_savings_account')->insert([
                'SAVINGS_ACCOUNT_NO' => 'SAVTST01',
                'MEMBER_REGISTRY'    => 'MEM001',
                'SAVINGS_PRODUCT_ID' => \App\Services\SavingsService::PRODUCT_NTR,
                'ACTIVE_ACCOUNT_ID'  => 1,
                'SAVINGS_BALANCE'    => 1000000,
                'LOGTIME'            => '2024-06-01 00:00:00',
            ]);

            $rekap = \App\Services\SavingsService::lapJatemSSBRekap('2024-01-01','2024-12-31',0);
            $this->assertIsArray($rekap);
            $this->assertNotEmpty($rekap, 'lapJatemSSBRekap harus menghasilkan data');

            $rinci = \App\Services\SavingsService::lapJatemSSBRinci('2024-01-01','2024-12-31');
            $this->assertIsArray($rinci);

            $daftarRekap = \App\Services\SavingsService::lapDaftarAkunRekapSSB('2024-01-01','2024-12-31',0);
            $this->assertArrayHasKey('JML_AKUN', $daftarRekap);
            $this->assertArrayHasKey('TOTAL_SALDO', $daftarRekap);

            $daftarRinci = \App\Services\SavingsService::lapDaftarAkunRinciSSB('2024-01-01','2024-12-31');
            $this->assertIsArray($daftarRinci);
        } finally {
            DB::rollBack();
        }
    }
}
