/*browser:true*/
/*global define*/
var payuSDK = window.checkoutConfig.payment.payuGatewayCard.secureFormConfig.env === 'sandbox' ? 'payuSDKSandbox' : 'payuSDK';
define(
    [
        'jquery',
        'paymentCardExtended',
        'mage/url',
        'ko',
        payuSDK
    ],
    function (
        $,
        Component,
        url,
        ko
    ) {
        'use strict';

        return Component.extend(
            {
                defaults: {
                    template: 'PayU_PaymentGateway/payment/payu_gateway_card',
                    postPlaceOrderData: 'payu/data/getPostPlaceOrderData',
                    logoSrc: window.checkoutConfig.payment.payuGatewayCard.logoSrc,
                    termsUrl: window.checkoutConfig.payment.payuGatewayCard.termsUrl,
                    transferKey: window.checkoutConfig.payment.payuGatewayCard.transferKey,
                    language: window.checkoutConfig.payment.payuGatewayCard.language,
                    cardToken: ko.observable(null),
                    payuAgreement: ko.observable(true),
                    payuStoreCard: ko.observable(true),
                    payuMore1: ko.observable(false),
                    payuMore2: ko.observable(false),
                    useNewCard: ko.observable(false),
                    storedCards: {
                        list: window.checkoutConfig.payment.payuGatewayCard.storedCards,
                        activeStatus: 'ACTIVE'
                    },
                    secureForm: window.checkoutConfig.payment.payuGatewayCard.secureFormConfig,
                    secureFormError: ko.observable(''),
                    secureFormOptions: {
                        elementFormNumber: '#payu-card-number',
                        elementFormDate: '#payu-card-date',
                        elementFormCvv: '#payu-card-cvv',
                        config: {
                            cardIcon: true,
                            placeholder: {
                                number: '',
                                cvv: ''
                            },
                            style: {
                                basic: {
                                    fontSize: '18px',
                                }
                            },
                            lang: 'en'
                        }
                    }
                },

                /**
                 * @returns {exports.initialize}
                 */
                initialize: function () {
                    this._super();

                    this.secureFormOptions.config.lang = this.language;

                    if (!this.storedCardsExist()) {
                        this.useNewCard(true);
                    }
                    try {
                        this.payuSDK = PayU(this.secureForm['posId'], true);
                    } catch (e) {
                        this.payuSDK = null
                        console.log(e)
                    }

                    return this;
                },

                renderSecureForm: function () {
                    this.secureForms = this.payuSDK.secureForms();
                    this.secureFormNumber = this.secureForms.add('number', this.secureFormOptions.config);
                    this.secureFormNumber.render(this.secureFormOptions.elementFormNumber);
                    this.secureFormDate = this.secureForms.add('date', this.secureFormOptions.config);
                    this.secureFormDate.render(this.secureFormOptions.elementFormDate);
                    this.secureFormCvv = this.secureForms.add('cvv', this.secureFormOptions.config);
                    this.secureFormCvv.render(this.secureFormOptions.elementFormCvv);
                },

                clearSecureForm: function () {
                    this.secureFormNumber.clear();
                    this.secureFormDate.clear();
                    this.secureFormCvv.clear();
                },

                /**
                 * @return {Object}
                 */
                getData: function () {
                    return {
                        'method': this.item.method,
                        'additional_data': {
                            'payu_method': this.cardToken(),
                            'payu_method_type': this.transferKey
                        }
                    };
                }
            }
        );
    }
);
