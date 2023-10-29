<?php

namespace App\Dealers;

use Exception;

class DealerManager
{
    /**
     * @throws Exception
     */
    public static function handleDown(): void
    {
        $dealer = DealerOctober::instance();

        $dealer->openLong();
    }

    /**
     * @throws Exception
     */
    public static function handleUp(): void
    {
        $dealer = DealerOctober::instance('SHORT');

        $dealer->openShort();
    }

    public static function status(): void
    {
        $status = [];

        self::scan();

        $status['active_count'] = DealerOctober::query()->where('status', Dealer::STATUS_ACTIVE)->count();
        $status['new_count'] = DealerOctober::query()->where('status', Dealer::STATUS_NEW)->count();
    }

    public static function scan()
    {
        $dealers = DealerOctober::query()
            ->where('status', '!=', Dealer::STATUS_CLOSED)
            ->get();

        foreach ($dealers as $dealer) {
            /** var Dealer $dealer */
            $dealer->collectTrades();
        }
    }
}