@extends('admin.layouts')

@section('title', 'Edit Role - SIMKOP')

@section('admin-content')
    <div class="card">
        <div class="card-content">
            <div class="row">
                <div class="col s12">
                    <h5 class="blue-text" style="font-size:1.5rem;">Edit Role: {{ $role->ROLE_NAME }}</h5>
                    <a href="{{ route('admin.role.index') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>
                </div>
            </div>

            @if ($errors->any())
                <div class="card red lighten-5"><div class="card-content red-text"><ul style="padding-left:2rem;margin:0;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
            @endif

            <form method="POST" action="{{ route('admin.role.update', $role->ROLE_ID) }}">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col s12 m6">
                        <div class="input-field">
                            <input type="text" name="ROLE_NAME" id="ROLE_NAME" value="{{ old('ROLE_NAME', $role->ROLE_NAME) }}" class="{{ $errors->has('ROLE_NAME') ? 'invalid' : '' }}" required>
                            <label for="ROLE_NAME">Nama Role *</label>
                            @if($errors->has('ROLE_NAME'))<span class="red-text">{{ $errors->first('ROLE_NAME') }}</span>@endif
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="input-field">
                            <select name="MODULE_ID">
                                <option value="">-- Tanpa Module --</option>
                                @foreach($modules as $m)
                                    <option value="{{ $m->MODULE_ID }}" {{ old('MODULE_ID', $role->MODULE_ID) == $m->MODULE_ID ? 'selected' : '' }}>{{ $m->MODULE_NAME }}</option>
                                @endforeach
                            </select>
                            <label>Module Akses</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="input-field">
                            <textarea name="NOTES" id="NOTES" class="materialize-textarea">{{ old('NOTES', $role->NOTES) }}</textarea>
                            <label for="NOTES">Keterangan</label>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:1.5rem;">
                    <div class="col s12">
                        <button type="submit" class="btn blue"><i class="material-icons left">save</i> Perbarui Role</button>
                        <a href="{{ route('admin.role.index') }}" class="btn grey" style="margin-left:8px;">Batal</a>
                    </div>
                </div>
            </form>
            <script>document.querySelectorAll('select').forEach(function(s){M.FormSelect.init(s);});</script>
        </div>
    </div>
@stop
