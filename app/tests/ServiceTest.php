<?php

namespace App\Tests;

use App\Exception\NoHotelException;
use App\Repository\HotelRepository;
use App\Repository\ReviewRepository;
use App\Service\StatisticService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceTest extends WebTestCase
{
    /**
     * @var StatisticService
     */
    private StatisticService $service;
    private HotelRepository $hotelRepositoryMock;
    private ReviewRepository $reviewRepositoryMock;

    protected function setUp(): void
    {
        $this->hotelRepositoryMock = \Mockery::mock('App\Repository\HotelRepository');
        $this->reviewRepositoryMock = \Mockery::mock('App\Repository\ReviewRepository');
        $this->service = new StatisticService($this->hotelRepositoryMock, $this->reviewRepositoryMock);

        parent::setUp();
    }


    public function testNonExistHotelIdReturnsException(): void
    {
        $this->service->setHotelId(12345);
        $this->service->setStartDate('2020-02-01');
        $this->service->setEndDate('2020-03-01');

        $this->hotelRepositoryMock->shouldReceive('find')->once();
        $this->expectException(NoHotelException::class);

        $this->service->reviewStatistic();
    }

    public function testDailySuccessResult(): void
    {
        $this->service->setHotelId(3);
        $this->service->setStartDate('2020-02-01');
        $this->service->setEndDate('2020-02-15');


        $this->hotelRepositoryMock->shouldReceive('find')->once()->andReturnTrue();
        $this->reviewRepositoryMock->shouldReceive('setResultType')->once();
        $this->reviewRepositoryMock->shouldReceive('getDateGroupedStatistic')->once()->andReturn([["review_count"=>1,"average_score"=>2.489,"date_group"=>"2021-01-01"]]);
        $this->reviewRepositoryMock->shouldReceive('getOverTimeAverage')->once()->andReturn(["overtime_review_count"=>1,"overtime_average_score"=>2.489]);

        $responseData = $this->service->reviewStatistic();

        $this->assertArrayHasKey('review_count', $responseData[0]);
        $this->assertArrayHasKey('average_score', $responseData[0]);
        $this->assertArrayHasKey('date_group', $responseData[0]);

        $dailyFormatDate=$responseData[0]['date_group'];
        $this->assertEquals($dailyFormatDate,date("Y-m-d",strtotime($dailyFormatDate)));
    }


}
