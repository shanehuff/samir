<?php

namespace Modules\ThreeCommas\Services;

use Modules\ThreeCommas\Contracts\ThreeCommasClientContract;

class ThreeCommasService
{
    protected ThreeCommasClientContract $threeCommas;

    public function __construct(ThreeCommasClientContract $threeCommas)
    {
        $this->threeCommas = $threeCommas;
    }

    public function ping()
    {
        try {
            return $this->threeCommas->ping();
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}