<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $table = "ma_fee";
    protected $primaryKey = "FEE_ID";
    public $timestamps = false;
    protected $fillable = ["FEE_NAME", "FEE_AMOUNT", "FEE_TYPE", "LOGTIME"];
}
