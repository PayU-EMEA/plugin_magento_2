/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component,
              rendererList) {
        'use strict';

        var config = window.checkoutConfig.payment;

        if (config.payuGateway.isActive) {
            rendererList.push(
                {
                    type: 'payu_gateway',
                    component: 'PayU_PaymentGateway/js/view/payment/method-renderer/payu_gateway'
                });
        }

        if (config.payuGatewayCard.isActive) {
            rendererList.push(
                {
                    type: 'payu_gateway_card',
                    component: 'PayU_PaymentGateway/js/view/payment/method-renderer/payu_gateway_card'
                });
        }

        return Component.extend({});
    }
);
