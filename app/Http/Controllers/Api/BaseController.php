<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;

class BaseController extends Controller
{
    use ApiResponse;

    protected function validateRequest(array $rules, array $data): array
    {
        return validator($data, $rules)->validate();
    }
}