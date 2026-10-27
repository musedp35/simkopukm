@extends('admin.layouts')
@section('title', 'Auto Closing - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Auto Closing Rekening</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    <form method="GET" action="{{ route('admin.savings.auto_closing') }}">
        <div class="row">
            <div class="col s12 m4"><div class="input-field">
                <input type="date" name="tglAwal" value="{{ request('tglAwal', now()->subDays(90)->format('Y-m-d')) }}">
                <label>Tgl Awal Threshold</label>
            </div></div>
            <div class="col s12 m4"><button type="submit" class="btn blue"><i class="material-icons left">search</i> Cari Rekening Inaktif</button></div>
        </div>
    </form>

    @if($rows ?? null)
    <h6 class="blue-text" style="margin-top:1rem;">Rekening yang Perlu Ditutup ({{ count($rows) }})</h6>
    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>#</th><th>No. Akun</th><th>Nama</th><th>Product</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['SAVINGS_ACCOUNT_NO'] }}</td>
                        <td>{{ $row['MEMBER_NAME'] }}</td>
                        <td>{{ $row['SAVINGS_PRODUCT_ID'] == 2 ? 'Harian' : 'SSB' }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.savings.proses_close_by_user') }}" class="row-form">
                                @csrf
                                <input type="hidden" name="noAcc" value="{{ $row['SAVINGS_ACCOUNT_NO'] }}">
                                <button type="submit" class="btn small red" onclick="return confirm('Tutup rekening ini?')">
                                    <i class="material-icons left">delete</i> Tutup
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="center grey-text">Tidak ada rekening yang perlu ditutup</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div></div>
@stop
