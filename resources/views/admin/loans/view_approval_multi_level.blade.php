@extends('admin.layouts')
@section('title', 'Approval Multi-Level')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Approval Multi-Level — Level {{ strtoupper($level) }}</h4>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="row">
        @foreach(['ao3' => 'AO3', 'ao4' => 'AO4', 'ao6' => 'AO6', 'kasie' => 'Kasie'] as $key => $label)
            <a href="{{ route('admin.loan.approval_multi_level', ['level' => $key]) }}"
               class="chip {{ ($level === $key) ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>
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
                        <td>{{ $r['LOAN_STATUS_ID'] }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.loan.proses_approval_multi_level') }}" style="display:inline">
                                @csrf
                                <input type="hidden" name="loanAccountNo" value="{{ $r['LOAN_ACCOUNT_NO'] }}">
                                <input type="hidden" name="level" value="{{ $level }}">
                                <input type="hidden" name="approve" value="1">
                                <button type="submit" class="btn-small green waves-effect">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.loan.proses_approval_multi_level') }}" style="display:inline">
                                @csrf
                                <input type="hidden" name="loanAccountNo" value="{{ $r['LOAN_ACCOUNT_NO'] }}">
                                <input type="hidden" name="level" value="{{ $level }}">
                                <input type="hidden" name="approve" value="0">
                                <button type="submit" class="btn-small red waves-effect">Reject</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="center">Belum ada pinjaman yang menunggu approval di level {{ strtoupper($level) }}.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
