<?php
namespace PayU\PaymentGateway\Block\Order\Repay;

use Magento\Sales\Block\Order\Info as OrderInfo;

/**
 * Class Info
 * @package PayU\PaymentGateway\Block\Order\Repay
 */
class Info extends OrderInfo
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Sales::order/info.phtml';

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Order Repayment # %1', $this->getOrder()->getRealOrderId()));
    }
}
