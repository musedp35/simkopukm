@extends('admin.layouts')
@section('title', 'Pembukaan Simpanan SSB - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Pembukaan Simpanan SSB (Product 3)</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    <h6 class="blue-text">Pilih Anggota</h6>
    <form method="GET" action="{{ route('admin.savings.ssb') }}">
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="idHiddenSSB" value="{{ request('idHiddenSSB') }}" required>
                <label>No. Anggota / No. KTP / No. Akun</label>
            </div></div>
            <div class="col s12 m6">
                <button type="submit" class="btn blue"><i class="material-icons left">search</i> Cari</button>
            </div>
        </div>
    </form>

    @if($detailSSB)
    @php $d = $detailSSB; @endphp
    <hr>
    <h6 class="blue-text">Data Anggota</h6>
    <div class="table-responsive">
        <table class="highlight">
            <tbody>
                <tr><th>Nama</th><td>{{ $d['MEMBER_NAME'] ?? '-' }}</td></tr>
                <tr><th>No. Anggota</th><td>{{ $d['MEMBER_NO'] ?? '-' }}</td></tr>
                <tr><th>No. Account SSB</th><td><strong>{{ $NO_ACC }}</strong></td></tr>
            </tbody>
        </table>
    </div>

    <h6 class="blue-text" style="margin-top:1rem;">Konstanta Jasa SSB</h6>
    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>#</th><th>Nama Jasa</th><th>Rate (%)</th></tr></thead>
            <tbody>
                @forelse($jasa ?? [] as $j)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $j['PRODUCT_NAME'] ?? '' }}</td>
                        <td>{{ $j['INTEREST_RATE'] ?? '0' }}%</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="center grey-text">Belum ada konstanta jasa SSB</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @else
    <p class="grey-text" style="margin-top:1rem;">Masukkan no. anggota / KTP untuk melihat form pembukaan SSB.</p>
    @endif
</div></div>
@stop
