<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TypeCity extends Model
{
    protected $table = "ma_type_city";
    protected $primaryKey = "TYPE_CITY_ID";
    public $timestamps = false;
    protected $fillable = ["TYPE_CITY_CODE", "TYPE_CITY_NAME"];
}
