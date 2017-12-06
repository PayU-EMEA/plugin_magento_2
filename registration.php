<?php
require_once('lib/openpayu.php');

/**
 * Register PayU_PaymentGateway Component
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'PayU_PaymentGateway',
    __DIR__
);
