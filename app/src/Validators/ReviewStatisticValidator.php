<?php

namespace App\Validators;

use App\Exception\ValidationException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;

class ReviewStatisticValidator
{
    private $validator;
    private $constraints;

    public function __construct()
    {
        $this->validator = Validation::createValidator();
        $this->setConstraints();
    }

    public function setConstraints()
    {
        $this->constraints = new Collection([
            'hotel_id' => [
                new NotBlank(),
                new Type(['type' => 'numeric','message' => 'The value {{ value }} is not a valid {{ type }}.'])
            ],
            'start_date' => [
                new NotBlank(),
                new Date(),
            ],
            'end_date' => [
                new NotBlank(),
                new Date(),
            ]
        ]);
    }

    public function validate($value = null)
    {
        $result = $this->validator->validate($value, $this->constraints);
        foreach ($result as $fieldName => $violation) {
            throw new ValidationException($violation->getPropertyPath()." ".$violation->getMessage());
        }
    }
}
