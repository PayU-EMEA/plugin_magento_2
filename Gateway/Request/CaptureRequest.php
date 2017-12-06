<?php

namespace PayU\PaymentGateway\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class CaptureRequest
 * @package PayU\PaymentGateway\Gateway\Request
 */
class CaptureRequest extends AbstractRequest implements BuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(array $buildSubject)
    {
        parent::build($buildSubject);

        return [
            'TXN_TYPE' => 'S',
            'TXN_ID' => $this->payment->getLastTransId(),
            'paymentCode' => $this->payment->getMethodInstance()->getCode()
        ];
    }
}
