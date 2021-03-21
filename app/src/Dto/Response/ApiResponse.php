<?php

namespace App\Dto\Response;

use JMS\Serializer\Annotation as Serialization;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiResponse
 * @package App\Dto\Response
 */
class ApiResponse
{

    private $data;
    private $statusCode;

    /**
     * ApiResponse constructor.
     * @param $data
     * @param $statusCode
     */
    public function __construct($data, $statusCode)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
    }

    public function send(){

        if($this->statusCode != Response::HTTP_OK){
            $this->data = json_encode(['error' => $this->data]);
        }

        return new JsonResponse(
            $this->data,
            $this->statusCode,
            [],
            true
        );
    }
}