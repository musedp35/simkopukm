@extends('admin.layouts')
@section('title', 'Laporan Agunan')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Laporan Agunan</h4>
    <div class="card">
        <div class="card-content">
            <div class="row">
                @foreach(['BPKB' => 'BPKB', 'Emas' => 'Emas', 'Gaji' => 'Gaji', 'Simpanan' => 'Simpanan', 'Sertifikat' => 'Sertifikat'] as $key => $label)
                    <a href="{{ route('admin.loan.laporan_agunan', ['jenis' => $key]) }}"
                       class="chip {{ ($jenis === $key) ? 'active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No. Anggota</th><th>Nama</th><th>LPO</th>
                        @if($jenis === 'BPKB')
                            <th>No. BPKB</th><th>Plat</th>
                        @elseif($jenis === 'Emas')
                            <th>Berat Emas</th><th>Karats</th>
                        @elseif($jenis === 'Gaji')
                            <th>Jumlah Gaji</th>
                        @elseif($jenis === 'Simpanan')
                            <th>No. Rekening</th><th>Jumlah</th>
                        @elseif($jenis === 'Sertifikat')
                            <th>No. Sertifikat</th><th>Luas Lahan</th>
                        @endif
                        <th>Jumlah Pinjaman</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r['MEMBER_NO'] ?? '-' }}</td>
                        <td>{{ $r['MEMBER_NAME'] ?? '-' }}</td>
                        <td>{{ $r['LOAN_ID'] ?? '-' }}</td>
                        @if($jenis === 'BPKB')
                            <td>{{ $r['BPKB_NO'] ?? '-' }}</td>
                            <td>{{ $r['VEHICLE_PLATE'] ?? '-' }}</td>
                        @elseif($jenis === 'Emas')
                            <td>{{ $r['GOLD_WEIGHT'] ?? 0 }} gr</td>
                            <td>{{ $r['GOLD_PURITY'] ?? '-' }}%</td>
                        @elseif($jenis === 'Gaji')
                            <td>{{ number_format($r['SALARY_AMOUNT'] ?? 0, 2) }}</td>
                        @elseif($jenis === 'Simpanan')
                            <td>{{ $r['SAVINGS_ACCOUNT_NO'] ?? '-' }}</td>
                            <td>{{ number_format($r['AMOUNT'] ?? 0, 2) }}</td>
                        @elseif($jenis === 'Sertifikat')
                            <td>{{ $r['CERTIFICATE_NO'] ?? '-' }}</td>
                            <td>{{ $r['LAND_AREA'] ?? 0 }} m²</td>
                        @endif
                        <td>{{ number_format($r['LOAN_AMOUNT'] ?? 0, 2) }}</td>
                        <td>{{ $r['LOAN_STATUS_ID'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="center">Belum ada data agunan {{ $jenis }}.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
