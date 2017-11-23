<?php

namespace PayU\PaymentGateway\Block;

use Magento\Payment\Block\ConfigurableInfo;
use PayU\PaymentGateway\Gateway\Response\FraudHandler;

/**
 * Class Info
 * @package PayU\PaymentGateway\Block
 */
class Info extends ConfigurableInfo
{
    /**
     * {@inheritdoc}
     */
    protected function getLabel($field)
    {
        return __($field);
    }

    /**
     * {@inheritdoc}
     */
    protected function getValueView($field, $value)
    {
        if ($field === FraudHandler::FRAUD_MSG_LIST) {
            return implode('; ', $value);
        }

        return parent::getValueView($field, $value);
    }
}
