define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper,quote) {
    'use strict';

    return function (setBillingAddressAction) {
        return wrapper.wrap(setBillingAddressAction, function (originalAction, messageContainer) {

            var billingAddress = quote.billingAddress();
            if(billingAddress != undefined) {

                if (billingAddress['extension_attributes'] === undefined) {
                    billingAddress['extension_attributes'] = {};
                }

                if (billingAddress.customAttributes != undefined) {
                    $.each(billingAddress.customAttributes, function (key, value) {
                        var code = value['attribute_code'];

                        if($.isPlainObject(value)){
                            value = value['value'];
                        }
                        if (code === 'w3w') {
                            billingAddress['extension_attributes'][code] = value.replace('/', '');;
                        }
                    });
                }

            }

            return originalAction(messageContainer);
        });
    };
});
