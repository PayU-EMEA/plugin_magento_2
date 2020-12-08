/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'uiComponent',
        'ko',
        'mage/url',
        'mage/translate',
        'es6Promise'
    ],
    function (
        $,
        Component,
        ko,
        url,
        $t
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                isPending: ko.observable(false),
                repayErrorMsg: ko.observable(null)
            },

            /**
             * @return {String}
             */
            getCode: function () {
                return this.code;
            },

            /**
             * @return {Boolean}
             */
            isButtonActivePbl: function () {
                return this.getCode() === this.isChecked() && this.validatePbl();
            },

            /**
             * @return {Boolean}
             */
            isButtonActiveCard: function () {
                return this.getCode() === this.isChecked() && this.validateCard();
            },

            /**
             * @param {Object} data
             * @return {Boolean}
             */
            isStoredActive: function (data) {
                return data.status === this.storedActiveStatus;
            },

            /**
             * @param {Object} data
             * @return {Boolean}
             */
            isStoredSelected: function (data) {
                return data.value === this.cardToken();
            },

            /**
             * @param {Object} data
             */
            selectStored: function (data) {
                if (data.status && data.value && this.isStoredActive(data)) {
                    this.cardToken(data.value);
                    this.useNewCard(false);
                    this.clearSecureForm()
                }
            },

            /**
             * @return {Boolean}
             */
            storedCardsExist: function () {
                return Array.isArray(this.storedCards) && this.storedCards.length > 0;
            },

            /**
             * @return {void}
             */
            showWidget: function () {
                this.useNewCard(true);
                this.cardToken(null);
            },

            /**
             * @return {Boolean}
             */
            validateCard: function () {
                if (this.useNewCard() === false && !this.cardToken()) {
                    return false;
                }

                return this.language === 'pl' ? this.payuAgreement() : true;
            },
            /**
             * @return {Boolean}
             */
            validatePbl: function () {
                if (this.payuMethod() === null) {
                    return false;
                }

                return this.language === 'pl' ? this.payuAgreement() : true;
            },

            /**
             * @param {Object} paymentMethod
             */
            setPayuMethod: function (paymentMethod) {
                if (paymentMethod.status === this.enabledStatus) {
                    this.payuMethod(paymentMethod.value);
                }
            },

            /**
             * @param {Function} index
             * @return {Boolean}
             */
            isLastMethod: function (index) {
                return index() === this.methods.length - 1;
            },

            /**
             * @param {String} msg
             */
            repayErrorCallback: function (msg) {
                var that = this,
                    errorMsg = msg || $t('Something went wrong. Please try again.');

                that.repayErrorMsg(errorMsg);
            },

            /**
             * @return {void}
             */
            repayPbl: function () {
                $(document.body).trigger('processStart');
                this.repay(this.getData());
            },

            /**
             * @return {void}
             */
            repayCard: function () {
                var self = this;

                $(document.body).trigger('processStart');

                if (this.useNewCard()) {
                    this.isPending(true);
                    try {
                        this.payuSDK.tokenize(this.secureForm.storeCard && this.payuStoreCard() ? 'MULTI' : 'SINGLE')
                            .then(function(result) {
                                if (result.status === 'SUCCESS') {
                                    self.cardToken(result.body.token);
                                    self.repay(self.getData());
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
                        self.secureFormError(JSON.stringify(e));
                        $(document.body).trigger('processStop');
                        self.isPending(false);
                    }

                } else {
                    this.repay(this.getData());
                }
            },

            /**
             * @return {void}
             */
            repay: function (data) {
                var self = this;

                $.ajax({
                    url: url.build(self.repayUrl),
                    data: data,
                    dataType: 'json',
                    type: 'POST',
                    /**
                     * @return {void}
                     */
                    beforeSend: function () {
                        self.repayErrorMsg(null);
                        self.isPending(true);
                    },
                    /**
                     * @param {String} response
                     */
                    success: function (response) {
                        if (response.success && response.redirectUri) {
                            window.location.replace(response.redirectUri);
                        } else {
                            $(document.body).trigger('processStop');
                            self.repayErrorCallback(response.error);
                        }
                    },
                    /**
                     * @return {void}
                     */
                    error: function () {
                        $(document.body).trigger('processStop');
                        self.repayErrorCallback();
                    },
                    /**
                     * @return {void}
                     */
                    complete: function () {
                        self.isPending(false);
                    }
                });
            }
        });
    }
);
