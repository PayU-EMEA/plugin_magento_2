/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'paymentExtended',
        'gatewayCardMethods',
        'mage/url',
        'ko',
        'mage/translate',
        'Magento_Checkout/js/model/quote',
        'toArray'
    ],
    function ($, Component, gatewayCardMethods, url, ko, $t, quote, toArray) {
        'use strict';

        return Component.extend(
            $.extend(
                {},
                gatewayCardMethods,
                {
                    defaults: {
                        template: 'PayU_PaymentGateway/payment/payu_gateway_card',
                        postPlaceOrderData: 'payu/data/getPostPlaceOrderData',
                        sigUrl: 'payu/data/getCardWidgetConfig',
                        logoSrc: window.checkoutConfig.payment.payuGatewayCard.logoSrc,
                        termsUrl: window.checkoutConfig.payment.payuGatewayCard.termsUrl,
                        transferKey: window.checkoutConfig.payment.payuGatewayCard.transferKey,
                        locale: window.checkoutConfig.payment.payuGateway.locale,
                        payuMethod: ko.observable(null),
                        payuWidget: {
                            config: JSON.parse(window.checkoutConfig.payment.payuGatewayCard.ccWidgetConfig),
                            showWarning: ko.observable(false),
                            cardValue: ko.observable(null),
                            cardType: ko.observable(null),
                            cardData: ko.observable(null)
                        },
                        payuAgreement: ko.observable(true),
                        agreementText: $t('You must accept the "Terms of a single PayU payment transaction"'),
                        payuScriptHtml: ko.observable(''),
                        payuCCVScriptHtml: ko.observable(''),
                        useNewCard: ko.observable(false),
                        stored: {
                            list: JSON.parse(window.checkoutConfig.payment.payuGatewayCard.storedCards),
                            activeStatus: 'ACTIVE'
                        }
                    },
                    /**
                     * @returns {exports}
                     */
                    initObservable: function () {
                        var that = this;
                        this._super();

                        this.storedCards = toArray(this.stored.list.cardTokens);
                        this.storedPex = toArray(this.stored.list.pexTokens);

                        if (!this.storedCardsExist()) {
                            this.useNewCard(true);
                        }

                        this.isCardSelected = ko.computed(function () {
                            return that.getCode() === that.isChecked();
                        });

                        this.setValidProp();
                        this.setTokenCallback();

                        return this;
                    },

                    /**
                     * @return {String}
                     */
                    createWidgetElement: function () {
                        var scriptAttributes = this.payuWidget.config,
                            guestEmail = quote.guestEmail,
                            attrString;

                        if (!quote.isCustomerLoggedIn) {
                            scriptAttributes = this.getWidgetConfig(guestEmail);
                        }

                        attrString = this.createScriptAttributesString(scriptAttributes);

                        return '<script type="text/javascript" ' + attrString + '></script>';
                    },

                    /**
                     * @param {String} guestEmail
                     *
                     * @return {Object}
                     */
                    getWidgetConfig: function (guestEmail) {
                        var widgetConfig = {};
                        $.ajax({
                            url: url.build(this.sigUrl),
                            data: {
                                'email': guestEmail
                            },
                            async: false,
                            dataType: 'json',
                            /**
                             * @param {String} response
                             */
                            success: function (response) {
                                if (response.success && response.widgetConfig) {
                                    widgetConfig = JSON.parse(response.widgetConfig);
                                }
                            }
                        });

                        return widgetConfig;
                    },

                    /**
                     * @return {String}
                     */
                    getCode: function () {
                        return 'payu_gateway_card';
                    },

                    /**
                     * @return {Object}
                     */
                    getData: function () {
                        return {
                            'method': this.item.method,
                            'additional_data': {
                                'payu_method': this.payuWidget.cardValue(),
                                'payu_method_type': this.transferKey
                            }
                        };
                    }
                }
            )
        );
    }
);
