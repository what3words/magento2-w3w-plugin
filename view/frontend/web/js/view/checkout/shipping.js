/**
 * Shipping address in checkout component
 * @author Vicki Tingle
 */
define([
    'Magento_Ui/js/form/element/abstract',
    'Magento_Ui/js/lib/validation/validator',
    'jquery',
    'jquery-typeahead',
    'Magento_Checkout/js/model/quote',
    'autoSuggestPlugin',
    'mage/validation',
    'domReady!'
], function(Element, validation, $, typeahead, quote) {
    'use strict';
    return Element.extend({
        defaults: {
            template: 'What3Words_What3Words/checkout/shipping'
        },
        result: false,

        /**
         * Initialize class
         * @returns {exports}
         */
        initialize: function () {
            this._super();
            this.inputTimeout = null;
            this.allowedCountries = JSON.parse(this.allowed_countries);
            this.countrySelect = $('select[name="country_id"]');
            this.addressHtml = null;
            this.resultCount = 3;
            var self = this;

            // When the customer chooses a new country, run some checks
            $(document).on('change','select[name="country_id"]',function() {
                self.handleCountrySelect();
                self.displayInCheckout();
            });

            // Wait for the field to exist
            var checkFieldExists = setInterval(function() {
                if ($('#what3words-shipping').length) {
                    // Save the address HTML to use with the country select later
                    self.addressHtml = $('.what3words-checkout-field').html();
                    self.initField();
                    self.addValidation();
                    clearInterval(checkFieldExists);
                }
            }, 100);

            // Make sure the country field exists in the DOM before doing anything with it
            var checkCountryFieldExists = setInterval(function() {
                if ($('select[name="country_id"]').length) {
                    self.handleCountrySelect();
                    clearInterval(checkCountryFieldExists);
                }
            }, 100);
            return this;
        },

        /**
         * Initialise what3words plugin on the field
         */
        initField: function() {
            var self = this;
            // If the field doesn't exist, it's been removed by the country selector
            // so just add it back
            if (!$('#what3words-shipping').length) {
                $('.what3words-checkout-field').append(self.addressHtml);
            }
            $('#what3words-shipping').w3wAddress({
                key: self.api_key,
                placeholder: self.placeholder,
                results: self.result_count,
                typeaheadDelay: self.typeahead_delay,
                valid_error: '',
                country_filter: $('select[name="country_id"]').val()
            });
            this.addValidation();
        },

        /**
         * Is the selected country enabled for the plugin?
         * @param iso
         * @returns {boolean}
         */
        isAllowedCountry: function(iso) {
            var allowedCountries = this.allowedCountries;
            return allowedCountries.indexOf(iso) !== -1;
        },

        /**
         * Add the custom input validation
         */
        addValidation: function() {
            var self = this;
            $('.validate-what3words').on('keyup', function () {
                if ($(this).val()) {
                    self.checkInput();
                } else {
                    if ($('.action-save-address').length) {
                        $('.action-save-address').attr('disabled', false);
                    }
                    if ($('button[data-role="opc-continue"]').length) {
                        $('button[data-role="opc-continue"]').attr('disabled', false);
                    }
                }
            });
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
            var saveButton = $('.action-save-address'),
                continueButton = $('button[data-role="opc-continue"]');
            if (saveButton.length) {
                saveButton.attr('disabled', true);
            }
            if (continueButton.length) {
                continueButton.attr('disabled', true);
            }
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
                        var errorHtml = $('<div/>').addClass('mage-error').addClass('what3words-error')
                            .text(response.message);
                        $('.typeahead__query.valid').removeClass('valid');
                        var parent = $('#what3words-shipping').parents('.control._with-tooltip');
                        parent.append(errorHtml);
                    } else {
                        if (saveButton.length) {
                            saveButton.attr('disabled', false);
                        }
                        if (continueButton.length) {
                            continueButton.attr('disabled', false);
                        }
                        if ($('.what3words-error').length) {
                            $('.what3words-error').remove();
                        }
                    }
                }
            });
        },

        /**
         * Render the three word address on loaded addresses
         * in the checkout
         */
        displayInCheckout: function() {
            var addressIds = $('.what3words-hidden-input'),
                self = this;

            $.each(addressIds, function (key, index) {
                var addressId = $(index).val();
                $.ajax({
                    url: self.fetchInfoUrl,
                    data: {
                        addressId: addressId
                    }, success: function (response) {
                        if (response.w3w && response.w3w !== '') {
                            var threeWordElement = $(index)
                                .parents('.shipping-address-item').find('[data-w3w="' + addressId + '"]');
                            threeWordElement.text(response.w3w);
                        }
                    }
                });
            });
        },

        /**
         * Handle what happens when the country changes
         */
        handleCountrySelect: function() {
            var allowedCountries = this.allowed_countries,
                self = this,
                what3wordsField = $('.w3w-control-checkout'),
                fieldLabel = $('.what3words-checkout-field').parents('.field').children('.label');

            var countryCode = $('select[name="country_id"]').val();

            // If we have a country code
            if (countryCode !== '') {
                // If the code is allowed
                if (this.isAllowedCountry(countryCode)) {
                    // Remove the field so it can be re-initialised
                    // with the new country code as the country_filter
                    fieldLabel.css('display', 'block');
                    what3wordsField.remove();
                    this.initField();
                } else {
                    // Remove field and hide label
                    fieldLabel.css('display', 'none');
                    what3wordsField.remove();
                }
            }
        },

        /**
         * AJAX cal to validate 3 word address
         * @param what3words
         * @param iso
         * @param callback
         */
        ajaxCall: function(what3words, iso, callback) {
            var self = this;
            $.ajax({
                url: self.validateUrl,
                data: {
                    what3words: what3words,
                    iso: iso
                },
                success: function (response) {
                    // If the input value doesn't match a W3W string, don't validate
                    if (!what3words.match(/^[a-z]+\b.[a-z]+\b.[a-z]+$/m)) {
                        self.result = false;
                        return callback(false);
                    } else {
                        var container = $('#checkout-step-shipping').find('[name="what3words"]');
                        if (!response.success && !response.country) {
                            if ($('.typeahead__query').hasClass('valid')) {
                                $('.typeahead__query').removeClass('valid');
                            }
                        } else if (response.success && response.country) {
                            // update country success
                        }
                        return callback(response.success);
                    }
                }
            });
        }
    });
});
