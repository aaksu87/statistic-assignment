<?php

namespace App\Service;

use App\Exception\NoHotelException;
use App\Repository\HotelRepository;
use App\Repository\ReviewRepository;

class StatisticService
{
    private HotelRepository $hotelRepository;
    private ReviewRepository $reviewRepository;

    private int $hotelId;
    private string $startDate;
    private string $endDate;

    public function __construct(HotelRepository $hotelRepository, ReviewRepository $reviewRepository)
    {
        $this->hotelRepository = $hotelRepository;
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * @param mixed $hotelId
     */
    public function setHotelId(int $hotelId): void
    {
        $this->hotelId = $hotelId;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate(string $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate(string $endDate): void
    {
        $this->endDate = $endDate;
    }


    /**
     * @return array
     * @throws NoHotelException
     */
    public function reviewStatistic(): array
    {
        //return [$this->hotelId];
        $hotel = $this->hotelRepository->find($this->hotelId);

        if (!$hotel) {
            throw new NoHotelException('Can not find any hotel with this id');
        }

        $this->setRangeType();

        $dateGroupedData = $this->reviewRepository->getDateGroupedStatistic($this->hotelId, $this->startDate, $this->endDate);

        return $this->calculateOvertime($dateGroupedData);
    }

    /**
     * Calculate the range between dates and set the group type in repository
     * @throws \Exception
     */
    private function setRangeType(): void
    {
        $from = new \DateTime($this->startDate);
        $to = new \DateTime($this->endDate);

        $diffDays = $to->diff($from)->format("%a");

        if ($diffDays >= 1 && $diffDays < 30) {
            $this->reviewRepository->setResultType('daily');
        } elseif ($diffDays >= 30 && $diffDays < 90) {
            $this->reviewRepository->setResultType('weekly');
        } elseif ($diffDays >= 90) {
            $this->reviewRepository->setResultType('monthly');
        }
    }

    /**
     * Get the start date overtime average and recalculate it with grouped date average data
     * @param array $dateGroupedData
     * @return array
     */
    private function calculateOvertime(array $dateGroupedData): array
    {
        //Get hotel's average score and review count of starting day
        $overTimeData = $this->reviewRepository->getOverTimeAverage($this->hotelId, $this->startDate);

        //Recalculate overtime data with grouped data to the end date
        foreach ($dateGroupedData as $key => $dateGroupAvg) {
            $overTimeSumScore = $overTimeData['overtime_review_count'] * $overTimeData['overtime_average_score'];
            $dateGroupedSumScore = $dateGroupAvg['review_count'] * $dateGroupAvg['average_score'];

            $overTimeData['overtime_review_count'] += $dateGroupAvg['review_count'];

            $dateGroupedData[$key]['average_score'] = round(($dateGroupedSumScore + $overTimeSumScore) / $overTimeData['overtime_review_count'], 3);
        }

        return $dateGroupedData;
    }


}