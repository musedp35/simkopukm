<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = "ma_city";
    protected $primaryKey = "CITY_ID";
    public $timestamps = false;
    protected $fillable = ["CITY_NAME", "TYPE_CITY_ID"];

    public function typeCity()
    {
        return $this->belongsTo(TypeCity::class, 'TYPE_CITY_ID', 'TYPE_CITY_ID');
    }
}
