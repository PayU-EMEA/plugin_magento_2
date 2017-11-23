<?php

namespace PayU\PaymentGateway\Model\Logger;

use Magento\Framework\Logger\Handler\Base;

/**
 * Class Handler
 * @package PayU\PaymentGateway\Model\Logger
 */
class Handler extends Base
{
    /**
     * Logging level
     *
     * @var int
     */
    protected $loggerType = Logger::CRITICAL;

    /**
     * File name
     *
     * @var string
     */
    protected $fileName = '/var/log/payugateway.log';
}
