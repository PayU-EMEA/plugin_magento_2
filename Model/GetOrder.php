<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\PayUGetOrderInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection as TransactionCollection;
use Psr\Log\LoggerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class GetOrder
 * @package PayU\PaymentGateway\Model
 */
class GetOrder implements PayUGetOrderInterface
{
    /**
     * @var \OpenPayU_Order
     */
    private $openPayUOrder;

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * @var TransactionCollection
     */
    private $transactionCollection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * GetPayMethods constructor.
     *
     * @param \OpenPayU_Order $openPayUOrder
     * @param PayUConfigInterface $payUConfig
     * @param TransactionCollection $transactionCollection
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        \OpenPayU_Order $openPayUOrder,
        PayUConfigInterface $payUConfig,
        TransactionCollection $transactionCollection,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger
    ) {
        $this->openPayUOrder = $openPayUOrder;
        $this->payUConfig = $payUConfig;
        $this->transactionCollection = $transactionCollection;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($orderId, $type)
    {
        if (empty($orderId)) {
            return [];
        }

        $transactionData = null;
        $storeId = null;

        try {
            $transactionData = $this->transactionCollection->addFieldToFilter('txn_id', $orderId)->getFirstItem();
        } catch (NoSuchEntityException $exception) {
            $this->logger->critical($exception->getMessage());
        }

        if ($transactionData) {
            $realOrderId = $transactionData->getOrderId();
            $order = $this->orderRepository->get($realOrderId);
            $storeId = $order->getStoreId();
        }
        $this->payUConfig->setDefaultConfig($type, $storeId);
        $payUOrder = $this->openPayUOrder;
        $response = $payUOrder::retrieve($orderId)->getResponse();

        if (isset($response->orders) && isset($response->orders[0])) {
            return get_object_vars($response->orders[0]);
        }

        return [];
    }
}
