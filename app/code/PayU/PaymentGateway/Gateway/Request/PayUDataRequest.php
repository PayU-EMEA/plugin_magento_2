<?php

namespace PayU\PaymentGateway\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;

/**
 * Class PayUDataRequest
 * @package PayU\PaymentGateway\Gateway\Request
 */
class PayUDataRequest extends AbstractRequest implements BuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(array $buildSubject)
    {
        parent::build($buildSubject);

        return [
            PayUConfigInterface::PAYU_METHOD_CODE => $this->payment->getAdditionalInformation(
                PayUConfigInterface::PAYU_METHOD_CODE
            ),
            PayUConfigInterface::PAYU_METHOD_TYPE_CODE => $this->payment->getAdditionalInformation(
                PayUConfigInterface::PAYU_METHOD_TYPE_CODE
            )
        ];
    }
}
