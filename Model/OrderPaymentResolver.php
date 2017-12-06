<?php

namespace PayU\PaymentGateway\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use PayU\PaymentGateway\Api\OrderPaymentResolverInterface;

/**
 * Class OrderPaymentResolver
 * @package PayU\PaymentGateway\Api
 */
class OrderPaymentResolver implements OrderPaymentResolverInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var OrderPaymentRepositoryInterface
     */
    private $paymentRepository;

    /**
     * OrderPaymentResolver constructor.
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TransactionRepositoryInterface $transactionRepository
     * @param OrderPaymentRepositoryInterface $paymentRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TransactionRepositoryInterface $transactionRepository,
        OrderPaymentRepositoryInterface $paymentRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->transactionRepository = $transactionRepository;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getLast($order)
    {
        /** @var Payment $payment */
        $payment = $order->getPaymentsCollection()->getLastItem();
        $order->setData(
            OrderInterface::PAYMENT,
            $payment
        );
        $payment->setOrder($order);

        return $payment;
    }

    /**
     * {@inheritdoc}
     */
    public function getByTransactionTxnId($txnId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('txn_id', $txnId, 'eq')->create();
        $transactionList = $this->transactionRepository->getList($searchCriteria)->getItems();
        /** @var \Magento\Sales\Api\Data\TransactionInterface $transaction */
        $transaction = current($transactionList);
        /** @var Payment $payment */
        $payment = $this->paymentRepository->get($transaction->getPaymentId());
        $payment->setData('is_active', !$transaction->getIsClosed());
        $payment->getOrder()->setData(OrderInterface::PAYMENT, $payment);

        return $payment;
    }
}
