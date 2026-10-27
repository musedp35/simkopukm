@extends('admin.layouts')
@section('title', 'Tambah Desa - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Tambah Desa / Kelurahan</h5>
    <a href="{{ route('admin.location.district') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>
    @if($errors->any())
        <div class="card red lighten-5"><div class="card-content red-text"><ul style="padding-left:2rem;margin:0;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div></div>
    @endif
    <form method="POST" action="{{ route('admin.location.district.store') }}">
        @csrf
        <div class="row">
            <div class="col s12 m6">
                <div class="input-field">
                    <input type="text" name="DISTRICT_NAME" value="{{ old('DISTRICT_NAME') }}" class="{{ $errors->has('DISTRICT_NAME') ? 'invalid' : '' }}" required>
                    <label>Nama Desa / Kelurahan *</label>
                    @if($errors->has('DISTRICT_NAME'))<span class="red-text">{{ $errors->first('DISTRICT_NAME') }}</span>@endif
                </div>
            </div>
            <div class="col s12 m6">
                <div class="input-field">
                    <select name="DISTRICT_ID">
                        <option value="">-- Kecamatan --</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->DISTRICT_ID }}" {{ old('DISTRICT_ID') == $d->DISTRICT_ID ? 'selected' : '' }}>{{ $d->DISTRICT_NAME }}</option>
                        @endforeach
                    </select>
                    <label>Kecamatan</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <button type="submit" class="btn blue"><i class="material-icons left">save</i> Simpan</button>
                <a href="{{ route('admin.location.district') }}" class="btn grey" style="margin-left:8px;">Batal</a>
            </div>
        </div>
    </form>
    <script>document.querySelectorAll('select').forEach(function(s){M.FormSelect.init(s);});</script>
</div></div>
@stop
