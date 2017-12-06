<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\PayUGetOrderInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;

/**
 * Class GetOrder
 * @package PayU\PaymentGateway\Model
 */
class GetOrder implements PayUGetOrderInterface
{
    /**
     * @var \OpenPayU_Order
     */
    private $openPayUOrder;

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * GetPayMethods constructor.
     *
     * @param \OpenPayU_Order $openPayUOrder
     * @param PayUConfigInterface $payUConfig
     */
    public function __construct(
        \OpenPayU_Order $openPayUOrder,
        PayUConfigInterface $payUConfig
    ) {
        $this->openPayUOrder = $openPayUOrder;
        $this->payUConfig = $payUConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($orderId, $type)
    {
        $this->payUConfig->setDefaultConfig($type);
        $payUOrder = $this->openPayUOrder;
        $response = $payUOrder::retrieve($orderId)->getResponse();

        if (isset($response->orders) && isset($response->orders[0])) {
            return get_object_vars($response->orders[0]);
        }

        return [];
    }
}
