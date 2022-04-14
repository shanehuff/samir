<?php

namespace Modules\ThreeCommas\Services;

use Modules\ThreeCommas\Contracts\ThreeCommasClientContract;

class ThreeCommasService
{
    protected ThreeCommasClientContract $threeCommas;

    /**
     * Constructor
     */
    public function __construct(ThreeCommasClientContract $threeCommas)
    {
        $this->threeCommas = $threeCommas;
    }

    /**
     * Ping connectivity to ThreeCommas
     */
    public function ping()
    {
        try {
            return $this->threeCommas->ping();
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Get list of bots
     * 
     * @return array
     */
    public function getBots(): array
    {
        try {
            return $this->threeCommas->bots();
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Get a bot information
     *
     */
    public function getBotInfo(int $id): array
    {
        try {
            return $this->threeCommas->botInfo($id);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}