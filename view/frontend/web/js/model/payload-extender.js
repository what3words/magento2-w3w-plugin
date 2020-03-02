define([
    'jquery',
    'mage/utils/wrapper'
], function (
    jQuery,
    wrapper
) {
    'use strict';

    return function (processor) {
        return wrapper.wrap(processor, function (proceed, payload) {
            payload = proceed(payload);

            var shippingAddress =  payload.addressInformation.shipping_address;
            var w3w = shippingAddress.extension_attributes.w3w;

            if(w3w == "" || w3w == null){
                if(shippingAddress.customAttributes == "undefined" || shippingAddress.customAttributes == null){
                    w3w = null;
                } else {
                    if(shippingAddress.customAttributes.w3w == "undefined" || shippingAddress.customAttributes.w3w == null) {
                        w3w = null;
                    } else {
                        w3w = shippingAddress.customAttributes.w3w;
                    }
                }
            }

            var goneExtentionAttributes = {
                'w3w': w3w
            };
            payload.addressInformation.extension_attributes = _.extend(
                payload.addressInformation.extension_attributes,
                goneExtentionAttributes
            );

            return payload;
        });
    };
});
