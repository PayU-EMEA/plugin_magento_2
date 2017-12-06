<?php

namespace PayU\PaymentGateway\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class VoidRequest
 * @package PayU\PaymentGateway\Gateway\Request
 */
class VoidRequest extends AbstractRequest implements BuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(array $buildSubject)
    {
        parent::build($buildSubject);

        return [
            'TXN_TYPE' => 'V',
            'TXN_ID' => $this->payment->getLastTransId()
        ];
    }
}
