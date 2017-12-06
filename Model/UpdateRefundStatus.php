<?php

namespace PayU\PaymentGateway\Model;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Framework\DB\Transaction;
use PayU\PaymentGateway\Api\PayUUpdateRefundStatusInterface;
use PayU\PaymentGateway\Api\OrderByExtOrderIdResolverInterface;

/**
 * Class UpdateRefundStatus
 * @package PayU\PaymentGateway\Model
 */
class UpdateRefundStatus implements PayUUpdateRefundStatusInterface
{
    /**
     * @var OrderByExtOrderIdResolverInterface
     */
    private $extOrderIdResolver;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * UpdateRefundStatus constructor.
     *
     * @param OrderByExtOrderIdResolverInterface $extOrderIdResolver
     * @param Transaction $transaction
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderByExtOrderIdResolverInterface $extOrderIdResolver,
        Transaction $transaction,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->extOrderIdResolver = $extOrderIdResolver;
        $this->transaction = $transaction;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function cancel($extOrderId)
    {
        $orderByIncrementId = $this->extOrderIdResolver->resolve($extOrderId);
        $orderByIncrementId->addStatusHistoryComment(__('Refund was canceled.'));
        /** @var Creditmemo $creditMemo */
        $creditMemo = $orderByIncrementId->getCreditmemosCollection()->getLastItem();
        $creditMemo->setState(Creditmemo::STATE_CANCELED)->setCreditmemoStatus(Creditmemo::STATE_CANCELED);
        $this->transaction->addObject($creditMemo);
        $this->transaction->addObject($orderByIncrementId);
        $this->transaction->save();
    }

    /**
     * {@inheritdoc}
     */
    public function addSuccessMessage($extOrderId)
    {
        $orderByIncrementId = $this->extOrderIdResolver->resolve($extOrderId);
        $orderByIncrementId->addStatusHistoryComment(__('Refund was finalized.'));
        if ($orderByIncrementId->getTotalRefunded() === $orderByIncrementId->getTotalPaid()) {
            $orderByIncrementId->setState(Order::STATE_CLOSED)->setStatus(Order::STATE_CLOSED);
        }
        $this->orderRepository->save($orderByIncrementId);
    }
}
