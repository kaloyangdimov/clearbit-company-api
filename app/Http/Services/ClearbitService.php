<?php

namespace App\Http\Services;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;

class ClearbitService extends Controller
{
    private $baseEndpoint = "https://company.clearbit.com/v2/companies/find";

    public function domainLookup(array $attributes)
    {
        $requestData = [
            'domain' => $attributes['company_domain']
        ];

        if (isset($attributes['company_name'])) {
            array_push($requestData, ['company_name' => $attributes['company_name']]);
        }

        $response = Http::withToken(env('CLEARBIT_API_KEY'))->get($this->baseEndpoint,
            $requestData
        );

        return $response;
    }
}
