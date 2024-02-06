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
                hiddenInput = $('input.what3words-autosuggest');
                $(document).on('focus', '.what3words-autosuggest', function () {
                    var country = $('[name="country_id"] option:selected').val();

                    if (customData.clipping === 'clip_to_circle') {
                        inputParent.removeAttribute('clip_to_country');
                        inputParent.setAttribute('clip_to_circle', customData.circle_data);
                    } else if (customData.clipping === 'clip_to_polygon') {
                        inputParent.removeAttribute('clip_to_country');
                        inputParent.setAttribute('clip_to_polygon', customData.polygon_data);
                    } else if (customData.clipping === 'clip_to_bounding_box') {
                        inputParent.removeAttribute('clip_to_country');
                        inputParent.setAttribute('clip_to_bounding_box', customData.box_data);
                    } else if (customData.clipping === 'clip_to_country' && typeof customData.country_iso !== 'undefined') {
                        inputParent.setAttribute('clip_to_country', customData.country_iso);
                    } else {
                        inputParent.setAttribute('clip_to_country', country);
                    }
                    if (customData.save_coordinates === '1') {
                        inputParent.setAttribute('return_coordinates', true);
                    }
                })

            if (customData.autosuggest_focus === '1') {
                navigator.geolocation.getCurrentPosition(function (position) {
                    inputParent.setAttribute('autosuggest_focus',position.coords.latitude + ',' + position.coords.longitude);
                });
            }

            inputParent.addEventListener("coordinates_changed", function (e) {
                if (customData.save_coordinates === '1') {
                    if (customData.save_coordinates === '1') {
                        var coords = e.detail.coordinates.lat + ',' + e.detail.coordinates.lng;
                        $('input[name*=w3w_coordinates]').val(coords);
                    }
                }
            })
            inputParent.addEventListener("selected_suggestion", function (e) {
                if (customData.save_nearest === '1') {
                    if (customData.save_nearest === '1') {
                        var nearestPlace =  e.detail.suggestion.nearestPlace;
                        $('input[name*=w3w_nearest]').val(nearestPlace);
                    }
                }
            });
        }
    });

    return $.w3w.autocomplete;
});
