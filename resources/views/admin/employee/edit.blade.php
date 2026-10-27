@extends('admin.layouts')
@section('title', 'Edit Karyawan - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Edit: {{ $employee->EMPLOYEE_NAME }}</h5>
    <a href="{{ route('admin.employee.index') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>
    @if($errors->any())
        <div class="card red lighten-5"><div class="card-content red-text"><ul style="padding-left:2rem;margin:0;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div></div>
    @endif
    <form method="POST" action="{{ route('admin.employee.update', $employee->EMPLOYEE_ID) }}">
        @csrf @method('PUT')
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="EMPLOYEE_NAME" value="{{ old('EMPLOYEE_NAME', $employee->EMPLOYEE_NAME) }}" required>
                <label>Nama Karyawan *</label>
            </div></div>
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="EMPLOYEE_NIP" value="{{ old('EMPLOYEE_NIP', $employee->EMPLOYEE_NIP) }}" required>
                <label>NIP / NIK *</label>
            </div></div>
        </div>
        <div class="row">
            <div class="col s12"><div class="input-field">
                <input type="text" name="EMPLOYEE_POSITION" value="{{ old('EMPLOYEE_POSITION', $employee->EMPLOYEE_POSITION) }}">
                <label>Posisi / Jabatan</label>
            </div></div>
        </div>
        <div class="row"><div class="col s12">
            <button type="submit" class="btn blue"><i class="material-icons left">save</i> Perbarui</button>
            <a href="{{ route('admin.employee.index') }}" class="btn grey" style="margin-left:8px;">Batal</a>
        </div></div>
    </form>
</div></div>
@stop
