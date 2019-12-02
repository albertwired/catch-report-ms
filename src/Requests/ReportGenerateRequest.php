<?php

namespace App\Requests;

use Symfony\Component\Validator\Constraints\{
    Collection, Required, Email, Optional, Existence
};
use Symfony\Component\Validator\Exception\{
    ConstraintDefinitionException,
    InvalidOptionsException,
    MissingOptionsException
};

class ReportGenerateRequest extends BaseValidation
{
    private const ALLOW_EXTRA_FIELDS = true;
    private const ALLOW_MISSING_FIELDS = false;
    private const EXTRA_FIELDS_MESSAGE = 'This field was not expected.';
    private const MISSING_FIELDS_MESSAGE = 'This field is missing.';

    /**
     * @return Collection
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws ConstraintDefinitionException
     */
    public function rules() : Collection
    {
        return new Collection([
            'fields'                => $this->getFields(),
            'allowExtraFields'      => self::ALLOW_EXTRA_FIELDS,
            'allowMissingFields'    => self::ALLOW_MISSING_FIELDS,
            'extraFieldsMessage'    => self::EXTRA_FIELDS_MESSAGE,
            'missingFieldsMessage'  => self::MISSING_FIELDS_MESSAGE,
        ]);
    }

    /**
     * @return array
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws ConstraintDefinitionException
     */
    private function getFields() : array
    {
        return [
            'result-type'   => new Required(),
            'email-to' => new Optional([
                new Email()
            ])
        ];
    }
}