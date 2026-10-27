@extends('admin.layouts')

@section('title', 'Tambah Module - SIMKOP')

@section('admin-content')
    <div class="card">
        <div class="card-content">
            <div class="row">
                <div class="col s12">
                    <h5 class="blue-text" style="font-size:1.5rem;">Tambah Module Baru</h5>
                    <a href="{{ route('admin.module.index') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>
                </div>
            </div>

            @if ($errors->any())
                <div class="card red lighten-5"><div class="card-content red-text"><ul style="padding-left:2rem;margin:0;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
            @endif

            <form method="POST" action="{{ route('admin.module.store') }}">
                @csrf
                <div class="row">
                    <div class="col s12 m8">
                        <div class="input-field">
                            <input type="text" name="MODULE_NAME" id="MODULE_NAME" value="{{ old('MODULE_NAME') }}" class="{{ $errors->has('MODULE_NAME') ? 'invalid' : '' }}" required>
                            <label for="MODULE_NAME">Nama Module *</label>
                            @if($errors->has('MODULE_NAME'))<span class="red-text">{{ $errors->first('MODULE_NAME') }}</span>@endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="input-field">
                            <textarea name="NOTES" id="NOTES" class="materialize-textarea">{{ old('NOTES') }}</textarea>
                            <label for="NOTES">Keterangan</label>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:1.5rem;">
                    <div class="col s12">
                        <button type="submit" class="btn blue"><i class="material-icons left">save</i> Simpan Module</button>
                        <a href="{{ route('admin.module.index') }}" class="btn grey" style="margin-left:8px;">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
