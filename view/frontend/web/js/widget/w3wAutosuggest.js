define([
    'jquery',
    'underscore',
    'jquery-ui-modules/widget',
    'jquery-ui-modules/core',
    'mage/translate'
], function ($, _) {
    'use strict';

    $.widget('w3w.autocomplete', {

        /** @inheritdoc */
        _create: function () {
            var inputParent = document.getElementById("autosuggest-w3w"),
                input = inputParent.querySelector('.what3words-input'),
                customData = window.w3wConfig,
                hiddenInput = $('input.what3words-autosuggest'),
                country = $('[name="country_id"] option:selected').val();

            input.addEventListener("focus", (value) => {

            });

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

            inputParent.addEventListener("select", (value) => {
                if (value.detail !== hiddenInput.val()) {
                    hiddenInput.attr('value', value.detail);
                    hiddenInput.val(value.detail);
                    hiddenInput.keyup();
                }

                if (value.detail) {
                    if (customData.save_coordinates === '1' || customData.save_nearest === '1') {
                        if (customData.save_coordinates === '1') {
                            var coords = value.target.coordinatesLng + ' ,' + value.target.coordinatesLat;
                            $('input[name*=w3w_coordinates]').val(coords);
                        }
                        if (customData.save_nearest === '1') {
                            $('input[name*=w3w_nearest]').val(value.target.nearestPlace);
                        }
                    }
                }
            });
        }
    });

    return $.w3w.autocomplete;
});
