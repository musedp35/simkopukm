@extends('admin.layouts')
@section('title', 'Persetujuan Transaksi Pinjaman')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Persetujuan Transaksi Pinjaman</h4>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="card">
        <div class="card-content">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No. Anggota</th><th>Nama</th><th>Tanggal</th>
                        <th>Pokok</th><th>Angsuran</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r->MEMBER_NO ?? '-' }}</td>
                        <td>{{ $r->MEMBER_NAME ?? '-' }}</td>
                        <td>{{ $r->LOAN_DATE ?? '-' }}</td>
                        <td>{{ number_format($r->LOAN_AMOUNT ?? 0, 2) }}</td>
                        <td>{{ number_format($r->INSTALLMENT_AMOUNT ?? 0, 2) }}</td>
                        <td>{{ $r->APPROVAL_STATUS_ID ?? 0 }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.loan.proses_approval_trans') }}" style="display:inline">
                                @csrf
                                <input type="hidden" name="transId" value="{{ $r->LOAN_ID ?? 0 }}">
                                <input type="hidden" name="chbApproval" value="1">
                                <button type="submit" class="btn-small green waves-effect">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.loan.proses_approval_trans') }}" style="display:inline">
                                @csrf
                                <input type="hidden" name="transId" value="{{ $r->LOAN_ID ?? 0 }}">
                                <input type="hidden" name="chbApproval" value="0">
                                <button type="submit" class="btn-small red waves-effect">Reject</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="center">Belum ada transaksi untuk disetujui.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
