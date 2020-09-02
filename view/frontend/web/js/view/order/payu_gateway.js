/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'repayExtended',
        'mage/url',
        'ko',
        'mage/translate',
        'repay'
    ],
    function (
        $,
        Component,
        url,
        ko,
        $t,
        repayModel
    ) {
        'use strict';

        return Component.extend(
            {
                defaults: {
                    template: 'PayU_PaymentGateway/order/payu_gateway',
                    enabledStatus: 'ENABLED',
                    isChecked: repayModel.method,
                    payuMethod: ko.observable(null),
                    payuAgreement: ko.observable(true),
                    payuMore1: ko.observable(false),
                    payuMore2: ko.observable(false)
                },

                /**
                 * @return {exports}
                 */
                initialize: function () {
                    this._super();

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
        );
    }
);
