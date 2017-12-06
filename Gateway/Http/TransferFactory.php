<?php

namespace PayU\PaymentGateway\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;

/**
 * Class TransferFactory
 * @package PayU\PaymentGateway\Gateway\Http
 */
class TransferFactory implements TransferFactoryInterface
{
    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * @param TransferBuilder $transferBuilder
     */
    public function __construct(TransferBuilder $transferBuilder)
    {
        $this->transferBuilder = $transferBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $request)
    {
        return $this->transferBuilder->setBody($request)->build();
    }
}
