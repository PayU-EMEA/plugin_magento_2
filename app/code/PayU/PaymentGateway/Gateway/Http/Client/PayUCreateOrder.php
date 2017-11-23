<?php

namespace PayU\PaymentGateway\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUCreateOrderInterfaceFactory;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;
use PayU\PaymentGateway\Model\Ui\ConfigProvider;
use PayU\PaymentGateway\Model\Logger\Logger;

/**
 * Class PayUCreateOrder
 * @package PayU\PaymentGateway\Gateway\Http\Client
 */
class PayUCreateOrder implements ClientInterface
{
    /**
     * @var PayUCreateOrderInterfaceFactory
     */
    private $createOrderFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param PayUCreateOrderInterfaceFactory $createOrderFactory
     * @param Logger $logger
     */
    public function __construct(PayUCreateOrderInterfaceFactory $createOrderFactory, Logger $logger)
    {
        $this->createOrderFactory = $createOrderFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        try {
            return $this->createApiOrder($transferObject);
        } catch (\OpenPayU_Exception $exception) {
            $this->logger->critical($exception->getMessage());

            return [];
        }
    }

    /**
     * Create order in PayU REST API
     *
     * @param \Magento\Payment\Gateway\Http\TransferInterface $transferObject
     *
     * @return array
     */
    protected function createApiOrder(TransferInterface $transferObject)
    {
        $parameters = $transferObject->getBody();
        unset($parameters['txn_type'], $parameters[PayUConfigInterface::PAYU_METHOD_CODE], $parameters[PayUConfigInterface::PAYU_METHOD_TYPE_CODE]);
        $type = ConfigProvider::CODE;
        if ($parameters['payMethods']['payMethod']['type'] === PayUConfigInterface::PAYU_CC_TRANSFER_KEY) {
            $type = CardConfigProvider::CODE;
        }

        return $this->createOrderFactory->create()->execute($type, $parameters);
    }
}
