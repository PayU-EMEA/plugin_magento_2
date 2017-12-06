<?php

namespace PayU\PaymentGateway\Api;

use Magento\Framework\Exception\LocalizedException;

/**
 * Interface PayUDeleteUserTokenInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUDeleteUserTokenInterface
{
    /**
     * Delete stored user token for given token
     *
     * @param string $userToken
     *
     * @return string
     * @throws LocalizedException
     */
    public function execute($userToken);
}
