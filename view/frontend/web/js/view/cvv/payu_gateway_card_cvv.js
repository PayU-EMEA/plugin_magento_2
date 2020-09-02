/*browser:true*/
/*global define*/
var payuSDK = window.payuConfig.env === 'sandbox' ? 'payuSDKSandbox' : 'payuSDK';

define(
    [
        'jquery',
        'repayExtended',
        'mage/url',
        'ko',
        payuSDK,
        'es6Promise'
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
                    template: 'PayU_PaymentGateway/cvv/payu_gateway_card_cvv',
                    isPending: ko.observable(false),
                    secureFormError: ko.observable(''),
                    secureFormOptions: {
                        elementFormCvv: '#payu-card-cvv',
                        config: {
                            placeholder: {
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

                    try {
                        this.payuSDK = PayU(this.posId, true);
                    } catch (e) {
                        this.payuSDK = null
                        console.log(e)
                    }

                    return this;
                },

                renderSecureForm: function () {
                    if (this.payuSDK) {
                        this.secureForms = this.payuSDK.secureForms();
                        this.secureFormCvv = this.secureForms.add('cvv', this.secureFormOptions.config);
                        this.secureFormCvv.render(this.secureFormOptions.elementFormCvv);
                    }
                },
                clearSecureForm: function () {
                    this.secureFormCvv.clear();
                },

                saveCvv: function () {
                    var self = this;

                    $(document.body).trigger('processStart');
                    this.isPending(true);

                    try {
                        this.payuSDK.sendCvv(this.payuSDK.extractRefReqId(this.cvvUrl))
                            .then(function(result) {
                                if (result.status === 'SUCCESS') {
                                    window.location.replace(self.redirectUri);
                                } else {
                                    $(document.body).trigger('processStop');
                                    self.isPending(false);
                                    var errorMessage = "";
                                    result.error.messages.forEach(function(error) {
                                        errorMessage += '<div>' + error.message + '<div>';
                                    });
                                    self.secureFormError(errorMessage);
                                }
                            });
                    } catch(e) {
                        console.log(e);
                        self.secureFormError(JSON.stringify(e));
                        $(document.body).trigger('processStop');
                        self.isPending(false);
                    }
                }
            }
        );
    }
);
