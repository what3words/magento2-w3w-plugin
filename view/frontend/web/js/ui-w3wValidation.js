define(['mage/translate'], function($t) {
    'use strict';

    return function(rules) {
        rules['validate-w3w'] = {
            handler: function (value) {
                return /^\/{0,}[^0-9`~!@#$%^&*()+\-_=[{\]}\\|'<,.>?/";:£§º©®\s]{1,}[・.。][^0-9`~!@#$%^&*()+\-_=[{\]}\\|'<,.>?/";:£§º©®\s]{1,}[・.。][^0-9`~!@#$%^&*()+\-_=[{\]}\\|'<,.>?/";:£§º©®\s]{1,}$/i.test(value);
            },
            message: $t('What3Words address is invalid.')
        };
        return rules;
    };
});
