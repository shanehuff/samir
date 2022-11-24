<?php

namespace App\Http\Controllers\Pages;

class StatsController
{
    public function __invoke()
    {
        return Jetstream::inertia()->render($request, 'Pages/Stats')
    }
}