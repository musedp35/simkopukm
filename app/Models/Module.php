<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent Model: Module
 * Tabel: ma_module
 * 
 * Pengganti model CI3: application/models/Module.php
 * Konvensi: nama model UpperCamelCase, tabel lower_snake_case, method camelCase
 */
class Module extends Model
{
    protected $table = 'ma_module';
    protected $primaryKey = 'MODULE_ID';
    public $timestamps = false;

    protected $fillable = [
        'MODULE_ID',
        'MODULE_NAME',
        'NOTES',
        'UPDATE_BY',
        'LOGTIME',
    ];

    public function roles()
    {
        return $this->hasMany(Role::class, 'MODULE_ID', 'MODULE_ID');
    }

    public function submodules()
    {
        return $this->hasMany(Submodule::class, 'MODULE_ID', 'MODULE_ID');
    }

    public function getModule()
    {
        return static::all()->toArray();
    }

    public function getSubModule()
    {
        return Submodule::select('ma_submodule.*', 'ma_module.MODULE_NAME')
            ->join('ma_module', 'ma_module.MODULE_ID = ma_submodule.MODULE_ID', 'left outer')
            ->get()
            ->toArray();
    }

    public function updateModule($data, $id)
    {
        $username = auth()->user()->USERNAME ?? '';

        return static::where('MODULE_ID', $id)
            ->update([
                'MODULE_NAME' => $data['edtNamaModule'] ?? '',
                'NOTES' => $data['edtKeterangan'] ?? '',
                'UPDATE_BY' => $username,
                'LOGTIME' => now()->format('Y-m-d H:i:s'),
            ]);
    }

    public function getDetailModule($id)
    {
        return static::where('MODULE_ID', $id)->first();
    }
}