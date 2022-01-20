<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
Use App\Rules\FQDNRule;

class CompanyDataRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_domain' => ['required', 'string', new FQDNRule(), 'max:253'],
            'company_name'   => 'nullable|string'
        ];
    }
}
