@extends('admin.layouts')
@section('title', 'Pembukaan Simpanan Harian - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Pembukaan Simpanan Harian</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    @if(session('alert-success'))
        <div class="card green lighten-5"><div class="card-content green-text">{{ session('alert-success') }}</div></div>
    @endif

    {{-- Cari Nomor Urut / No Anggota --}}
    <h6 class="blue-text">Pilih Anggota</h6>
    <form method="GET" action="{{ route('admin.savings.harian') }}">
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="idHiddenSimHar" value="{{ request('idHiddenSimHar') }}" required>
                <label>No. Anggota / No. KTP / No. Akun</label>
            </div></div>
            <div class="col s12 m6">
                <button type="submit" class="btn blue"><i class="material-icons left">search</i> Cari</button>
            </div>
        </div>
    </form>

    @if($detailSimHar)
    @php $d = $detailSimHar; @endphp
    <hr>
    <h6 class="blue-text">Data Anggota</h6>
    <div class="table-responsive">
        <table class="highlight">
            <tbody>
                <tr><th>Nama / Status</th><td>{{ $d['MEMBER_NAME'] ?? '-' }} / {{ (($d['MEMBER_STATUS_ID'] ?? 0) == 4 ? 'NTR' : 'TR') }}</td></tr>
                <tr><th>No. Anggota</th><td>{{ $d['MEMBER_NO'] ?? '-' }}</td></tr>
                <tr><th>Alamat</th><td>{{ $d['MEMBER_ADDRESS'] ?? '-' }}</td></tr>
                <tr><th>No. Account</th><td><strong>{{ $NO_ACC }}</strong></td></tr>
            </tbody>
        </table>
    </div>

    <h6 class="blue-text" style="margin-top:1rem;">Form Pembukaan</h6>
    <form method="POST" action="{{ route('admin.savings.store_simhar') }}">
        @csrf
        <input type="hidden" name="edtNoAcc" value="{{ $NO_ACC }}">
        <input type="hidden" name="memberRegistry" value="{{ $memberRegistry ?? '' }}">
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="edtNamaTab" value="{{ $d['MEMBER_NAME'] ?? '' }}" required>
                <label>Nama Tabungan</label>
            </div></div>
            <div class="col s6 m3"><div class="input-field">
                <input type="radio" id="chbStatus1" name="chbStatus" value="1" checked>
                <label for="chbStatus1">Aktif</label>
                <input type="radio" id="chbStatus2" name="chbStatus" value="0" style="margin-top:5px;">
                <label for="chbStatus2">Tidak Aktif</label>
            </div></div>
            <div class="col s6 m3"><div class="input-field">
                <input type="radio" id="chbPotongan1" name="chbPotongan" value="1" checked>
                <label for="chbPotongan1">Ya</label>
                <input type="radio" id="chbPotongan2" name="chbPotongan" value="0" style="margin-top:5px;">
                <label for="chbPotongan2">Tidak</label>
            </div></div>
        </div>
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <input type="number" step="0.01" name="blokBalance" value="{{ old('blokBalance') }}">
                <label>Saldo Blokir (opsional)</label>
            </div></div>
        </div>
        <button type="submit" class="btn blue accent-4"><i class="material-icons left">check</i> Simpan</button>
    </form>
    @else
    <p class="grey-text" style="margin-top:1rem;">Masukkan no. anggota / KTP / no. akun untuk melihat form pembukaan.</p>
    @endif

    <hr>
    <h6 class="blue-text">Konstanta Jasa (Product 2)</h6>
    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>#</th><th>Nama Jasa</th><th>Rate (%)</th></tr></thead>
            <tbody>
                @forelse($jasa as $j)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $j['PRODUCT_NAME'] ?? '' }}</td>
                        <td>{{ $j['INTEREST_RATE'] ?? '0' }}%</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="center grey-text">Belum ada konstanta jasa</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@stop
