<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

class PaymentMethodController extends Controller
{
    public function index(): JsonResponse
    {
        $methods = PaymentMethod::where('is_active', true)->get();
        return ApiResponse::success($methods, 'Payment methods retrieved successfully');
    }
}

