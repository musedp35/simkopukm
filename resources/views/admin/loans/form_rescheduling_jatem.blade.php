@extends('admin.layouts')
@section('title', 'Rescheduling Jatuh Tempo')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Rescheduling Jatuh Tempo</h4>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-content">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No. Anggota</th><th>Nama</th><th>No. Kredit</th>
                        <th>Pokok</th><th>Sisa</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r['MEMBER_NO'] ?? '-' }}</td>
                        <td>{{ $r['MEMBER_NAME'] ?? '-' }}</td>
                        <td>{{ $r['LOAN_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>{{ number_format($r['LOAN_AMOUNT'] ?? 0, 2) }}</td>
                        <td>{{ number_format($r['REMAINING_AMOUNT'] ?? 0, 2) }}</td>
                        <td>Rescheduling</td>
                        <td>
                            <a href="{{ route('admin.loan.pengajuan_rsjatem', ['loanAccountNo' => $r['LOAN_ACCOUNT_NO']]) }}"
                               class="btn-small amber waves-effect">Reschedule</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="center">Belum ada pinjaman dalam kondisi rescheduling.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
