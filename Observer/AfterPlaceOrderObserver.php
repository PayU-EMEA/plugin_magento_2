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
     * Status pending_payment
     */
    const STATUS_PENDING_PAYMENT = 'pending_payment';

    /**
     * Allowed statuses after place order
     */
    const ALLOWED_STATUSES = array(self::STATUS_PENDING, self::STATUS_PENDING_PAYMENT);

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

        $status = in_array($this->payUConfig->getStatusAfterOrderPlace($method), static::ALLOWED_STATUSES)
            ? $this->payUConfig->getStatusAfterOrderPlace($method) : static::STATUS_PENDING;
        $this->assignStatus($payment, $status);
        if ($this->payUConfig->isRepaymentActive($method)) {
            $this->emailProcessor->process($payment);
        }
    }

    /**
     * @param Payment $payment
     * @param string $status
     *
     * @return void
     */
    private function assignStatus(Payment $payment, $status)
    {
        $order = $payment->getOrder();
        $order->setState($status)->setStatus($status);
        $this->orderRepository->save($order);
    }
}
