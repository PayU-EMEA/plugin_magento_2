define(
    [
        'jquery',
        'ko'
    ],
    function ($, ko) {
        'use strict';

        return {
            /**
             * @return {void}
             */
            setValidProp: function () {
                var that = this;

                this.valid = ko.computed(function () {
                    if (that.payuMethod() !== null && that.payuMethod()) {
                        if (that.locale === 'pl') {
                            return that.payuAgreement();
                        }

                        return true;
                    }

                    return false;
                });
            },

            /**
             * @return {void}
             */
            setSelectedSubscription: function () {
                var that = this;

                this.isPayuSelected.subscribe(function (isSelected) {
                    if (!isSelected) {
                        that.payuMethod(null);
                    }
                });
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
            }
        };
    }
);
