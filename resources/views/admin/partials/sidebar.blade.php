<!-- Sidebar menu untuk admin -->
<div class="card" id="sidebar-menu" style="margin-bottom:1rem; border-radius:4px; box-shadow:0 2px 4px rgba(0,0,0,.08);">
    <div class="card-content" style="padding:10px 14px;">
        <span style="font-size:1rem; font-weight:600; color:#1565c0; display:block; margin-bottom:4px;">Menu Admin</span>
        <ul class="collapsible" id="sidebar-collapsible" data-collapsible="accordion" style="border:none;">
            <li style="border:none;">
                <a class="collapsible-header sidebar-hdr" href="javascript:void(0)">Dashboard</a>
                <div class="collapsible-body">
                    <a href="{{ route('admin.dashboard') }}">Dashboard Utama</a>
                </div>
            </li>
            <li style="border:none;">
                <a class="collapsible-header sidebar-hdr" href="javascript:void(0)">Member</a>
                <div class="collapsible-body">
                    <a href="{{ route('admin.member.index') }}">Daftar Member</a>
                    <a href="{{ route('admin.member.create') }}">Tambah Member</a>
                </div>
            </li>
            <li style="border:none;">
                <a class="collapsible-header sidebar-hdr" href="javascript:void(0)">Role &amp; Module</a>
                <div class="collapsible-body">
                    <a href="{{ route('admin.role.index') }}">Daftar Role</a>
                    <a href="{{ route('admin.role.create') }}">Tambah Role</a>
                    <a href="{{ route('admin.module.index') }}">Daftar Module</a>
                    <a href="{{ route('admin.module.create') }}">Tambah Module</a>
                </div>
            </li>
            <li style="border:none;">
                <a class="collapsible-header sidebar-hdr" href="javascript:void(0)">Master Data</a>
                <div class="collapsible-body">
                    <a href="{{ route('admin.employee.index') }}">Karyawan</a>
                    <a href="{{ route('admin.fee.index') }}">Biaya</a>
                    <a href="{{ route('admin.user.index') }}">Akun Login</a>
                </div>
            </li>
            <li style="border:none;">
                <a class="collapsible-header sidebar-hdr" href="javascript:void(0)">Lokasi</a>
                <div class="collapsible-body">
                    <a href="{{ route('admin.location.village') }}">Desa / Kelurahan</a>
                    <a href="{{ route('admin.location.district') }}">Kecamatan</a>
                    <a href="{{ route('admin.location.city') }}">Kota</a>
                </div>
            </li>
            <li style="border:none;">
                <a class="collapsible-header sidebar-hdr" href="javascript:void(0)">Konstanta</a>
                <div class="collapsible-body">
                    <a href="{{ route('admin.constants.index') }}">Manajemen Konstanta</a>
                    <a href="{{ route('admin.constants.simhar') }}">Konstanta SimHar</a>
                    <a href="{{ route('admin.constants.ssb') }}">Konstanta SSB</a>
                    <a href="{{ route('admin.constants.pinjaman') }}">Konstanta Pinjaman</a>
                </div>
            </li>
            <li style="border:none;">
                <a class="collapsible-header sidebar-hdr" href="javascript:void(0)">Simpanan</a>
                <div class="collapsible-body">
                    <a href="{{ route('admin.savings.list_akun') }}">Daftar Akun Simpanan</a>
                    <a href="{{ route('admin.savings.harian') }}">Master Simpanan Harian</a>
                    <a href="{{ route('admin.savings.trans_simhar') }}">Transaksi SimHar</a>
                    <a href="{{ route('admin.savings.ntr') }}">Master NTR</a>
                    <a href="{{ route('admin.savings.trans_ntr') }}">Transaksi NTR</a>
                    <a href="{{ route('admin.savings.setiaplus') }}">Master SetiaPlus</a>
                    <a href="{{ route('admin.savings.ssb') }}">Master SSB</a>
                    <a href="{{ route('admin.savings.cek_saldo_simhar') }}">Saldo SimHar</a>
                    <a href="{{ route('admin.savings.cek_saldo_ssb') }}">Saldo SSB</a>
                    <a href="{{ route('admin.savings.tutup_rek_simhar') }}">Tutup Rekening SimHar</a>
                    <a href="{{ route('admin.savings.tutup_rek_ssb') }}">Tutup Rekening SSB</a>
                    <a href="{{ route('admin.savings.persetujuan') }}">Persetujuan Transaksi</a>
                    <a href="{{ route('admin.savings.cetak_bkm') }}">Cetak BKM</a>
                    <a href="{{ route('admin.savings.cetak_bkk') }}">Cetak BKK</a>
                    <a href="{{ route('admin.savings.cetak_bu') }}">Cetak BU</a>
                    <a href="{{ route('admin.savings.trans_ntr_form') }}">Transaksi NTR (Form)</a>
                    <a href="{{ route('admin.savings.trans_ssb') }}">Transaksi SSB</a>
                    <a href="{{ route('admin.savings.auto_closing') }}">Auto Closing</a>
                    <a href="{{ route('admin.savings.tagihan_jasa_setiaplus') }}">Jasa SetiaPlus</a>
                    <a href="{{ route('admin.savings.tagihan_jasa_ssb') }}">Jasa SSB</a>
                </div>
            </li>
            <li style="border:none;">
                <a class="collapsible-header sidebar-hdr" href="javascript:void(0)">Pinjaman</a>
                <div class="collapsible-body">
                    <a href="{{ route('admin.loan.pj_pinjaman') }}">Dashboard Pinjaman</a>
                    <a href="{{ route('admin.loan.ukm') }}">Master Pinjaman UKM</a>
                    <a href="{{ route('admin.loan.mikro') }}">Master Pinjaman Mikro</a>
                    <a href="{{ route('admin.loan.jaminan') }}">Master Pinjaman Jaminan</a>
                    <a href="{{ route('admin.loan.pengajuan_pp') }}">Pengajuan PP</a>
                    <a href="{{ route('admin.loan.pengajuan_jatem') }}">Pengajuan Jatuh Tempo</a>
                    <a href="{{ route('admin.loan.pengajuan_mg_emas') }}">Pengajuan MG Emas</a>
                    <a href="{{ route('admin.loan.pengajuan_mg_simhar') }}">Pengajuan MG Simhar</a>
                    <a href="{{ route('admin.loan.pengajuan_mg_potong') }}">Pengajuan MG Potong Gaji</a>
                    <a href="{{ route('admin.loan.tagihan_jatem') }}">Tagihan Jatuh Tempo</a>
                    <a href="{{ route('admin.loan.angsuran_kolektif') }}">Angsuran Kolektif</a>
                    <a href="{{ route('admin.loan.rescheduling_jatem') }}">Rescheduling Jatem</a>
                    <a href="{{ route('admin.loan.rescheduling_by_req') }}">Rescheduling by Request</a>
                    <a href="{{ route('admin.loan.persetujuan_trans') }}">Persetujuan Transaksi</a>
                    <a href="{{ route('admin.loan.approval_multi_level', ['level' => 'ao3']) }}">ao3|Approval Multi-Level</a>
                    <a href="{{ route('admin.loan.laporan_agunan') }}">Laporan Agunan</a>
                    <a href="{{ route('admin.loan.laporan_realisasi') }}">Laporan Realisasi</a>
                    <a href="{{ route('admin.loan.laporan_pelunasan') }}">Laporan Pelunasan</a>
                    <a href="{{ route('admin.loan.laporan_resheduling') }}">Laporan Resheduling</a>
                    <a href="{{ route('admin.loan.laporan_angsuran') }}">Laporan Angsuran</a>
                    <a href="{{ route('admin.loan.laporan_tunggakan') }}">Laporan Tunggakan</a>
                    <a href="{{ route('admin.loan.laporan_sph') }}">Laporan SPH</a>
                    <a href="{{ route('admin.loan.view_payment') }}">View Payment</a>
                    <a href="{{ route('admin.loan.cetak_bkm') }}">Cetak BKM Pinjaman</a>
                    <a href="{{ route('admin.loan.cetak_bkk') }}">Cetak BKK Pinjaman</a>
                    <a href="{{ route('admin.loan.cetak_bu') }}">Cetak BU Pinjaman</a>
                </div>
            </li>
            <li style="border:none;">
                <a class="collapsible-header sidebar-hdr" href="javascript:void(0)">Audit</a>
                <div class="collapsible-body">
                    <a href="{{ route('admin.audit.index') }}">Audit Trail</a>
                </div>
            </li>
        </ul>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('sidebar-collapsible');
        if (el && typeof M !== 'undefined' && M.Collapsible) {
            M.Collapsible.init(el, { accordion: true });
        }
    });
    </script>
</div>
