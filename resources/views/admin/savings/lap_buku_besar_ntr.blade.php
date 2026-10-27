@extends('admin.layouts')
@section('title', 'Lap. Buku Besar NTR - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Buku Besar NTR</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>
    <form method="GET" action="{{ route('admin.savings.lap_buku_besar_ntr') }}">
        <div class="row">
            <div class="col s12 m4"><div class="input-field"><input type="text" name="edtNoReg" value="{{ request('edtNoReg') }}" required><label>No. Anggota / Registry</label></div></div>
            <div class="col s12 m4"><button type="submit" class="btn blue"><i class="material-icons left">search</i> Cari</button></div>
        </div>
    </form>
    @if(request('edtNoReg'))
    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>#</th><th>Nama</th><th>Tgl</th><th>Nominal</th><th>Saldo Akhir</th></tr></thead>
            <tbody>
                @forelse($rows ?? [] as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['MEMBER_NAME'] ?? '-' }}</td>
                        <td>{{ $row['TRANSACTION_DATE'] ?? '-' }}</td>
                        <td>Rp {{ number_format((float)($row['AMOUNT'] ?? 0), 2) }}</td>
                        <td>Rp {{ number_format((float)($row['BALANCE_AFTER'] ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="center grey-text">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div></div>
@stop
