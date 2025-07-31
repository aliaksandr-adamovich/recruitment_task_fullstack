<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\NbpApiService;
use App\Service\ExchangeRateCalculator;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/rates')]
class ExchangeRateController
{
    private NbpApiService $nbp;
    private ExchangeRateCalculator $calc;
    private array $allowedCurrencies = ['EUR', 'USD', 'CZK', 'IDR', 'BRL'];

    public function __construct(NbpApiService $nbp, ExchangeRateCalculator $calc)
    {
        $this->nbp = $nbp;
        $this->calc = $calc;
    }

    #[Route('/today', name: 'rates_today', methods: ['GET'])]
    public function today(): JsonResponse
    {
        $currencies = ['EUR', 'USD', 'CZK', 'IDR', 'BRL'];
        $data = [];

        foreach ($currencies as $currency) {
            $mid = $this->nbp->getLatestMidRate($currency);
            if ($mid !== null) {
                $data[] = $this->calc->calculateRate($currency, $mid, (new \DateTime())->format('Y-m-d'));
            }
        }

        return new JsonResponse($data);
    }

    #[Route('/today', name: 'rates_today_post', methods: ['POST'])]
    public function todayPost(): JsonResponse
    {
        return new JsonResponse(['error' => 'Method POST not allowed'], 405);
    }

    #[Route('/history/{currency}/{date}', name: 'rates_history', methods: ['GET'])]
    public function history(string $currency, string $date): JsonResponse
    {
        $currency = strtoupper($currency);
        if (!in_array($currency, ['EUR', 'USD', 'CZK', 'IDR', 'BRL'])) {
            return new JsonResponse(['error' => 'Unsupported currency'], 400);
        }

        try {
            $requestedDate = new \DateTime($date);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format'], 400);
        }

        $today = new \DateTime();
        if ($requestedDate > $today) {
            return new JsonResponse(['error' => 'Date cannot be in the future'], 400);
        }

        try {
            $rates = $this->nbp->getHistoricalRates($currency, $date);
        } catch (ClientException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 404) {
                return new JsonResponse(['error' => 'No data for given date'], 404);
            }
            return new JsonResponse(['error' => 'External API error'], 500);
        }

        if (empty($rates)) {
            return new JsonResponse(['error' => 'No data for given date'], 404);
        }

        $data = [];
        foreach ($rates as $rate) {
            $data[] = $this->calc->calculateRate($currency, $rate['mid'], $rate['date']);
        }

        return new JsonResponse($data);
    }

}
