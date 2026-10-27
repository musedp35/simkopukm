<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = "ma_district";
    protected $primaryKey = "DISTRICT_ID";
    public $timestamps = false;
    protected $fillable = ["DISTRICT_NAME", "CITY_ID"];

    public function city()
    {
        return $this->belongsTo(City::class, 'CITY_ID', 'CITY_ID');
    }
}
