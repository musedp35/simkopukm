@extends('admin.layouts')
@section('title', 'Tambah Kota - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Tambah Kota / Kabupaten</h5>
    <a href="{{ route('admin.location.city') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>
    @if($errors->any())
        <div class="card red lighten-5"><div class="card-content red-text"><ul style="padding-left:2rem;margin:0;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div></div>
    @endif
    <form method="POST" action="{{ route('admin.location.city.store') }}">
        @csrf
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="CITY_NAME" value="{{ old('CITY_NAME') }}" required>
                <label>Nama Kota / Kabupaten *</label>
            </div></div>
            <div class="col s12 m6"><div class="input-field">
                <select name="TYPE_CITY_ID">
                    <option value="">-- Tipe --</option>
                    @foreach($typeCities as $t)
                        <option value="{{ $t->TYPE_CITY_ID }}" {{ old('TYPE_CITY_ID') == $t->TYPE_CITY_ID ? 'selected' : '' }}>{{ $t->TYPE_CITY_NAME }}</option>
                    @endforeach
                </select>
                <label>Tipe Kota</label>
            </div></div>
        </div>
        <div class="row"><div class="col s12">
            <button type="submit" class="btn blue"><i class="material-icons left">save</i> Simpan</button>
            <a href="{{ route('admin.location.city') }}" class="btn grey" style="margin-left:8px;">Batal</a>
        </div></div>
    </form>
    <script>document.querySelectorAll('select').forEach(function(s){M.FormSelect.init(s);});</script>
</div></div>
@stop
