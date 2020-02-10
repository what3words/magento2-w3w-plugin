define([
    'Magento_Ui/js/form/element/abstract',
    'mage/url',
    'ko',
    'jquery',
    'jquery/ui'
], function (Abstract, url, ko, $) {
    'use strict';

    ko.bindingHandlers.autoComplete = {

        init: function (element, valueAccessor) {

            var settings = valueAccessor(),
                selectedOption = settings.selected,
                options = settings.options;

            var updateElementValueWithLabel = function (event, ui) {
                event.preventDefault();

                if ($(element).attr('aria-invalid') === 'false' && $(element).val()) {
                    if (ui.item) {
                        var newValue = ui.item.label.replace(/ *\([^)]*\) */g, "");
                        $(element).val(newValue);
                    }

                    if(typeof ui.item !== "undefined") {
                        selectedOption(ui.item);
                    }
                }
            };

            $(element).autocomplete({
                appendTo: $('.input-wrap-w3w'),
                source: options,
                select: function (event, ui) {
                    updateElementValueWithLabel(event, ui);
                },
                focus: function (event, ui) {
                    updateElementValueWithLabel(event, ui);
                },
                change: function (event, ui) {
                    updateElementValueWithLabel(event, ui);
                }
            });

        }
    };

    return Abstract.extend({
        selectedOption: ko.observable(''),
        getElements: function( request, response ) {
            var addressVal = request.term,
                countrySelected = '&clip-to-country=' + $('[name="country_id"] option:selected').val(),
                ariaValid = $('input[name*="w3w"]').attr('aria-invalid'),
                apiKey = '&key='+ atob($('input[name*="w3w"]').attr('apiKey')),
                baseUrl = 'https://api.what3words.com/v3/autosuggest?input='+addressVal,
                data = countrySelected+apiKey;

            if (ariaValid === 'false' && addressVal) {
                $.ajax({
                    url: baseUrl,
                    data: data,
                    type: "GET",
                    dataType: 'json',
                    error: function () {
                        alert("An error have occurred.");
                    },
                    success: function (data) {
                        var result = data.suggestions,
                            valuesUpdated = [],
                            nearest;
                        result.map(function(value,index) {
                            if (typeof value['nearestPlace'] !== 'undefined') {
                                nearest = ' ( '+value['nearestPlace']+' )';
                            } else {
                                nearest = '';
                            }
                            valuesUpdated.push(value['words'] + nearest);
                        });

                        response(valuesUpdated);
                    }
                });
            }
        }
    });
});
