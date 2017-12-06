<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUGetUserPayMethodsInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUGetUserPayMethodsInterface
{
    /**
     * Card Tokens code
     */
    const CARD_TOKENS = 'cardTokens';

    /**
     * Pex Tokens code
     */
    const PEX_TOKENS = 'pexTokens';

    /**
     * Get user payment methods from PayU REST API
     *
     * @param string|null $email
     * @param string|null $customerId
     *
     * @return array
     * @throws \OpenPayU_Exception
     */
    public function execute($email = null, $customerId = null);

    /**
     * Convert execute method to Json
     *
     * @return string
     */
    public function toJson();
}
