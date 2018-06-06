/**
 * Edit customer account area component
 * @author Vicki Tingle
 */
define([
    'uiComponent',
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/lib/validation/validator',
    'jquery-typeahead',
    'autoSuggestPlugin',
    'mage/validation'
], function(Component, $, ko, _, validation) {
    return Component.extend({

        /**
         * Initialise variables
         * @param config
         */
        initialize: function (config) {
            var self = this;
            this.config = config;
            this.validationFired = false;
            this.validateUrl = config.validateUrl;
            this.countryElem = $('select[name="country_id"]');
            this.result = false;
            this.saveButton = $('button[data-action="save-address"]');
            this.formSubmitted = false;
            this.allowedCountries = config.allowedCountries;

            this.inputTimeout = null;

            // If the country loaded at the start is in our list of allowed countries
            if (this.isAllowedCountry(this.countryElem.val())) {
                self.appendHtml();
                self.validateInput();
            }

            // // Initialise the input at the start
            self.initInputField(self.countryElem.val());

            // // Update the input's config when the country changes
            self.countryElem.on('change', function () {
                var countryCode = self.countryElem.val();
                $('#what3words-customer-field').remove();
                if (self.isAllowedCountry(countryCode)) {
                    self.appendHtml();
                    self.initInputField(countryCode);
                    self.validateInput();
                }
            });
        },

        /**
         * Add the 'has user stopped typing?' timed validation to the input
         */
        validateInput: function() {
            var self = this;
            // Validate the input
            $('.validate-what3words').on('keyup', function () {
                if ($(this).val()) {
                    self.checkInput();
                } else {
                    self.saveButton.attr('disabled', false);
                }
            });
        },

        isAllowedCountry: function(iso) {
            var allowedCountries = this.allowedCountries;
            return allowedCountries.indexOf(iso) !== -1;
        },

        /**
         * Run our custom validation whenever
         * the user hasn't typed in the box for 1 second
         */
        checkInput: function () {
            clearTimeout(this.inputTimeout);
            this.inputTimeout = setTimeout ( function () {
                this.runCustomValidation()
            }.bind(this), 1000)
        },

        /**
         * Our custom validation
         * Used as the AJAX call resulted in conflicts
         * with Magento 2's core validation
         */
        runCustomValidation: function() {
            // Disable save button while we check
            this.saveButton.attr('disabled', true);

            var self = this;
            $.ajax({
                url: this.validateUrl,
                data: {
                    what3words: $('.validate-what3words').val(),
                    iso: $('select[name="country_id"]').val()
                },
                success: function (response) {
                    if (!response.success) {
                        if ($('.what3words-error').length) {
                            $('.what3words-error').remove();
                        }

                        // Append our custom error div
                        var errorHtml = $('<div/>').addClass('mage-error').addClass('what3words-error')
                            .text(response.message);
                        if ($('.typeahead__query.valid')) {
                            $('.typeahead__query.valid').removeClass('valid');

                        }
                        var parent = $('#what3words-customer-field');
                        parent.append(errorHtml);
                    } else {
                        // If the validation was successful, re-enable the save button
                        self.saveButton.attr('disabled', false);
                        if (!$('.typeahead__query').hasClass('valid')) {
                            $('.typeahead__query').addClass('valid');
                        }
                        if ($('.what3words-error').length) {
                            $('.what3words-error').remove();
                        }
                    }
                }
            });
        },

        /**
         * Insert the w3w HTML block
         */
        appendHtml: function() {
            var fieldHtml = $('#what3words-customer-address').html();
            $(fieldHtml).insertAfter('.country');
        },

        /**
         * Initialise the input with the country filter
         * @param iso
         */
        initInputField: function(iso) {
            var self = this,
                input = $('#what3words');

            input.w3wAddress({
                key: self.config.apiKey,
                placeholder: self.config.placeholder,
                results: 3,
                typeaheadDelay: self.config.typeaheadDelay,
                valid_error: '',
                country_filter: iso
            });
        }
    })
});
