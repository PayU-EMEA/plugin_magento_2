<?php

namespace PayU\PaymentGateway\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class AbstractTxnId
 * @package PayU\PaymentGateway\Gateway\Response
 */
abstract class AbstractTxnId
{
    /**
     * Handling sobject payment code
     */
    const HANDLING_SUBJECT_PAYMENT = 'payment';

    /**
     * @var array
     */
    protected $response = [];

    /**
     * @var Payment
     */
    protected $payment;

    /**
     * Handle response from payment gateway
     *
     * @param array $handlingSubject
     * @param array $response
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject[static::HANDLING_SUBJECT_PAYMENT]) ||
            !$handlingSubject[static::HANDLING_SUBJECT_PAYMENT] instanceof PaymentDataObjectInterface) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }
        $this->response = $response;
        /** @var PaymentDataObjectInterface $payment */
        $handlingPayment = $handlingSubject[static::HANDLING_SUBJECT_PAYMENT];
        $this->payment = $handlingPayment->getPayment();
    }
}
