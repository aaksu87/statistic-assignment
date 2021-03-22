<?php

namespace App\Controller;

use App\Dto\Response\ApiResponse;
use App\Dto\Response\Transformer\ReviewStatisticResponseDtoTransformer;
use App\Exception\NoHotelException;
use App\Exception\ValidationException;
use App\Service\StatisticService;
use App\Validators\ReviewStatisticValidator;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class StatisticController extends AbstractController
{
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private StatisticService $statisticService;
    private ReviewStatisticResponseDtoTransformer $reviewStatisticResponseDtoTransformer;

    public function __construct(
        LoggerInterface $logger,
        SerializerInterface $serializer,
        StatisticService $statisticService,
        ReviewStatisticResponseDtoTransformer $reviewStatisticResponseDtoTransformer
    )
    {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->statisticService = $statisticService;
        $this->reviewStatisticResponseDtoTransformer = $reviewStatisticResponseDtoTransformer;
    }

    /**
     * @Route("/review-statistic", name="review_statistic")
     * @param Request $request
     * @param ReviewStatisticValidator $validator
     * @return JsonResponse
     */
    public function reviewStatistic(Request $request, ReviewStatisticValidator $validator): JsonResponse
    {
        try {
            $validator->validate($request->request->all());

            $this->statisticService->setHotelId((int)$request->get('hotel_id'));
            $this->statisticService->setStartDate($request->get('start_date'));
            $this->statisticService->setEndDate($request->get('end_date'));


            $statisticData = $this->statisticService->reviewStatistic();

            $dtoData = $this->reviewStatisticResponseDtoTransformer->transformFromObjects($statisticData);

            $serializedData = $this->serializer->serialize($dtoData, 'json');

            return (new ApiResponse($serializedData, Response::HTTP_OK))->send();

        } catch (NoHotelException | ValidationException $e) {
            return (new ApiResponse($e->getMessage(), Response::HTTP_BAD_REQUEST))->send();
        } catch (\Exception $e) {
            $this->logger->error('Statistic Error: ' . $e->getMessage());
            return (new ApiResponse('Something is wrong', Response::HTTP_BAD_REQUEST))->send();
        }
    }
}
