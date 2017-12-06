/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'repayExtended',
        'gatewayCardMethods',
        'mage/url',
        'ko',
        'mage/translate',
        'repay',
        'toArray'
    ],
    function ($, Component, gatewayCardMethods, url, ko, $t, repayModel, toArray) {
        'use strict';

        return Component.extend(
            $.extend(
                {},
                gatewayCardMethods,
                {
                    defaults: {
                        template: 'PayU_PaymentGateway/order/payu_gateway_card',
                        sigUrl: 'payu/data/getCardWidgetConfig',
                        payuMethod: ko.observable(null),
                        payuWidget: {
                            showWarning: ko.observable(false),
                            cardValue: ko.observable(null),
                            cardType: ko.observable(null),
                            cardData: ko.observable(null)
                        },
                        isChecked: repayModel.method,
                        payuAgreement: ko.observable(true),
                        agreementText: $t('You must accept the "Terms of a single PayU payment transaction"'),
                        payuScriptHtml: ko.observable(''),
                        payuCCVScriptHtml: ko.observable(''),
                        useNewCard: ko.observable(false),
                        stored: {
                            activeStatus: 'ACTIVE'
                        }
                    },
                    /**
                     * @returns {exports}
                     */
                    initialize: function () {
                        var that = this;
                        this._super();

                        this.storedCards = toArray(this.stored.list.cardTokens);
                        this.storedPex = toArray(this.stored.list.pexTokens);

                        if (!this.storedCardsExist()) {
                            this.useNewCard(true);
                        }

                        this.isCardSelected = ko.computed(function () {
                            return that.getCode() === repayModel.method();
                        });

                        this.setValidProp();
                        this.setTokenCallback();

                        return this;
                    },

                    /**
                     * @return {String}
                     */
                    createWidgetElement: function () {
                        var scriptAttributes,
                            attrString;

                        scriptAttributes = this.getWidgetConfig();

                        attrString = this.createScriptAttributesString(scriptAttributes);

                        return '<script type="text/javascript" ' + attrString + '></script>';
                    },

                    /**
                     * @return {Object}
                     */
                    getWidgetConfig: function () {
                        return this.widgetConfig;
                    },

                    /**
                     * @return {Object}
                     */
                    getData: function () {
                        return {
                            'method': this.getCode(),
                            'payu_method': this.payuWidget.cardValue(),
                            'payu_method_type': this.transferKey,
                            'order_id': this.orderId
                        };
                    }
                }
            )
        );
    }
);
