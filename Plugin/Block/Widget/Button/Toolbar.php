<?php

namespace Payu\PaymentGateway\Plugin\Block\Widget\Button;

use Magento\Backend\Block\Widget\Button\Toolbar as ToolbarContext;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Sales\Block\Adminhtml\Order\View as OrderView;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;
use PayU\PaymentGateway\Model\Ui\ConfigProvider;

/**
 * Class Toolbar
 * @package Payu\PaymentGateway\Plugin\Block\Widget\Button
 */
class Toolbar
{
    /**
     * Before push button aceept payment and deny payment action key
     */
    const KEY_ACTION_ONCLICK = 'onclick';

    /**
     * Url action key
     */
    const URL_ACTION_KEY = 'action';

    /**
     * Base review payment url
     */
    const REVIEW_PAYMENT_URL = 'payu/data/reviewpayment';

    /**
     * Before Push Button Plugin change url for accept payment and deny payment for PayU payment methods
     *
     * @param ToolbarContext $toolbar
     * @param AbstractBlock|OrderView $context
     * @param ButtonList $buttonList
     *
     * @return array
     */
    public function beforePushButtons(ToolbarContext $toolbar, AbstractBlock $context, ButtonList $buttonList)
    {
        unset($toolbar);
        if (!$context instanceof OrderView) {
            return [$context, $buttonList];
        }
        $paymentMethod = $context->getOrder()->getPayment()->getMethod();
        if ($paymentMethod === CardConfigProvider::CODE || $paymentMethod === ConfigProvider::CODE) {
            $acceptMessage = __('Are you sure you want to accept this payment?');
            $denyMessage = __('Are you sure you want to deny this payment?');
            $denyUrl = $context->getUrl(static::REVIEW_PAYMENT_URL, [static::URL_ACTION_KEY => 'deny']);
            $acceptUrl = $context->getUrl(static::REVIEW_PAYMENT_URL, [static::URL_ACTION_KEY => 'accept']);
            $buttonList->update(
                'accept_payment',
                static::KEY_ACTION_ONCLICK,
                "confirmSetLocation('{$acceptMessage}', '{$acceptUrl}')"
            );
            $buttonList->update(
                'deny_payment',
                static::KEY_ACTION_ONCLICK,
                "confirmSetLocation('{$denyMessage}', '{$denyUrl}')"
            );
        }

        return [$context, $buttonList];
    }
}
