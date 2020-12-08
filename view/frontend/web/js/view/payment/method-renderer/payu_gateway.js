/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'paymentPblExtended',
        'ko',
        'applePay'
    ],
    function ($,
              Component,
              ko,
              applePay
    ) {
        'use strict';

        return Component.extend(
            {
                defaults: {
                    template: 'PayU_PaymentGateway/payment/payu_gateway',
                    postPlaceOrderData: 'payu/data/getPostPlaceOrderData',
                    logoSrc: window.checkoutConfig.payment.payuGateway.logoSrc,
                    termsUrl: window.checkoutConfig.payment.payuGateway.termsUrl,
                    transferKey: window.checkoutConfig.payment.payuGateway.transferKey,
                    language: window.checkoutConfig.payment.payuGateway.language,
                    payuMethod: ko.observable(null),
                    payuAgreement: ko.observable(true),
                    payuMore1: ko.observable(false),
                    payuMore2: ko.observable(false),
                    enabledStatus: 'ENABLED'
                },

                    /**
                     * @return {exports}
                     */
                    initialize: function () {
                        this._super();
                        this.methods = applePay.filterApplePay(window.checkoutConfig.payment.payuGateway.payByLinks);

                        return this;
                    },

                /**
                 * @return {Object}
                 */
                getData: function () {
                    return {
                        'method': this.item.method,
                        'additional_data': {
                            'payu_method': this.payuMethod(),
                            'payu_method_type': this.transferKey
                        }
                    };
                }
            }
        );
    }
);
