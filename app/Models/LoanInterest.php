<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LoanInterest extends Model
{
    protected $table = "mu_loan_interest";
    protected $primaryKey = "LOAN_INTEREST_ID";
    public $timestamps = false;
    protected $fillable = [
        "LOAN_INTEREST_NAME", "LOAN_INTEREST_RATE",
        "LOAN_PRODUCT_ID", "ACTIVE_status", "LOGTIME"
    ];
}
