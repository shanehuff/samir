<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\ThreeCommas\Services\ThreeCommasService;

class TestController extends Controller
{
    public function test(ThreeCommasService $service)
    {
        return response()->json([
            'data' => $service->ping()
        ]);
    }
}
