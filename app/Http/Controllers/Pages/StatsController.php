<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use Laravel\Jetstream\Jetstream;

class StatsController
{
    public function __invoke(Request $request)
    {
        return Jetstream::inertia()->render($request, 'Stats');
    }
}