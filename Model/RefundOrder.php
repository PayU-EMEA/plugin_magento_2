<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayURefundOrderInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection as TransactionCollection;
use Psr\Log\LoggerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class RefundOrder
 * @package PayU\PaymentGateway\Model
 */
class RefundOrder implements PayURefundOrderInterface
{
    /**
     * @var \OpenPayU_Refund
     */
    private $openPayURefund;

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
     * RefundOrder constructor.
     *
     * @param \OpenPayU_Refund $openPayURefund
     * @param PayUConfigInterface $payUConfig
     * @param TransactionCollection $transactionCollection
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        \OpenPayU_Refund $openPayURefund,
        PayUConfigInterface $payUConfig,
        TransactionCollection $transactionCollection,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger
    ) {
        $this->openPayURefund = $openPayURefund;
        $this->payUConfig = $payUConfig;
        $this->transactionCollection = $transactionCollection;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($orderId, $type, $description = '', $amount = null)
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
        $payURefund = $this->openPayURefund;
        $response = $payURefund::create($orderId, $description, $amount);

        return ['status' => $response->getStatus()];
    }
}
