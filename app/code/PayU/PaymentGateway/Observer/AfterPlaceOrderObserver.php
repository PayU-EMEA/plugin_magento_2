<?php

namespace PayU\PaymentGateway\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;
use PayU\PaymentGateway\Model\Ui\ConfigProvider;
use Magento\Sales\Model\Order\Payment;

/**
 * Class AfterPlaceOrderObserver
 * @package PayU\PaymentGateway\Observer
 */
class AfterPlaceOrderObserver implements ObserverInterface
{
    /**
     * Status pending
     */
    const STATUS_PENDING = 'pending';

    /**
     * Store key
     */
    const STORE = 'store';

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var AfterPlaceOrderRepayEmailProcessor
     */
    private $emailProcessor;

    /**
     * StatusAssignObserver constructor.
     *
     * @param PayUConfigInterface $payUConfig
     * @param OrderRepositoryInterface $orderRepository
     * @param AfterPlaceOrderRepayEmailProcessor $emailProcessor
     */
    public function __construct(
        PayUConfigInterface $payUConfig,
        OrderRepositoryInterface $orderRepository,
        AfterPlaceOrderRepayEmailProcessor $emailProcessor
    ) {
        $this->payUConfig = $payUConfig;
        $this->orderRepository = $orderRepository;
        $this->emailProcessor = $emailProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var Payment $payment */
        $payment = $observer->getData('payment');
        $method = $payment->getMethod();
        if ($method !== CardConfigProvider::CODE && $method !== ConfigProvider::CODE) {
            return;
        }
        $this->assignStatus($payment);
        if ($this->payUConfig->isRepaymentActive($method)) {
            $this->emailProcessor->process($payment);
        }
    }

    /**
     * @param Payment $payment
     *
     * @return void
     */
    private function assignStatus(Payment $payment)
    {
        $order = $payment->getOrder();
        $order->setState(static::STATUS_PENDING)->setStatus(static::STATUS_PENDING);
        $this->orderRepository->save($order);
    }
}
