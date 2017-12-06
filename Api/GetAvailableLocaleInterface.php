<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUGetCreditCardWidgetConfig
 * @package PayU\PaymentGateway\Api
 */
interface GetAvailableLocaleInterface
{
    /**
     * Get current language Provide option to get only language from array parametes
     *
     * @param array $availableLanguages
     *
     * @return string
     */
    public function execute(array $availableLanguages = []);
}
