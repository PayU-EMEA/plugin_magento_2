<?php

namespace PayU\PaymentGateway\Block\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Framework\DataObject;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;
use PayU\PaymentGateway\Model\Ui\ConfigProvider;

/**
 * Class TxnId
 * @package PayU\PaymentGateway\Block\Widget\Grid\Column\Renderer
 */
class TxnId extends AbstractRenderer
{
    /**
     * {@inheritdoc}
     */
    protected function _getValue(DataObject $row)
    {
        /** @var Transaction $transaction */
        $transaction = $row;
        $paymentMethod = $transaction->getOrder()->getPayment()->getMethod();
        $paymentId = $transaction->getAdditionalInformation('payment_id');
        if (in_array($paymentMethod, [ConfigProvider::CODE, CardConfigProvider::CODE]) &&
            $transaction->getTxnType() === 'capture' &&
            $paymentId !== null) {
            return $paymentId;
        }

        return parent::_getValue($row);
    }
}
