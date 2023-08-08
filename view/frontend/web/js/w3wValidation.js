define(['jquery'], function ($) {
    'use strict';

    return function() {
        // W3W Address regex
        $.validator.addMethod(
            'validate-w3w',
            function(value) {
                // This can be updated to use the window.what3words.utils.validateAddress function instead so we don't another source of truth to update when we want to update RegEx.
                return /^\/{0,}[^0-9`~!@#$%^&*()+\-_=[{\]}\\|'<,.>?/";:£§º©®\s]{1,}[・.。][^0-9`~!@#$%^&*()+\-_=[{\]}\\|'<,.>?/";:£§º©®\s]{1,}[・.。][^0-9`~!@#$%^&*()+\-_=[{\]}\\|'<,.>?/";:£§º©®\s]{1,}$/i.test(value);
            },
            $.mage.__("What3Words address is invalid.")
        );
    }
});
