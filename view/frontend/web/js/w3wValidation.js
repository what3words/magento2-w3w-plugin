define(['jquery'], function ($) {
    'use strict';

    return function() {
        // W3W Address regex
        $.validator.addMethod(
            'validate-w3w',
            function(value) {
                return /^\/{0,}[^0-9`~!@#$%^&*()+\-_=[{\]}\\|'<,.>?/";:£§º©®\s]{1,}[・.。][^0-9`~!@#$%^&*()+\-_=[{\]}\\|'<,.>?/";:£§º©®\s]{1,}[・.。][^0-9`~!@#$%^&*()+\-_=[{\]}\\|'<,.>?/";:£§º©®\s]{1,}$/i.test(value);
            },
            $.mage.__("What3Words address is invalid.")
        );
    }
});
