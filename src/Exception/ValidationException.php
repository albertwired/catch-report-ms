<?php

namespace App\Exception;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ValidationException
 */
class ValidationException extends \RuntimeException
{
    /** @var ValidatorInterface */
    private $validator;

    /** @var ConstraintViolationList|null */
    private $violations;

    /** @var \Symfony\Component\PropertyAccess\PropertyAccessor */
    private $propertyAccessor;

    public function __construct(ValidatorInterface $validator, ConstraintViolationList $violations = null)
    {
        $message = 'The given data failed to pass validation.';

        parent::__construct($message);

        $this->validator = $validator;
        $this->violations = $violations;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function getValidator() : ValidatorInterface
    {
        return $this->validator;
    }

    public function getResponseData() : array
    {
        $errors = [];

        if ($this->violations instanceof ConstraintViolationList) {
            $iterator = $this->violations->getIterator();

            /** @var ConstraintViolation $violation */
            foreach ($iterator as $key => $violation) {
                $entryErrors = (array) $this->propertyAccessor->getValue($errors, $violation->getPropertyPath());
                $entryErrors[] = $violation->getMessage();

                $this->propertyAccessor->setValue($errors, $violation->getPropertyPath(), $entryErrors);
            }
        }

        return $errors;
    }
}
