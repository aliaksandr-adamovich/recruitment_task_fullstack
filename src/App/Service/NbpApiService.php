<?php
namespace App\Service;

use GuzzleHttp\Client;

class NbpApiService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://api.nbp.pl/api/']);
    }

    public function getLatestMidRate(string $currency): ?float
    {
        $res = $this->client->get("exchangerates/rates/A/{$currency}/?format=json");
        $data = json_decode($res->getBody()->getContents(), true);
        return $data['rates'][0]['mid'] ?? null;
    }
    public function getAllMidRates(): array
    {
        $res = $this->client->get("exchangerates/tables/A/?format=json");
        $data = json_decode($res->getBody()->getContents(), true);

        $wanted = ['EUR', 'USD', 'CZK', 'IDR', 'BRL'];
        $rates = [];

        foreach ($data[0]['rates'] as $rate) {
            if (in_array($rate['code'], $wanted)) {
                $rates[$rate['code']] = $rate['mid'];
            }
        }

        return $rates;
    }

    public function getHistoricalRates(string $currency, string $endDate, int $days = 14): array
    {
        $startDate = (new \DateTime($endDate))->modify('-30 days')->format('Y-m-d');

        $res = $this->client->get("exchangerates/rates/A/{$currency}/{$startDate}/{$endDate}/?format=json");
        $data = json_decode($res->getBody()->getContents(), true);

        $rates = array_reverse($data['rates'] ?? []);
        $rates = array_slice($rates, 0, $days);

        return array_reverse(array_map(function ($rate) use ($currency) {
            return [
                'date' => $rate['effectiveDate'],
                'mid' => $rate['mid'],
                'currency' => $currency,
            ];
        }, $rates));
    }



}
