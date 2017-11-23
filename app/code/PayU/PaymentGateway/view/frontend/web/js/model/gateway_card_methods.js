define(
    [
        'jquery',
        'mage/url',
        'ko'
    ],
    function ($, url, ko) {
        'use strict';

        return {
            /**
             * @return {void}
             */
            setValidProp: function () {
                var that = this;

                this.valid = ko.computed(function () {
                    if (that.payuWidget.cardValue() !== null) {
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
            setTokenCallback: function () {
                var that = this;
                /**
                 * @param {Object} response
                 */
                window.getTokenCallback = function (response) {
                    var cardData = {};

                    try {
                        that.assignCardValue(response.value);
                        that.assignCardType(response.type);

                        cardData.mask = response.maskedCard;
                        cardData.imageSrc = that.getCardBrandImage(response.maskedCard);
                        cardData.value = response.value;
                        cardData.status = that.stored.activeStatus;

                        that.assignCardData(cardData);
                    } catch (e) {
                        that.errorCallback();
                    }
                };
            },

            /**
             * {void}
             */
            errorCallback: function () {
                this.clearCardValue();
                this.showWarning();
            },

            /**
             * {void}
             */
            assignCardValue: function (cardValue) {
                this.payuWidget.cardValue(cardValue);
            },

            /**
             * {void}
             */
            assignCardType: function (cardType) {
                this.payuWidget.cardType(cardType);
            },

            /**
             * @param {Object} cardData
             * {void}
             */
            assignCardData: function (cardData) {
                this.payuWidget.cardData(cardData);
            },

            /**
             * @param {String} cardMaskNumber
             * @return {String}
             */
            getCardBrandImage: function (cardMaskNumber) {
                var cardType = '',
                    payUImageLink = 'http://static.payu.com/images/mobile/',
                    payUImageExt = '.png';

                if (cardMaskNumber.match(/^4/)) {
                    cardType = 'visa';
                }

                if (cardMaskNumber.match(/^(06|5[0678]|6)/)) {
                    cardType = 'maestro';
                }

                if (cardMaskNumber.match(/^(5[1-5]|2[2-7])/)) {
                    cardType = 'mastercard';
                }

                return payUImageLink + cardType + payUImageExt;
            },

            /**
             * {void}
             */
            clearCardValue: function () {
                if (this.payuWidget.cardValue !== null) {
                    this.payuWidget.cardValue(null);
                }
            },

            /**
             * {void}
             */
            showWarning: function () {
                this.payuWidget.showWarning(true);
            },

            /**
             * {void}
             */
            removeWarning: function () {
                if (this.payuWidget.showWarning() === true) {
                    this.payuWidget.showWarning(false);
                }
            },

            /**
             * {void}
             */
            rendered: function () {
                this.payuScriptHtml(this.createWidgetElement());
            },

            /**
             * @param {Object} data
             * @return {Boolean}
             */
            isStoredActive: function (data) {
                return data.status === this.stored.activeStatus;
            },

            /**
             * @param {Object} data
             * @return {Boolean}
             */
            isStoredSelected: function (data) {
                return data.value === this.payuWidget.cardValue();
            },

            /**
             * @param {Object} data
             */
            selectStored: function (data) {
                if (data.status && data.value) {
                    if (this.isStoredActive(data)) {
                        this.payuWidget.cardValue(data.value);
                    }
                } else {
                    this.payuWidget.cardValue(this.payuWidget.cardData().value);
                }
            },

            /**
             * @return {Boolean}
             */
            storedCardsExist: function () {
                var cards = this.storedCards;

                return cards && !!cards.length;
            },

            /**
             * @return {Boolean}
             */
            storedPexExist: function () {
                var pex = this.storedPex;

                return pex && !!pex.length;
            },

            /**
             * @return {void}
             */
            showWidget: function () {
                var useNew = this.useNewCard();
                this.useNewCard(!useNew);
            },

            /**
             * @return {String}
             */
            createScriptAttributesString: function (attributes) {
                return Object.keys(attributes)
                    .map(this.keyToAttr.bind(attributes))
                    .join(' ');
            },

            /**
             * @return {String}
             */
            keyToAttr: function (key) {
                return key + '="' + this[key] + '"';
            }
        };
    }
);
