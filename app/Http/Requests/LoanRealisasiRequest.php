<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanRealisasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'loanAccountNo' => 'required|string|max:50',
            'amount'        => 'required|numeric|min:0.01',
        ];
    }
}
