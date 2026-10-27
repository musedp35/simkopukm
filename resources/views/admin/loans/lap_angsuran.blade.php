@extends('admin.layouts')
@section('title', 'Laporan Angsuran')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Laporan Angsuran</h4>
    <div class="card">
        <div class="card-content">
            <form method="GET" class="row">
                <div class="input-field col s4">
                    <input type="date" name="start" value="{{ $start }}">
                    <label>Mulai</label>
                </div>
                <div class="input-field col s4">
                    <input type="date" name="end" value="{{ $end }}">
                    <label>Sampai</label>
                </div>
                <div class="col s4">
                    <button type="submit" class="btn waves-effect waves-light blue">Tampilkan</button>
                </div>
            </form>
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No. Anggota</th><th>Nama</th><th>No. Kredit</th>
                        <th>Angsuran</th><th>Jumlah</th><th>Jatuh Tempo</th><th>Dibayar</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r['MEMBER_NO'] ?? '-' }}</td>
                        <td>{{ $r['MEMBER_NAME'] ?? '-' }}</td>
                        <td>{{ $r['LOAN_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>{{ $r['INSTALLMENT_NO'] ?? 0 }}</td>
                        <td>{{ number_format($r['AMOUNT'] ?? 0, 2) }}</td>
                        <td>{{ $r['DUE_DATE'] ?? '-' }}</td>
                        <td>{{ $r['PAID_DATE'] ? $r['PAID_DATE'] : 'Belum' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="center">Belum ada data angsuran pada periode ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
