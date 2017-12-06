<?php

namespace PayU\PaymentGateway\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use PayU\PaymentGateway\Api\PayUGetOrderInterfaceFactory;
use PayU\PaymentGateway\Model\Logger\Logger;

/**
 * Class PayUGetOrder
 * @package PayU\PaymentGateway\Gateway\Http\Client
 */
class PayUGetOrder implements ClientInterface
{
    /**
     * @var PayUGetOrderInterfaceFactory
     */
    private $getOrderFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param PayUGetOrderInterfaceFactory $getOrderFactory
     * @param Logger $logger
     */
    public function __construct(PayUGetOrderInterfaceFactory $getOrderFactory, Logger $logger)
    {
        $this->getOrderFactory = $getOrderFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        try {
            return $this->getApiOrder($transferObject);
        } catch (\OpenPayU_Exception_Network $exception) {
            $this->logger->critical($exception->getMessage());

            return [];
        }
    }

    /**
     * Get order from PayU REST API
     *
     * @param \Magento\Payment\Gateway\Http\TransferInterface $transferObject
     *
     * @return array
     */
    protected function getApiOrder(TransferInterface $transferObject)
    {
        $parameters = $transferObject->getBody();

        return $this->getOrderFactory->create()->execute($parameters['TXN_ID'], $parameters['paymentCode']);
    }
}
