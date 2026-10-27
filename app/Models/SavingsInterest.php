<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SavingsInterest extends Model
{
    protected $table = "mu_savings_interest";
    protected $primaryKey = "SAVINGS_INTEREST_ID";
    public $timestamps = false;
    protected $fillable = [
        "SAVINGS_INTEREST_NAME", "SAVINGS_INTEREST_RATE",
        "SAVINGS_PRODUCT_ID", "ACTIVE_STATUS", "MEMBER_STATUS_ID",
        "TAX_EXPENSE_RATE", "INTEREST_EXPENSE_NOMINAL", "LOGTIME"
    ];
}
