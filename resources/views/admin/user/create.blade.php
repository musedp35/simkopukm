@extends('admin.layouts')
@section('title', 'Buat Akun - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Buat Akun Login Baru</h5>
    <a href="{{ route('admin.user.index') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>
    @if($errors->any())
        <div class="card red lighten-5"><div class="card-content red-text"><ul style="padding-left:2rem;margin:0;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div></div>
    @endif
    <form method="POST" action="{{ route('admin.user.store') }}">
        @csrf
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <select name="MEMBER_REGISTRY" required>
                    <option value="">-- Pilih Member --</option>
                    @foreach($members as $m)
                        <option value="{{ $m->MEMBER_REGISTRY }}" {{ old('MEMBER_REGISTRY') == $m->MEMBER_REGISTRY ? 'selected' : '' }}>
                            {{ $m->MEMBER_NAME }} ({{ $m->MEMBER_REGISTRY }})
                        </option>
                    @endforeach
                </select>
                <label>Member / Pengguna *</label>
            </div></div>
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="USERNAME" value="{{ old('USERNAME') }}" required>
                <label>Username *</label>
            </div></div>
        </div>
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <select name="ROLE_ID" required>
                    <option value="">-- Pilih Role --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->ROLE_ID }}" {{ old('ROLE_ID') == $r->ROLE_ID ? 'selected' : '' }}>{{ $r->ROLE_NAME }}</option>
                    @endforeach
                </select>
                <label>Role *</label>
            </div></div>
            <div class="col s12 m6"><div class="input-field">
                <input type="password" name="PASSWORD" required>
                <label>Password (minimal 6 karakter) *</label>
            </div></div>
        </div>
        <div class="row"><div class="col s12">
            <button type="submit" class="btn blue"><i class="material-icons left">save</i> Simpan Akun</button>
            <a href="{{ route('admin.user.index') }}" class="btn grey" style="margin-left:8px;">Batal</a>
        </div></div>
    </form>
    <script>document.querySelectorAll('select').forEach(function(s){M.FormSelect.init(s);});</script>
</div></div>
@stop
