@extends('admin.layouts')
@section('title', 'Tagihan Jatuh Tempo')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Tagihan Jatuh Tempo</h4>
    <div class="card">
        <div class="card-content">
            <form method="GET" class="row">
                <div class="input-field col s3">
                    <input type="date" name="date" value="{{ request('date') }}">
                    <label>Tanggal Jatuh Tempo (kosongkan = semua)</label>
                </div>
                <div class="col s2">
                    <button type="submit" class="btn waves-effect waves-light blue">Filter</button>
                </div>
            </form>
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No. Anggota</th><th>Nama</th><th>No. Kredit</th>
                        <th>Jumlah</th><th>Jatuh Tempo</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r['MEMBER_NO'] ?? '-' }}</td>
                        <td>{{ $r['MEMBER_NAME'] ?? '-' }}</td>
                        <td>{{ $r['LOAN_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>{{ number_format($r['AMOUNT'] ?? 0, 2) }}</td>
                        <td>{{ $r['DUE_DATE'] ?? '-' }}</td>
                        <td><span class="red-text">Jatuh Tempo</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="center">Belum ada tagihan jatuh tempo.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
