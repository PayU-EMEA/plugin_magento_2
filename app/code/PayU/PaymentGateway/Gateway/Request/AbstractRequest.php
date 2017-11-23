<?php

namespace PayU\PaymentGateway\Gateway\Request;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;

/**
 * Class AbstractRequest
 * @package PayU\PaymentGateway\Gateway\Request
 */
abstract class AbstractRequest
{
    /**
     * Payment key in subject
     */
    const BUILD_SUBJECT_PAYMENT = 'payment';

    /**
     * @var Payment
     */
    protected $payment;

    /**
     * @var OrderAdapterInterface
     */
    protected $order;

    /**
     * @var PaymentDataObjectInterface
     */
    protected $buildPayment;

    /**
     * Build payment data
     *
     * @param array $buildSubject
     *
     * @return void
     * @throws \LogicException
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject[static::BUILD_SUBJECT_PAYMENT]) ||
            !$buildSubject[static::BUILD_SUBJECT_PAYMENT] instanceof PaymentDataObjectInterface) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }
        $this->buildPayment = $buildSubject[static::BUILD_SUBJECT_PAYMENT];
        $this->payment = $this->buildPayment->getPayment();
        $this->order = $this->buildPayment->getOrder();
        if (!$this->payment instanceof OrderPaymentInterface) {
            throw new \LogicException('Order payment should be provided.');
        }
    }
}
