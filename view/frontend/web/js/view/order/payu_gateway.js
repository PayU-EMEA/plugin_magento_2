/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'repayExtended',
        'gatewayMethods',
        'mage/url',
        'ko',
        'mage/translate',
        'repay',
        'toArray'
    ],
    function ($, Component, gatewayMethods, url, ko, $t, repayModel, toArray) {
        'use strict';

        return Component.extend(
            $.extend(
                {},
                gatewayMethods,
                {
                    defaults: {
                        template: 'PayU_PaymentGateway/order/payu_gateway',
                        agreementText: $t('You must accept the "Terms of a single PayU payment transaction"'),
                        enabledStatus: 'ENABLED',
                        isChecked: repayModel.method,
                        payuMethod: ko.observable(false),
                        payuAgreement: ko.observable(true)
                    },

                    /**
                     * @return {exports}
                     */
                    initialize: function () {
                        var that = this;

                        this._super();

                        this.methods = toArray(this.methods).slice();

                        this.isPayuSelected = ko.computed(function () {
                            return that.getCode() === repayModel.method();
                        });

                        this.setValidProp();
                        this.setSelectedSubscription();

                        return this;
                    },

                    /**
                     * @return {Object}
                     */
                    getData: function () {
                        return {
                            'method': this.getCode(),
                            'payu_method': this.payuMethod(),
                            'payu_method_type': this.transferKey,
                            'order_id': this.orderId
                        };
                    }
                }
            )
        );
    }
);
