<?php

namespace PayU\PaymentGateway\Model\Config;


/**
 * Get list of statuses after order place
 *
 * Class StatusesAfterOrderPlace
 * @package PayU\PaymentGateway\Model\Config
 */
class StatusesAfterOrderPlace implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'pending', 'label' => __('Pending')], ['value' => 'pending_payment', 'label' => __('Pending Payment')]];
    }

}
