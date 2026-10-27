<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $table = 'mu_loan';
    protected $primaryKey = 'LOAN_ID';
    public $timestamps = false;

    public function getRouteKeyName(): string
    {
        return 'LOAN_ID';
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'MEMBER_REGISTRY', 'MEMBER_REGISTRY');
    }
}
