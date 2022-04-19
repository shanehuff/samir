<?php

namespace App\Commander;

use App\Models\Commander;

class Risk
{
    protected int $leverage = 20;

    protected ?float $liquidationPrice = null;

    protected ?float $margin = null;

    protected ?float $availableMargin = null;

    protected const BINANCE_RATIO = 1.0256;

    protected Commander $commander;

    /**
     * @return float|null
     */
    public function getLiquidationPrice(): ?float
    {
        return $this->liquidationPrice;
    }

    public function setLiquidationPrice($liquidationPrice): void
    {
        $this->liquidationPrice = $liquidationPrice;
    }

    /**
     * @return int
     */
    public function getLeverage(): int
    {
        return $this->leverage;
    }

    /**
     * @param int $leverage
     */
    public function setLeverage(int $leverage): void
    {
        $this->leverage = $leverage;
    }

    public function setCommander(Commander $commander)
    {
        $this->commander = $commander;
    }

    public function getCommander(): Commander
    {
        return $this->commander;
    }

    public function calculate()
    {
        $position = $this->commander->getPosition();

        $this->margin = $position->getBuySize() / $this->getLeverage();
        $this->availableMargin = $this->commander->fund - $this->getMargin();
        $this->liquidationPrice = ($position->getBuySize() - $this->getAvailableMargin() - $this->getMargin())
            / $position->getBuySizeInSymbol() * self::BINANCE_RATIO;
    }

    /**
     * @return float|null
     */
    public function getMargin(): ?float
    {
        return $this->margin;
    }

    /**
     * @param float|null $margin
     */
    public function setMargin(?float $margin): void
    {
        $this->margin = $margin;
    }

    /**
     * @return float|null
     */
    public function getAvailableMargin(): ?float
    {
        return $this->availableMargin;
    }

    /**
     * @param float|null $availableMargin
     */
    public function setAvailableMargin(?float $availableMargin): void
    {
        $this->availableMargin = $availableMargin;
    }
}
