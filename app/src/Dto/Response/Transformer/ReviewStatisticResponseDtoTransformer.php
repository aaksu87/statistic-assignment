<?php

declare(strict_types=1);

namespace App\Dto\Response\Transformer;

use App\Dto\Exception\UnexpectedTypeException;
use App\Dto\Response\ReviewStatisticResponseDto;

class ReviewStatisticResponseDtoTransformer extends AbstractResponseDtoTransformer
{
    /**
     * @param array $statisticData
     * @return ReviewStatisticResponseDto
     */
    public function transformFromObject(array $statisticData): ReviewStatisticResponseDto
    {
        if (!is_array($statisticData)) {
            throw new UnexpectedTypeException('Expected type of data but got ' . \get_class($statisticData));
        }

        $dto = new ReviewStatisticResponseDto();
        $dto->date_group = $statisticData['date_group'];
        $dto->review_count = $statisticData['review_count'];
        $dto->average_score = $statisticData['average_score'];

        return $dto;
    }
}
