@extends('admin.layouts')
@section('title', 'Angsuran Kolektif')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Angsuran Kolektif — Pembayaran Batch</h4>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-content">
            <form method="POST" action="{{ route('admin.loan.proses_angsuran_kolektif') }}">
                @csrf
                <table class="striped responsive-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>No. Anggota</th><th>Nama</th><th>No. Kredit</th>
                            <th>Angsuran</th><th>Jatuh Tempo</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td><input type="checkbox" name="transIds[]" value="{{ $r['TRANS_ID'] }}" class="trans-id"></td>
                            <td>{{ $r['MEMBER_NO'] ?? '-' }}</td>
                            <td>{{ $r['MEMBER_NAME'] ?? '-' }}</td>
                            <td>{{ $r['LOAN_ACCOUNT_NO'] ?? '-' }}</td>
                            <td>{{ $r['INSTALLMENT_NO'] ?? 0 }}/{{ $r['AMOUNT'] ? number_format($r['AMOUNT'], 2) : '-' }}</td>
                            <td>{{ $r['DUE_DATE'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="center">Belum ada angsuran untuk dibayar.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="row" style="margin-top:10px">
                    <button type="submit" class="btn green waves-effect">Bayar Angsuran Terpilih</button>
                    <a href="{{ route('admin.loan.angsuran_kolektif') }}" class="btn blue darken-1 waves-effect">Refresh</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.getElementById('checkAll')?.addEventListener('change', function() {
    document.querySelectorAll('.trans-id').forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
