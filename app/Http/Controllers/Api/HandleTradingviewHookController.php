<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HandleTradingviewHookController extends Controller
{
    public function __invoke(Request $request)
    {
        ray($request->input('secret'));
    }
}
