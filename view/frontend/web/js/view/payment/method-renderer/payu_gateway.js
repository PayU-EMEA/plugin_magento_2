/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'paymentExtended',
        'gatewayMethods',
        'ko',
        'mage/translate',
        'toArray'
    ],
    function ($, Component, gatewayMethods, ko, $t, toArray) {
        'use strict';

        return Component.extend(
            $.extend(
                {},
                gatewayMethods,
                {
                    defaults: {
                        template: 'PayU_PaymentGateway/payment/payu_gateway',
                        postPlaceOrderData: 'payu/data/getPostPlaceOrderData',
                        logoSrc: window.checkoutConfig.payment.payuGateway.logoSrc,
                        termsUrl: window.checkoutConfig.payment.payuGateway.termsUrl,
                        transferKey: window.checkoutConfig.payment.payuGateway.transferKey,
                        locale: window.checkoutConfig.payment.payuGateway.locale,
                        methods: toArray(JSON.parse(window.checkoutConfig.payment.payuGateway.payByLinks)),
                        payuMethod: ko.observable(false),
                        payuAgreement: ko.observable(true),
                        agreementText: $t('You must accept the "Terms of a single PayU payment transaction"'),
                        enabledStatus: 'ENABLED'
                    },

                    /**
                     * @return {exports}
                     */
                    initObservable: function () {
                        var that = this;
                        this._super();

                        this.isPayuSelected = ko.computed(function () {
                            return that.getCode() === that.isChecked();
                        });

                        this.setValidProp();
                        this.setSelectedSubscription();

                        return this;
                    },

                    /**
                     * @return {String}
                     */
                    getCode: function () {
                        return 'payu_gateway';
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
            )
        );
    }
);
