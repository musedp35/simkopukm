<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'loanAccountNo'   => 'nullable|string|max:50',
            'amount'         => 'nullable|numeric|min:0',
            'loanAmount'     => 'nullable|numeric|min:0',
            'loanPercent'    => 'nullable|numeric|min:0|max:100',
            'installmentTotal' => 'nullable|integer|min:1',
            'loanProduct'    => 'nullable|string|max:20',
            'requestDate'    => 'nullable|date',
            'newInstallmentTotal' => 'nullable|integer|min:1',
        ];
    }
}
