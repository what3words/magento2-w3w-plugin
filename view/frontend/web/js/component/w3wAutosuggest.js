define([
    'Magento_Ui/js/form/element/abstract',
    'mage/url',
    'ko',
    'jquery',
    'jquery/ui',
    'domReady!'
], function (Abstract, url, ko, $) {
    'use strict';

    ko.bindingHandlers.autoComplete = {

        init: function () {

            var inputParent = document.getElementById("autosuggest-w3w"),
                hiddenInput = $('input.what3words-autosuggest');


            $(document).on('focus', '.what3words-input', function (e) {
                var country = $('[name="country_id"] option:selected').val();
                inputParent.setAttribute('clip-to-country', country);
            });

            inputParent.addEventListener("select", (value) => {
                if (value.detail !== hiddenInput.val()) {
                    hiddenInput.val(value.detail);
                    hiddenInput.keyup();
                }
            });

        }
    };

    return Abstract.extend();
});
