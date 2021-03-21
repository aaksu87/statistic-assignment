<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTest extends WebTestCase
{

    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    public function testInvalidHotelIdReturnsException(): void
    {
        $this->client->request(
            'POST',
            '/review-statistic',
            [
                "hotel_id" => "abc",
                "start_date" => "2020-02-01",
                "end_date" => "2020-03-01"
            ]
        );

        $response = $this->client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
    }


    public function testNonExistHotelIdReturnsException(): void
    {
        $this->client->request(
            'POST',
            '/review-statistic',
            [
                "hotel_id" => 999,
                "start_date" => "2020-02-01",
                "end_date" => "2020-03-01"
            ]
        );

        $response = $this->client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function testInvalidDatesReturnsException(): void
    {
        $this->client->request(
            'POST',
            '/review-statistic',
            [
                "hotel_id" => 5,
                "start_date" => "01-02-2020",
                "end_date" => "2020-03-01"
            ]
        );

        $response = $this->client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function testDailySuccessResult(): void
    {
        $this->client->request(
            'POST',
            '/review-statistic',
            [
                "hotel_id" => 5,
                "start_date" => "2020-02-01",
                "end_date" => "2020-02-15"
            ]
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('review_count', $responseData[0]);
        $this->assertArrayHasKey('average_score', $responseData[0]);
        $this->assertArrayHasKey('date_group', $responseData[0]);

        $dailyFormatDate = $responseData[0]['date_group'];
        $this->assertEquals($dailyFormatDate, date("Y-m-d", strtotime($dailyFormatDate)));
    }

    public function testWeeklySuccessResult(): void
    {
        $this->client->request(
            'POST',
            '/review-statistic',
            [
                "hotel_id" => 3,
                "start_date" => "2020-01-01",
                "end_date" => "2020-02-01"
            ]
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('review_count', $responseData[0]);
        $this->assertArrayHasKey('average_score', $responseData[0]);
        $this->assertArrayHasKey('date_group', $responseData[0]);

        $weeklyFormatDate = $responseData[0]['date_group'];
        $this->assertEquals($weeklyFormatDate, date("Y-W", strtotime($weeklyFormatDate)));

    }

    public function testMonthlySuccessResult(): void
    {
        $this->client->request(
            'POST',
            '/review-statistic',
            [
                "hotel_id" => 5,
                "start_date" => "2020-02-01",
                "end_date" => "2020-06-01"
            ]
        );

        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('review_count', $responseData[0]);
        $this->assertArrayHasKey('average_score', $responseData[0]);
        $this->assertArrayHasKey('date_group', $responseData[0]);

        $monthlyFormatDate = $responseData[0]['date_group'];
        $this->assertEquals($monthlyFormatDate, date("Y-m", strtotime($monthlyFormatDate)));
    }


}
