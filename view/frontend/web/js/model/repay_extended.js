/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'uiComponent',
        'ko',
        'mage/url',
        'mage/translate'
    ],
    function ($, Component, ko, url, $t) {
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
            repay: function () {
                var that = this;

                $.ajax({
                    url: url.build(that.repayUrl),
                    data: that.getData(),
                    dataType: 'json',
                    type: 'POST',
                    /**
                     * @return {void}
                     */
                    beforeSend: function () {
                        $('.payment-loader').show();
                        that.repayErrorMsg(null);
                        that.isPending(true);
                    },
                    /**
                     * @param {String} response
                     */
                    success: function (response) {
                        if (response.success && response.redirectUri) {
                            window.location.replace(response.redirectUri);
                        } else {
                            $('.payment-loader').hide();
                            that.repayErrorCallback(response.error);
                        }
                    },
                    /**
                     * @return {void}
                     */
                    error: function () {
                        $('.payment-loader').hide();
                        that.repayErrorCallback();
                    },
                    /**
                     * @return {void}
                     */
                    complete: function () {
                        that.isPending(false);
                    }
                });
            }
        });
    }
);
