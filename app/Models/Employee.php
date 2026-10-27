<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = "ma_employee";
    protected $primaryKey = "EMPLOYEE_ID";
    public $timestamps = false;
    protected $fillable = [
        "EMPLOYEE_NAME", "EMPLOYEE_NIP", "EMPLOYEE_POSITION", "STATUS", "LOGTIME"
    ];
}
