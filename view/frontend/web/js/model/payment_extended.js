/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mage/url'
    ],
    function ($, Component, additionalValidators, url) {
        'use strict';

        return Component.extend({
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

                if (this.valid() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    this.getPlaceOrderDeferredObject()
                        .fail(
                            function () {
                                self.isPlaceOrderActionAllowed(true);
                            }
                        ).done(
                        function () {
                            self.afterPlaceOrder();

                            if (self.redirectAfterPlaceOrder) {
                                $.getJSON(url.build(self.postPlaceOrderData), function (response) {
                                    if (response.success && response.redirectUri) {
                                        window.location.replace(response.redirectUri);
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
