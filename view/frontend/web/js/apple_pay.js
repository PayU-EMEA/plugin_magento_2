define([], function () {
    var isApplePayAvaiable = function () {
        try {
            return window.ApplePaySession && window.ApplePaySession.canMakePayments();
        } catch (e) {
            return false;
        }
    }

    return {
        filterApplePay: function (methods) {
            if (Array.isArray(methods)) {
                return methods.filter(function(method) {
                    return method.value !== 'jp' || (method.value === 'jp' && isApplePayAvaiable())
                })
            }

            return [];
        }
    };
});
