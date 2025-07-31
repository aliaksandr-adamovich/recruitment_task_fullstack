<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExchangeRateControllerTest extends WebTestCase
{
    public function testTodayEndpoint()
    {
        $client = static::createClient();
        $client->request('GET', '/api/rates/today');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
    }

    public function testHistoryEndpointValidDate()
    {
        $client = static::createClient();
        $validDate = (new \DateTime('-1 week'))->format('Y-m-d');
        $client->request('GET', "/api/rates/history/EUR/$validDate");

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
    }

    public function testHistoryEndpointFutureDate()
    {
        $client = static::createClient();
        $futureDate = (new \DateTime('+1 day'))->format('Y-m-d');
        $client->request('GET', "/api/rates/history/EUR/$futureDate");

        $this->assertResponseStatusCodeSame(400);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testHistoryEndpointUnknownCurrency()
    {
        $client = static::createClient();
        $validDate = (new \DateTime('-1 week'))->format('Y-m-d');
        $client->request('GET', "/api/rates/history/XYZ/$validDate");

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testHistoryEndpointNoData()
    {
        $client = static::createClient();
        $oldDate = '1900-01-01';
        $client->request('GET', "/api/rates/history/EUR/$oldDate");

        $this->assertTrue(
            $client->getResponse()->getStatusCode() === 404 ||
            $client->getResponse()->getStatusCode() === 200
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        if ($client->getResponse()->getStatusCode() === 200) {
            $this->assertEmpty($data);
        }
    }

    public function testHistoryEndpointInvalidDateFormat()
    {
        $client = static::createClient();
        $client->request('GET', "/api/rates/history/EUR/invalid-date");

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testTodayEndpointPostMethodNotAllowed()
    {
        $client = static::createClient();
        $client->request('POST', '/api/rates/today');

        $this->assertResponseStatusCodeSame(405); // Method Not Allowed
    }


}
