<?php
namespace PayU\PaymentGateway\Block\Sales\Order;

use Magento\Sales\Block\Order\Totals as SalesOrderTotals;

/**
 * Class Totals
 * @package PayU\PaymentGateway\Block\Sales\Order
 */
class Totals extends SalesOrderTotals
{
    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $beforeToHtml = parent::_beforeToHtml();
        unset($this->_totals['base_grandtotal']);

        return $beforeToHtml;
    }
}
