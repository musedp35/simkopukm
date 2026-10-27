@extends('admin.layouts')
@section('title', 'Rescheduling by Request')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Rescheduling by Request</h4>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-content">
            <form method="POST" action="{{ route('admin.loan.proses_rescheduling_by_req') }}">
                @csrf
                <div class="row">
                    <div class="input-field col s6">
                        <input type="text" name="loanAccountNo" id="loanAccountNo" required>
                        <label for="loanAccountNo">No. Kredit / Loan Account No</label>
                    </div>
                    <div class="input-field col s6">
                        <input type="date" name="requestDate" id="requestDate" required>
                        <label for="requestDate">Tanggal Request</label>
                    </div>
                </div>
                <button type="submit" class="btn amber waves-effect">Proses Rescheduling</button>
                <a href="{{ route('admin.loan.rescheduling_jatem') }}" class="btn blue darken-1 waves-effect">Kembali</a>
            </form>
            <div style="margin-top:20px">
                <h5 style="font-size:1.1rem">Daftar Rescheduling</h5>
                <table class="striped responsive-table">
                    <thead>
                        <tr>
                            <th>No. Anggota</th><th>Nama</th><th>No. Kredit</th>
                            <th>Pokok</th><th>Sisa</th><th>Status</th>
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
                        </tr>
                    @empty
                        <tr><td colspan="6" class="center">Belum ada pinjaman dalam kondisi rescheduling.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
