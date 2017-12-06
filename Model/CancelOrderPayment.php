<?php

namespace PayU\PaymentGateway\Model;

use Magento\Sales\Api\OrderRepositoryInterface;
use PayU\PaymentGateway\Api\CancelOrderPaymentInterface;
use PayU\PaymentGateway\Api\OrderPaymentResolverInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order\Payment\Transaction as TransactionModel;
use Magento\Sales\Api\Data\TransactionInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;

/**
 * Class CancelOrderPayment
 * @package PayU\PaymentGateway\Model
 */
class CancelOrderPayment implements CancelOrderPaymentInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderPaymentResolverInterface
     */
    private $orderPaymentResolver;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * CancelOrderPayment constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderPaymentResolverInterface $orderPaymentResolver
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TransactionRepositoryInterface $transactionRepository
     * @param Transaction $transaction
     * @param PayUConfigInterface $payUConfig
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderPaymentResolverInterface $orderPaymentResolver,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TransactionRepositoryInterface $transactionRepository,
        Transaction $transaction,
        PayUConfigInterface $payUConfig
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderPaymentResolver = $orderPaymentResolver;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->transactionRepository = $transactionRepository;
        $this->transaction = $transaction;
        $this->payUConfig = $payUConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($txnId, $amount)
    {
        $payment = $this->orderPaymentResolver->getByTransactionTxnId($txnId);
        $order = $payment->getOrder();
        if ($order->canCancel() && $amount == $payment->getAmountAuthorized()) {
            $this->closeTransactions($order->getEntityId(), $payment->getEntityId());
            if ($this->getAllActiveTransaction($order->getEntityId()) === 0 &&
                !$this->payUConfig->isRepaymentActive($payment->getMethod())) {
                $order->cancel();
                $this->orderRepository->save($order);
            }
        }
    }

    /**
     * @param string $orderId
     * @param string $paymentId
     *
     * @return void
     */
    private function closeTransactions($orderId, $paymentId)
    {
        $searchCriteria = $this->getSearchCriteriaForOrder($orderId)->addFilter(
            'payment_id',
            $paymentId,
            'eq'
        )->create();

        /** @var TransactionModel[]|TransactionInterface[] $transactions */
        $transactions = $this->transactionRepository->getList($searchCriteria)->getItems();
        foreach ($transactions as $transaction) {
            $transaction->setIsClosed(1);
            $this->transaction->addObject($transaction);
        }
        $this->transaction->save();
    }

    /**
     * @param string $orderId
     *
     * @return int
     */
    private function getAllActiveTransaction($orderId)
    {
        $searchCriteria = $this->getSearchCriteriaForOrder($orderId)->addFilter(
            'is_closed',
            0,
            'eq'
        )->create();

        return count($this->transactionRepository->getList($searchCriteria)->getItems());
    }

    /**
     * Set Search Criteria for Order ID
     *
     * @param string $orderId
     *
     * @return SearchCriteriaBuilder
     */
    private function getSearchCriteriaForOrder($orderId)
    {
        $this->searchCriteriaBuilder->addFilter('order_id', $orderId, 'eq');

        return $this->searchCriteriaBuilder;
    }
}
