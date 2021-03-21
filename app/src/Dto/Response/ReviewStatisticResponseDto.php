<?php

namespace App\Dto\Response;

use JMS\Serializer\Annotation as Serialization;

/**
 * Class ReviewStatisticResponseDto
 * @package App\Dto\Response
 */
class ReviewStatisticResponseDto
{

    /**
     * @Serialization\Type("int")
     */
    public $review_count;

    /**
     * @Serialization\Type("float")
     */
    public $average_score;

    /**
     * @Serialization\Type("string")
     */
    public $date_group;


}