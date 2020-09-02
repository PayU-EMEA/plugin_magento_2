/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mage/url',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function ($,
              Component,
              additionalValidators,
              url,
              fullScreenLoader
    ) {
        'use strict';

        return Component.extend({
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
                if (this.payuMethod() === null) {
                    return false;
                }

                return this.language === 'pl' ? this.payuAgreement() : true;
            },

            /**
             * @param {Function} index
             * @return {Boolean}
             */
            isLastMethod: function (index) {
                return index() === this.methods.length - 1;
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

                    this.getPlaceOrderDeferredObject()
                        .fail(
                            function () {
                                fullScreenLoader.stopLoader();
                                self.isPlaceOrderActionAllowed(true);
                            }
                        ).done(
                            function (orderId) {
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

                    return true;
                }

                return false;
            }
        });
    }
);
