define([
    'jquery',
    'underscore',
    'mage/template',
    'jquery-ui-modules/widget',
    'jquery-ui-modules/core',
    'mage/translate'
], function ($, _, mageTemplate) {
    'use strict';

    $.widget('w3w.autocomplete', {

        /** @inheritdoc */
        _create: function () {
            var inputParent = document.getElementById("autosuggest-w3w"),
                input = inputParent.querySelector('.what3words-input'),
                hiddenInput = $('input.what3words-autosuggest');
            input.addEventListener("focus", (value) => {
                var country = $('[name="country_id"] option:selected').val();
                inputParent.setAttribute('clip-to-country', country);
            });
            inputParent.addEventListener("select", (value) => {
                if (value.detail !== hiddenInput.val()) {
                    hiddenInput.attr('value', value.detail);
                    hiddenInput.val(value.detail);
                    hiddenInput.keyup();
                }
            });
        }
    });

    return $.w3w.autocomplete;
});
