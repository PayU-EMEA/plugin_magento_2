<?php

namespace PayU\PaymentGateway\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;

/**
 * Class AbstractValidator
 * @package PayU\PaymentGateway\Gateway\Validator
 */
abstract class AbstractResponseValidator extends AbstractValidator
{
    /**
     * Validation response subject code
     */
    const VALIDATION_SUBJECT_RESPONSE = 'response';

    /**
     * Validation status subject code
     */
    const VALIDATION_SUBJECT_STATUS = 'status';

    /**
     * @var array
     */
    protected $response = [];

    /**
     * {@inheritdoc}
     */
    public function validate(array $validationSubject)
    {
        if (!isset($validationSubject[static::VALIDATION_SUBJECT_RESPONSE]) ||
            !is_array($validationSubject[static::VALIDATION_SUBJECT_RESPONSE])) {
            throw new \InvalidArgumentException('Response does not exist');
        }

        $this->response = $validationSubject[static::VALIDATION_SUBJECT_RESPONSE];
        if ($this->isSuccessfulTransaction()) {
            return $this->createResult(true, []);
        }

        return $this->createResult(false, [__('Gateway rejected the transaction.')]);
    }

    /**
     * @return bool
     */
    protected function isSuccessfulTransaction()
    {
        return false;
    }
}
