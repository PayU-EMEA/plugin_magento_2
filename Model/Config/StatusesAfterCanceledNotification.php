<?php

namespace PayU\PaymentGateway\Model\Config;


/**
 * Get list of statuses after order place
 *
 * Class StatusesAfterOrderPlace
 * @package PayU\PaymentGateway\Model\Config
 */
class StatusesAfterCanceledNotification implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'without_changing', 'label' => __('Without changing status')], ['value' => 'canceled', 'label' => __('Canceled ')]];
    }

}
