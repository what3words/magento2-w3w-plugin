define([
    'Magento_Ui/js/form/element/abstract',
    'mage/url',
    'ko',
    'jquery',
    'Magento_Checkout/js/model/quote',
    "Magento_Customer/js/customer-data",
    'jquery/ui',
    'domReady!'
], function (Abstract, url, ko, $, quote, customerData) {
    'use strict';

    ko.bindingHandlers.autoComplete = {

        init: function () {

            var inputParent = document.getElementById("autosuggest-w3w"),
                customData = window.w3wConfig,
                hiddenInput = $('input.what3words-autosuggest'),
                quoteAddress = quote.shippingAddress(),
                checkoutData = customerData.get('checkout-data')();

            inputParent.setAttribute('headers', '{"X-W3W-Plugin": "what3words-Magento/'+customData.w3w_version+'()"}');

            $(document).on('focus', '.what3words-input', function () {
                var country = $('[name="country_id"] option:selected').val();
                if (customData.clipping === 'clip-to-circle') {
                    inputParent.setAttribute('clip-to-circle', customData.circle_data);
                } else if (customData.clipping === 'clip-to-polygon') {
                    inputParent.setAttribute('clip-to-polygon', customData.polygon_data);
                } else if (customData.clipping === 'clip-to-bounding-box') {
                    inputParent.setAttribute('clip-to-bounding-box', customData.box_data);
                } else if (customData.clipping === 'clip-to-country' && typeof customData.country_iso !== 'undefined') {
                    inputParent.setAttribute('clip-to-country', customData.country_iso);
                } else {
                    inputParent.setAttribute('clip-to-country', country);
                }
                if (customData.save_coordinates === '1') {
                    inputParent.setAttribute('return-coordinates', 'true');
                }
            });

            inputParent.addEventListener("select", (value) => {

                if (value.detail !== hiddenInput.val()) {
                    hiddenInput.val(value.detail);
                    hiddenInput.attr('value', value.detail);
                    hiddenInput.keyup();
                }

                if (value.detail) {
                    if (customData.save_coordinates === '1' || customData.save_nearest === '1') {
                        var w3wCustom = [];

                        if (quoteAddress['custom_attributes'] === undefined) {
                            quoteAddress['custom_attributes'] = {};
                        }

                        if (quoteAddress['extension_attributes'] === undefined) {
                            quoteAddress['extension_attributes'] = {};
                        }

                        if (customData.save_coordinates === '1') {
                            var coords = value.target.coordinatesLng + ' ,' + value.target.coordinatesLat;
                            quoteAddress['extension_attributes']['w3w_coordinates'] = coords;
                            quoteAddress['custom_attributes']['w3w_coordinates'] = coords;
                            $('input[name*=w3w_coordinates]').val(coords);
                            checkoutData.shippingAddressFromData.custom_attributes.w3w_coordinates = coords;

                        }
                        if (customData.save_nearest === '1') {
                            quoteAddress['extension_attributes']['w3w_coordinates'] = value.target.nearestPlace;
                            quoteAddress['custom_attributes']['w3w_coordinates'] = value.target.nearestPlace;
                            $('input[name*=w3w_nearest]').val(value.target.nearestPlace);
                            checkoutData.shippingAddressFromData.custom_attributes.w3w_nearest = value.target.nearestPlace;
                        }
                    }
                }
            });
        }
    };

    return Abstract.extend();
});
