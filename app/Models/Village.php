<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $table = "ma_village";
    protected $primaryKey = "VILLAGE_ID";
    public $timestamps = false;
    protected $fillable = ["VILLAGE_NAME", "DISTRICT_ID"];

    public function district()
    {
        return $this->belongsTo(District::class, 'DISTRICT_ID', 'DISTRICT_ID');
    }
}
