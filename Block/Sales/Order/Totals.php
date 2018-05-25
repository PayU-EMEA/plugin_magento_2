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
        return parent::_beforeToHtml();

        //TODO: Remove total from e-mail only for MCP
        //$beforeToHtml = parent::_beforeToHtml();
        //unset($this->_totals['base_grandtotal']);

        //return $beforeToHtml;
    }
}
