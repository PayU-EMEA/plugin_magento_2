<?php

namespace PayU\PaymentGateway\Block\Order\Grid;

use PayU\PaymentGateway\Api\RepaymentResolverInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Action
 * @package PayU\PaymentGateway\Block\Order\Grid
 */
class Action extends Template
{
    /**
     * Order ID
     */
    const ORDER_ID = 'order_id';

    /**
     * @var RepaymentResolverInterface
     */
    private $repaymentResolver;

    /**
     * Action constructor.
     *
     * @param Context $context
     * @param RepaymentResolverInterface $repaymentResolver
     * @param array $data
     */
    public function __construct(Context $context, RepaymentResolverInterface $repaymentResolver, array $data = [])
    {
        $this->repaymentResolver = $repaymentResolver;
        parent::__construct($context, $data);
    }

    /**
     * Is order can repay
     *
     * @param int $orderId
     *
     * @return bool
     */
    public function isOrderCanRepay($orderId)
    {
        return $this->repaymentResolver->isRepayment($orderId);
    }

    /**
     * Return url for repay
     *
     * @param Order $order
     *
     * @return string
     */
    public function getOrderRepayUrl(Order $order)
    {
        return $this->getUrl('sales/order/repayview', [static::ORDER_ID => $order->getId()]);
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function getViewUrl(Order $order)
    {
        return $this->getUrl('sales/order/view', [static::ORDER_ID => $order->getId()]);
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function getReorderUrl(Order $order)
    {
        return $this->getUrl('sales/order/reorder', [static::ORDER_ID => $order->getId()]);
    }
}
