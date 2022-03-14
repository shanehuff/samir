<?php

namespace Modules\ThreeCommas\Contracts;

interface ThreeCommasClientContract
{
    /**
     * Set base URL for 3Commas API
     * 
     * @param string $uri Base URI of 3Commas
     * @see https://github.com/3commas-io/3commas-official-api-docs
     * @return self
     */
    public function setBaseURI(string $uri): self;
}