<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUUpdateOrderStatusrInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUUpdateOrderStatusInterface
{
    /**
     * Update status of order
     *
     * @param string $type
     * @param string $orderId
     * @param string $status
     *
     * @return \OpenPayU_Result
     * @throws \OpenPayU_Exception
     */
    public function update($type, $orderId, $status);

    /**
     * Cancel order action
     *
     * @param string $type
     * @param string $orderId
     * @param int $loop
     *
     * @return \OpenPayU_Result|null
     * @throws \OpenPayU_Exception
     */
    public function cancel($type, $orderId, $loop = 1);
}
