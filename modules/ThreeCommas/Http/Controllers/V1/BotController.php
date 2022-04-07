<?php

namespace Modules\ThreeCommas\Http\Controllers\V1;

use Illuminate\Routing\Controller;
use Modules\ThreeCommas\Services\ThreeCommasService;
use Illuminate\Http\Request;

class BotController extends Controller
{
    protected $service;

    public function __construct(ThreeCommasService $service)
    {
        $this->service = $service;
    }

    public function show($id)
    {
        try {
            return $this->service->getBotInfo($id);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * @todo make individual request for this one
     */
    public function index(Request $request)
    {
        try {
            return $this->service->getBots($request->all());
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}