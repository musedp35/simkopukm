<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MemberStatus extends Model
{
    protected $table = "ma_member_status";
    protected $primaryKey = "MEMBER_STATUS_ID";
    public $timestamps = false;
    protected $fillable = ["MEMBER_STATUS_NAME"];
}
