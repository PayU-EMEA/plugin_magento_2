<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayURefundOrderInterface;

/**
 * Class RefundOrder
 * @package PayU\PaymentGateway\Model
 */
class RefundOrder implements PayURefundOrderInterface
{
    /**
     * @var \OpenPayU_Refund
     */
    private $openPayURefund;

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * RefundOrder constructor.
     *
     * @param \OpenPayU_Refund $openPayURefund
     * @param PayUConfigInterface $payUConfig
     */
    public function __construct(
        \OpenPayU_Refund $openPayURefund,
        PayUConfigInterface $payUConfig
    ) {
        $this->openPayURefund = $openPayURefund;
        $this->payUConfig = $payUConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($orderId, $type, $description = '', $amount = null)
    {
        $this->payUConfig->setDefaultConfig($type);
        $payURefund = $this->openPayURefund;
        $response = $payURefund::create($orderId, $description, $amount);

        return ['status' => $response->getStatus()];
    }
}
