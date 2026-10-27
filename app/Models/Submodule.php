<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent Model: Submodule
 * Tabel: ma_submodule
 * 
 * Pengganti model CI3: application/models/Submodule.php
 * Konvensi: nama model UpperCamelCase, tabel lower_snake_case, method camelCase
 */
class Submodule extends Model
{
    protected $table = 'ma_submodule';
    protected $primaryKey = 'SUBMODULE_ID';
    public $timestamps = false;

    protected $fillable = [
        'SUBMODULE_NAME',
        'NOTES',
        'MODULE_ID',
        'UPDATE_BY',
        'LOGTIME',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'MODULE_ID', 'MODULE_ID');
    }

    public function updateSubModule($data, $id)
    {
        $username = auth()->user()->USERNAME ?? '';

        return static::where('SUBMODULE_ID', $id)
            ->update([
                'SUBMODULE_NAME' => $data['edtNamaSubmodule'] ?? '',
                'NOTES' => $data['edtKeterangan'] ?? '',
                'MODULE_ID' => $data['cmbAkses'] ?? '',
                'UPDATE_BY' => $username,
                'LOGTIME' => now()->format('Y-m-d H:i:s'),
            ]);
    }
}