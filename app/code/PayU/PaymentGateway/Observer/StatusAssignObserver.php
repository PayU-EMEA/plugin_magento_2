<?php

namespace PayU\PaymentGateway\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class DataAssignObserver
 * @package PayU\PaymentGateway\Observer
 */
class StatusAssignObserver implements ObserverInterface
{
    /**
     * Status pending
     */
    const STATUS_PENDING = 'pending';

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * StatusAssignObserver constructor.
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $observer->getData('payment');
        $order = $payment->getOrder();
        $order->setState(static::STATUS_PENDING)->setStatus(static::STATUS_PENDING);
        $this->orderRepository->save($order);
    }
}
