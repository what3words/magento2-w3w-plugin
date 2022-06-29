define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction, messageContainer) {

            var shippingAddress = quote.shippingAddress();

            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }
            if (shippingAddress.customAttributes != undefined) {
                $.each(shippingAddress.customAttributes, function (key, value) {
                    var code = value['attribute_code'];
                    if ($.isPlainObject(value)) {
                        value = value['value'];
                    }
                    shippingAddress['customAttributes'][code] = value;
                    shippingAddress['extension_attributes'][code] = value;
                });
            }

            return originalAction(messageContainer);
        });
    };
});
