/*browser:true*/
/*global define*/
define(
    [],
    function () {
        'use strict';

        return function (config) {
            /**
             * @return {Void}
             */
            function tokenCallback() {
                window.location.href = config.redirectUrl;
            }

            window.getTokenCallback = tokenCallback;
        };
    }
);
