<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUCreateOrderInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUCreateOrderInterface
{
    /**
     * Redirect Url key
     */
    const REDIRECT_URI_FIELD = 'redirectUri';

    /**
     * Warning continue cvv code
     */
    const WARNING_CONTINUE_CVV = 'WARNING_CONTINUE_CVV';

    /**
     * Warning continue 3DS code
     */
    const WARNING_CONTINUE_3_DS = 'WARNING_CONTINUE_3DS';

    /**
     * Create order in by PayU REST API
     *
     * @param string $type
     * @param array $data
     *
     * @return array
     * @throws \OpenPayU_Exception
     */
    public function execute($type, array $data = []);
}
