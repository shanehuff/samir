<?php

namespace Modules\ThreeCommas\Interactions;
use Illuminate\Http\Client\Response;

trait InteractWith3CommasReponse
{
    /**
     * Decode response to json
     * 
     * @param Response $response
     * @return mixed
     */
    public function decodeResponseJson(Response $reponse): mixed
    {
        try {
            return $reponse->json();
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}