<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoanRouteSmokeTest extends TestCase
{
    private array $getRoutes = [
        'admin/loan/ukm',
        'admin/loan/mikro',
        'admin/loan/jaminan',
        'admin/loan/pj-pinjaman',
        'admin/loan/pengajuan-pp',
        'admin/loan/pengajuan-jatem',
        'admin/loan/pengajuan-rsjatem',
        'admin/loan/pengajuan-rsbyreq',
        'admin/loan/data-anggota-pinjaman',
        'admin/loan/data-anggota-pinjaman/buku-besar',
        'admin/loan/data-pinjaman-all-status',
        'admin/loan/transaksi-hari-ini',
        'admin/loan/tagihan',
        'admin/loan/persetujuan-trans',
        'admin/loan/cetak-bkm',
        'admin/loan/cetak-bkk',
        'admin/loan/cetak-bu',
        'admin/loan/tagihan-jatem',
        'admin/loan/angsuran-kolektif',
        'admin/loan/rescheduling-jatem',
        'admin/loan/rescheduling-by-req',
        'admin/loan/detail-rescheduling',
        'admin/loan/data-tagihan-kolektif',
        'admin/loan/laporan-agunan',
        'admin/loan/laporan-realisisasi',
        'admin/loan/laporan-pelunasan',
        'admin/loan/laporan-resheduling',
        'admin/loan/laporan-angsuran',
        'admin/loan/form-realisisasi',
        'admin/loan/view-payment',
        'admin/loan/approval-multi-level',
        'admin/loan/laporan-tunggakan',
        'admin/loan/pengajuan-mg-emas',
        'admin/loan/pengajuan-mg-simhar',
        'admin/loan/pengajuan-mg-potong',
        'admin/loan/laporan-sph',
    ];

    public function test_all_loan_get_routes_do_not_throw_500(): void
    {
        foreach ($this->getRoutes as $uri) {
            $response = $this->get('/' . $uri);
            $this->assertNotEquals(500, $response->getStatusCode(),
                "Loan route /{$uri} returns 500");
        }
    }

    public function test_mg_detail_routes_with_valid_loan_no(): void
    {
        $no = 'CR20260001';
        $routes = [
            "admin/loan/mg-serti/{$no}",
            "admin/loan/mg-simhar/{$no}",
            "admin/loan/mg-potong/{$no}",
            "admin/loan/survey/{$no}",
            "admin/loan/pelunasan/{$no}",
            "admin/loan/agunan/{$no}",
            "admin/loan/log-ao-pj/{$no}",
            "admin/loan/sph/kecil/{$no}",
            "admin/loan/sph/besar/{$no}",
        ];
        foreach ($routes as $uri) {
            $response = $this->get('/' . $uri);
            $this->assertNotEquals(500, $response->getStatusCode(),
                "Loan detail route /{$uri} returns 500");
        }
    }
}
