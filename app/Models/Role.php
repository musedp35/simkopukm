<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent Model: Role
 * Tabel: ma_role
 * 
 * Pengganti model CI3: application/models/Role.php
 * Konvensi: nama model UpperCamelCase, tabel lower_snake_case, method camelCase
 */
class Role extends Model
{
    protected $table = 'ma_role';
    protected $primaryKey = 'ROLE_ID';
    public $timestamps = false;

    protected $fillable = [
        'ROLE_ID',
        'ROLE_NAME',
        'NOTES',
        'MODULE_ID',
        'UPDATE_BY',
        'LOGTIME',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'MODULE_ID', 'MODULE_ID');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'ROLE_ID', 'ROLE_ID');
    }

    public function getRole()
    {
        return static::select('ma_role.*', 'mm.*')
            ->join('ma_module as mm', 'mm.MODULE_ID = ma_role.MODULE_ID', 'left outer')
            ->get()
            ->toArray();
    }

    public function addNewRole($data)
    {
        $username = auth()->user()->USERNAME ?? '';

        return static::insert([
            'ROLE_NAME' => $data['edtNamaRole'],
            'NOTES' => $data['edtKeterangan'],
            'MODULE_ID' => $data['cmbAkses'],
            'LOGTIME' => now()->format('Y-m-d H:i:s'),
            'UPDATE_BY' => $username,
        ]);
    }

    public function updateRole($data, $id)
    {
        $username = auth()->user()->USERNAME ?? '';

        return static::where('ROLE_ID', $id)
            ->update([
                'ROLE_NAME' => $data['edtNamaRole'],
                'NOTES' => $data['edtKeterangan'],
                'MODULE_ID' => $data['cmbAkses'],
                'UPDATE_BY' => $username,
                'LOGTIME' => now()->format('Y-m-d H:i:s'),
            ]);
    }

    public function dropRole($id)
    {
        return static::where('ROLE_ID', $id)->delete();
    }

    public function getDetailRole($idRole)
    {
        return static::where('ROLE_ID', $idRole)->first();
    }
}