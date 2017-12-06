<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUGetOrderInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUGetOrderInterface
{
    const ORDER_KEY = 'order';
    const PAY_METHOD = 'payMethod';
    const PAY_METHOD_TYPE = 'type';
    const ORDER_ID = 'orderId';

    /**
     * Retrive order for payU REST API
     *
     * @param int $orderId
     * @param string $type
     *
     * @return array
     * @throws \OpenPayU_Exception_Network
     */
    public function execute($orderId, $type);
}
