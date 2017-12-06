<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUUpdateOrderStatusInterface;

/**
 * Class UpdateOrderStatus
 * @package PayU\PaymentGateway\Model
 */
class UpdateOrderStatus implements PayUUpdateOrderStatusInterface
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
    public function update($type, $orderId, $status)
    {
        $this->payUConfig->setDefaultConfig($type);
        $orderStatusUpdate = ['orderId' => $orderId, 'orderStatus' => $status];
        $payUOrder = $this->openPayUOrder;

        return $payUOrder::statusUpdate($orderStatusUpdate);
    }

    /**
     * {@inheritdoc}
     */
    public function cancel($type, $orderId, $loop = 1)
    {
        $result = null;
        $this->payUConfig->setDefaultConfig($type);
        $payUOrder = $this->openPayUOrder;

        /**
         * Case of status WAITING FOR CONFIRMATION deny payment has to been run twice
         */
        for ($cancelLoop = 0; $cancelLoop <= $loop; $cancelLoop++) {
            $result = $payUOrder::cancel($orderId);
        }

        return $result;
    }

}
