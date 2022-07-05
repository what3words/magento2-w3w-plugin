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

            var shippingAddress =  payload.addressInformation.shipping_address,
                w3w = shippingAddress.extension_attributes.w3w,
                w3w_coordinates = shippingAddress.extension_attributes.w3w_coordinates,
                w3w_nearest = shippingAddress.extension_attributes.w3w_nearest;

            if(w3w === "" || w3w == null){
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

            if(w3w_coordinates === "" || w3w_coordinates == null){
                if(shippingAddress.customAttributes == "undefined" || shippingAddress.customAttributes == null){
                    w3w_coordinates = null;
                } else {
                    if(shippingAddress.customAttributes.w3w_coordinates == "undefined" || shippingAddress.customAttributes.w3w_coordinates == null) {
                        w3w_coordinates = null;
                    } else {
                        w3w_coordinates = shippingAddress.customAttributes.w3w_coordinates;
                    }
                }
            }

            if(w3w_nearest === "" || w3w_nearest == null){
                if(shippingAddress.customAttributes == "undefined" || shippingAddress.customAttributes == null){
                    w3w_nearest = null;
                } else {
                    if(shippingAddress.customAttributes.w3w_nearest == "undefined" || shippingAddress.customAttributes.w3w_nearest == null) {
                        w3w_nearest = null;
                    } else {
                        w3w_nearest = shippingAddress.customAttributes.w3w_nearest;
                    }
                }
            }

            var goneExtentionAttributes = {
                'w3w': w3w,
                'w3w_coordinates': w3w_coordinates,
                'w3w_nearest': w3w_nearest
            };
            payload.addressInformation.extension_attributes = _.extend(
                payload.addressInformation.extension_attributes,
                goneExtentionAttributes
            );

            return payload;
        });
    };
});
