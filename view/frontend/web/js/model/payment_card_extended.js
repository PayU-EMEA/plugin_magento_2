/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mage/url',
        'Magento_Checkout/js/model/full-screen-loader',
        'es6Promise'
    ],
    function (
        $,
        Component,
        additionalValidators,
        url,
        fullScreenLoader
    ) {
        'use strict';

        return Component.extend({

            /**
             * @param {Object} data
             * @return {Boolean}
             */
            isStoredActive: function (data) {
                return data.status === this.storedCards.activeStatus;
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
                return Array.isArray(this.storedCards.list) && this.storedCards.list.length > 0;
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
            isButtonActive: function () {
                return this.getCode() === this.isChecked() && this.validate() && this.isPlaceOrderActionAllowed();
            },

            /**
             * @return {Boolean}
             */
            validate: function () {
                if (this.useNewCard() === false && !this.cardToken()) {
                    return false;
                }

                return this.language === 'pl' ? this.payuAgreement() : true;
            },

            /**
             * @param {Object} data
             * @param {Object} event
             * @return {Boolean}
             */
            placeOrder: function (data, event) {
                var self = this;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate() &&
                    additionalValidators.validate() &&
                    this.isPlaceOrderActionAllowed() === true
                ) {
                    fullScreenLoader.startLoader();
                    this.isPlaceOrderActionAllowed(false);

                    if (this.cardToken()) {
                        this.placeOrderDefer();
                    } else {
                        self.secureFormError('');

                        try {
                            this.payuSDK.tokenize(this.secureForm.storeCard && this.payuStoreCard() ? 'MULTI' : 'SINGLE')
                                .then(function(result) {
                                    if (result.status === 'SUCCESS') {
                                        self.cardToken(result.body.token);
                                        self.placeOrderDefer();
                                    } else {
                                        fullScreenLoader.stopLoader();
                                        self.isPlaceOrderActionAllowed(true);
                                        var errorMessage = "";
                                        result.error.messages.forEach(function(error) {
                                            errorMessage += '<div>' + error.message + '<div>';
                                        });
                                        self.secureFormError(errorMessage);
                                    }
                                });
                        } catch(e) {
                            self.secureFormError(JSON.stringify(e));
                            fullScreenLoader.stopLoader();
                            self.isPlaceOrderActionAllowed(true);
                        }
                    }
                }

                return false;
            },

            placeOrderDefer: function () {
                var self = this;

                this.getPlaceOrderDeferredObject()
                    .fail(
                        function () {
                            fullScreenLoader.stopLoader();
                            self.isPlaceOrderActionAllowed(true);
                        }
                    )
                    .done(
                        function (oderId) {
                            self.afterPlaceOrder();
                            if (self.redirectAfterPlaceOrder) {
                                $.getJSON(url.build(self.postPlaceOrderData), function (response) {
                                    if (response.success && response.redirectUri) {
                                        window.location.replace(response.redirectUri);
                                    } else {
                                        fullScreenLoader.stopLoader();
                                    }
                                });
                            }
                        }
                    );
            }
        });
    }
);
