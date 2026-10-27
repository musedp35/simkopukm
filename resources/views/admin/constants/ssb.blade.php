@extends('admin.layouts')
@section('title', 'Konstanta SSB - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="teal-text" style="font-size:1.5rem;">Konstanta SSB (Product ID: 3)</h5>
    <a href="{{ route('admin.constants.index') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>

    @if(session('alert-success'))
        <div class="card green lighten-5"><div class="card-content green-text">{{ session('alert-success') }}</div></div>
    @endif

    <h6 class="teal-text">Jasa / Interest</h6>
    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>#</th><th>Nama Konstanta</th><th>Rate (%)</th><th>Pajak</th><th>Nominal Min</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($interests as $i)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $i->SAVINGS_INTEREST_NAME }}</td>
                        <td>{{ $i->SAVINGS_INTEREST_RATE }}%</td>
                        <td>{{ $i->TAX_EXPENSE_RATE ?? 0 }}%</td>
                        <td>Rp {{ number_format($i->INTEREST_EXPENSE_NOMINAL ?? 0, 0, ',', '.') }}</td>
                        <td><span class="chip green white-text">Aktif</span></td>
                        <td>
                            <form method="POST" action="{{ route('admin.constants.delete_savings_interest', $i->SAVINGS_INTEREST_ID) }}" class="row-form" onsubmit="return confirm('Nonaktifkan?');">
                                @csrf @method('DELETE')
                                <button class="btn red small"><i class="material-icons">delete</i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="center grey-text">Belum ada konstanta</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Form Tambah / Edit Konstanta --}}
    <h6 class="teal-text" style="margin-top:1rem;">Tambah Konstanta SSB</h6>
    <form method="POST" action="{{ route('admin.constants.save_savings_interest') }}">
        @csrf
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="SAVINGS_INTEREST_NAME" value="{{ old('SAVINGS_INTEREST_NAME') }}" required>
                <label>Nama Konstanta</label>
            </div></div>
            <div class="col s12 m6"><div class="input-field">
                <input type="number" name="SAVINGS_INTEREST_RATE" step="0.0001" value="{{ old('SAVINGS_INTEREST_RATE') }}" required>
                <label>Rate (%)</label>
            </div></div>
        </div>
        <div class="row">
            <div class="col s12 m4"><div class="input-field">
                <input type="number" name="TAX_EXPENSE_RATE" step="0.0001" value="{{ old('TAX_EXPENSE_RATE', 0) }}">
                <label>Rate Pajak (%)</label>
            </div></div>
            <div class="col s12 m4"><div class="input-field">
                <input type="number" name="INTEREST_EXPENSE_NOMINAL" value="{{ old('INTEREST_EXPENSE_NOMINAL', 0) }}">
                <label>Nominal Min Jasa</label>
            </div></div>
            <div class="col s12 m4"><div class="input-field">
                <input type="number" name="MEMBER_STATUS_ID" value="{{ old('MEMBER_STATUS_ID') }}">
                <label>Member Status ID</label>
            </div></div>
        </div>
        <input type="hidden" name="SAVINGS_PRODUCT_ID" value="3">
        <button type="submit" class="btn teal"><i class="material-icons left">save</i> Simpan</button>
    </form>

    <h6 class="teal-text" style="margin-top:1rem;">Parameter Produk</h6>
    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>#</th><th>Parameter</th><th>Nilai</th><th>Update</th></tr></thead>
            <tbody>
                @forelse($supports as $s)
                    @if(is_array($s))
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $s['name'] }}</td>
                        <td>{{ $s['value'] }}</td>
                        <td>{{ $s['logtime'] ? \Carbon\Carbon::parse($s['logtime'])->format('d M Y H:i') : '-' }}</td>
                    </tr>
                    @endif
                @empty
                    <tr><td colspan="4" class="center grey-text">Belum ada parameter</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@stop
