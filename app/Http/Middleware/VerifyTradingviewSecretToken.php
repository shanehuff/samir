<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class VerifyTradingviewSecretToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->input('secret');
        $tokenToCheck = config('tradingview.secret');

        if (empty($token) || $tokenToCheck !== $token) {
            return abort(401);
        }

        return $next($request);
    }
}
