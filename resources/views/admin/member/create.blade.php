@extends('admin.layouts')

@section('title', 'Tambah Member - SIMKOP')

@section('admin-content')
    <div class="card">
        <div class="card-content">
            <div class="row">
                <div class="col s12">
                    <h5 class="blue-text" style="font-size: 1.5rem;">Tambah Member Baru</h5>
                    <a href="{{ route('admin.member.index') }}" class="grey-text" style="display:block; margin-bottom:1rem;">
                        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali ke daftar
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="card red lighten-5">
                    <div class="card-content red-text">
                        <ul style="padding-left: 2rem; margin:0;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.member.store') }}">
                @csrf
                <div class="row">
                    <div class="col s12 m6">
                        <div class="input-field">
                            <input type="text" name="MEMBER_NO" id="MEMBER_NO" value="{{ old('MEMBER_NO') }}" class="{{ $errors->has('MEMBER_NO') ? 'invalid' : '' }}" required>
                            <label for="MEMBER_NO">No. Member *</label>
                            @if($errors->has('MEMBER_NO'))<span class="red-text">{{ $errors->first('MEMBER_NO') }}</span>@endif
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="input-field">
                            <input type="text" name="MEMBER_NAME" id="MEMBER_NAME" value="{{ old('MEMBER_NAME') }}" class="{{ $errors->has('MEMBER_NAME') ? 'invalid' : '' }}" required>
                            <label for="MEMBER_NAME">Nama Lengkap *</label>
                            @if($errors->has('MEMBER_NAME'))<span class="red-text">{{ $errors->first('MEMBER_NAME') }}</span>@endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m6">
                        <div class="input-field">
                            <input type="email" name="MEMBER_EMAIL" id="MEMBER_EMAIL" value="{{ old('MEMBER_EMAIL') }}">
                            <label for="MEMBER_EMAIL">Email</label>
                            @if($errors->has('MEMBER_EMAIL'))<span class="red-text">{{ $errors->first('MEMBER_EMAIL') }}</span>@endif
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="input-field">
                            <input type="text" name="BANKING_ACCOUNT_NO" id="BANKING_ACCOUNT_NO" value="{{ old('BANKING_ACCOUNT_NO') }}">
                            <label for="BANKING_ACCOUNT_NO">No. Rekening Bank</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="input-field">
                            <textarea name="ADDRESS_STAY" id="ADDRESS_STAY" class="materialize-textarea">{{ old('ADDRESS_STAY') }}</textarea>
                            <label for="ADDRESS_STAY">Alamat Tempat Tinggal</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m4">
                        <div class="input-field">
                            <select name="VILLAGE_ID">
                                <option value="">-- Desa / Kelurahan --</option>
                                @foreach($villages as $v)
                                    <option value="{{ $v->VILLAGE_ID }}" {{ old('VILLAGE_ID') == $v->VILLAGE_ID ? 'selected' : '' }}>{{ $v->VILLAGE_NAME }}</option>
                                @endforeach
                            </select>
                            <label>Desa / Kelurahan</label>
                        </div>
                    </div>
                    <div class="col s12 m4">
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
                    <div class="col s12 m4">
                        <div class="input-field">
                            <select name="CITY_ID">
                                <option value="">-- Kota / Kab --</option>
                                @foreach($cities as $c)
                                    <option value="{{ $c->CITY_ID }}" {{ old('CITY_ID') == $c->CITY_ID ? 'selected' : '' }}>{{ $c->CITY_NAME }}</option>
                                @endforeach
                            </select>
                            <label>Kota / Kabupaten</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m4">
                        <div class="input-field">
                            <select name="MEMBER_STATUS_ID" class="{{ $errors->has('MEMBER_STATUS_ID') ? 'invalid' : '' }}">
                                <option value="">-- Status Member --</option>
                                @foreach($statuses as $st)
                                    <option value="{{ $st->MEMBER_STATUS_ID }}" {{ old('MEMBER_STATUS_ID') == $st->MEMBER_STATUS_ID ? 'selected' : '' }}>{{ $st->MEMBER_STATUS_NAME }}</option>
                                @endforeach
                            </select>
                            <label>Status Member *</label>
                            @if($errors->has('MEMBER_STATUS_ID'))<span class="red-text">{{ $errors->first('MEMBER_STATUS_ID') }}</span>@endif
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 1.5rem;">
                    <div class="col s12">
                        <button type="submit" class="btn blue">
                            <i class="material-icons left">save</i> Simpan Member
                        </button>
                        <a href="{{ route('admin.member.index') }}" class="btn grey" style="margin-left: 8px;">Batal</a>
                    </div>
                </div>
            </form>
            <script>document.querySelectorAll('select').forEach(function(s){M.FormSelect.init(s);});</script>
        </div>
    </div>
@stop
