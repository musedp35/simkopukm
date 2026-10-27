<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transId'       => 'required|integer|min:0',
            'chbApproval'   => 'required|integer|in:0,1',
            'loanAccountNo' => 'required|string|max:50',
            'level'         => 'required|string|in:ao3,ao4,ao6,kasie',
            'approve'       => 'required|integer|in:0,1',
        ];
    }
}
