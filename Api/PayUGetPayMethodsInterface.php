<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUGetPayMethodsInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUGetPayMethodsInterface
{
    /**
     * Credit card code
     */
    const CREDIT_CARD_CODE = 'c';

    /**
     * Test Payment code
     */
    const TEST_PAYMENT_CODE = 't';

    /**
     * Paymethod status ENABLED
     */
    const PAYMETHOD_STATUS_ENABLED = 'ENABLED';

    /**
     * Get all pay methods for selected POS from PayU REST API
     *
     * @return array
     * @throws \OpenPayU_Exception
     */
    public function execute();

    /**
     * Convert execute method to Json
     *
     * @return string
     */
    public function toJson();
}
