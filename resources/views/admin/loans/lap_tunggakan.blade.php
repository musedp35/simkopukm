@extends('admin.layouts')
@section('title', 'Laporan Tunggakan')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Laporan Tunggakan Pinjaman</h4>
    <div class="card">
        <div class="card-content">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No. Anggota</th><th>Nama</th>
                        <th>Jumlah Tunggakan</th><th>Total Tunggakan</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r['MEMBER_NO'] ?? '-' }}</td>
                        <td>{{ $r['MEMBER_NAME'] ?? '-' }}</td>
                        <td>{{ $r['TOTAL_TUNGGAKAN'] ?? 0 }}</td>
                        <td>{{ number_format($r['TOTAL_JUMLAH'] ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="center">Belum ada tunggakan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
