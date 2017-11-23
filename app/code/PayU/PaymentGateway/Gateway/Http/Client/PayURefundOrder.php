<?php

namespace PayU\PaymentGateway\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Gateway\Http\ClientException;
use PayU\PaymentGateway\Api\PayURefundOrderInterfaceFactory;
use PayU\PaymentGateway\Model\Logger\Logger;

/**
 * Class PayUCreateOrder
 * @package PayU\PaymentGateway\Gateway\Http\Client
 */
class PayURefundOrder implements ClientInterface
{
    /**
     * @var PayURefundOrderInterfaceFactory
     */
    private $refundOrder;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * PayURefundOrder constructor.
     *
     * @param PayURefundOrderInterfaceFactory $refundOrder
     * @param Logger $logger
     */
    public function __construct(PayURefundOrderInterfaceFactory $refundOrder, Logger $logger)
    {
        $this->refundOrder = $refundOrder;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        try {
            return $this->refundApiOrder($transferObject);
        } catch (\OpenPayU_Exception_Network $exception) {
            $this->logger->critical($exception->getMessage());

            return [];
        } catch (\OpenPayU_Exception $exception) {
            $this->logger->critical($exception->getMessage());
            throw new ClientException(__($exception->getMessage()));
        }
    }

    /**
     * Refund payment in PayU REST API
     *
     * @param \Magento\Payment\Gateway\Http\TransferInterface $transferObject
     *
     * @return array
     */
    private function refundApiOrder(TransferInterface $transferObject)
    {
        $parameters = $transferObject->getBody();

        return $this->refundOrder->create()->execute(
            $parameters['TXN_ID'],
            $parameters['paymentCode'],
            $parameters['description'],
            $parameters['amount']
        );
    }
}
