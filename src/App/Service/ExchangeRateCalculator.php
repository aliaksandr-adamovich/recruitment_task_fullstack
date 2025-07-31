<?php

namespace App\Service;

class ExchangeRateCalculator
{
    public function calculateRate(string $currency, float $mid, string $date): array
    {
        $buy = null;
        $sell = null;

        if (in_array($currency, ['EUR', 'USD'])) {
            $buy = round($mid - 0.15, 2);
            $sell = round($mid + 0.11, 2);
        } else {
            $sell = round($mid + 0.20, 2);
        }

        return [
            'currency' => $currency,
            'mid' => $mid,
            'buy' => $buy,
            'sell' => $sell,
            'date' => $date,
        ];
    }
}
