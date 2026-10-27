@extends('admin.layouts')
@section('title', 'Tambah Biaya - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Tambah Biaya</h5>
    <a href="{{ route('admin.fee.index') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>
    @if($errors->any())
        <div class="card red lighten-5"><div class="card-content red-text"><ul style="padding-left:2rem;margin:0;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div></div>
    @endif
    <form method="POST" action="{{ route('admin.fee.store') }}">
        @csrf
        <div class="row">
            <div class="col s12"><div class="input-field">
                <input type="text" name="FEE_NAME" value="{{ old('FEE_NAME') }}" required>
                <label>Nama Biaya *</label>
            </div></div>
        </div>
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="FEE_TYPE" value="{{ old('FEE_TYPE') }}">
                <label>Tipe / Kategori Biaya</label>
            </div></div>
            <div class="col s12 m6"><div class="input-field">
                <input type="number" name="FEE_AMOUNT" value="{{ old('FEE_AMOUNT') }}" required min="0">
                <label>Jumlah / Nominal (Rp) *</label>
            </div></div>
        </div>
        <div class="row"><div class="col s12">
            <button type="submit" class="btn blue"><i class="material-icons left">save</i> Simpan</button>
            <a href="{{ route('admin.fee.index') }}" class="btn grey" style="margin-left:8px;">Batal</a>
        </div></div>
    </form>
</div></div>
@stop
