<?php
require_once(BP . '/lib/internal/OpenPayU/openpayu.php');

/**
 * Register PayU_PaymentGateway Component
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'PayU_PaymentGateway',
    __DIR__
);
