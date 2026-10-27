<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $table = "ma_audit";
    public $timestamps = false;
    protected $fillable = [
        "USER_ID", "MODULE_NAME", "ACTION", "IP_ADDRESS", "CREATED_AT"
    ];
}
