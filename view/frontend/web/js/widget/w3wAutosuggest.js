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
        options: {
            autocomplete: 'on',
            minaddressLength: 8,
            responseFieldElements: 'ul li',
            selectClass: 'selected',
            template:
                '<li class="<%- data.row_class %>" data-address="<%- address %>" id="w3w-address-<%- data.index %>" role="option">' +
                ' <%- data %>' +
                '</li>',
            isExpandable: null,
            suggestionDelay: 300,
            url: 'https://api.what3words.com/v3/autosuggest?input=',

        },

        /** @inheritdoc */
        _create: function () {
            this.responseList = {
                indexList: null,
                selected: null
            };
            this.autoComplete = $(this.options.destinationSelector);
            this.addressForm = $(this.options.formSelector);
            this.isExpandable = this.options.isExpandable;

            _.bindAll(this, 'onPropChange');


            this.element.attr('autocomplete', this.options.autocomplete);

            this.element.on('blur', $.proxy(function () {

                setTimeout($.proxy(function () {
                    if (this.autoComplete.is(':hidden')) {
                        this.setActiveState(false);
                    } else {
                        this.element.trigger('focus');
                    }
                    this.autoComplete.hide();
                    this._updateAriaHasPopup(false);
                }, this), 250);
            }, this));

            if (this.element.get(0) === document.activeElement) {
                this.setActiveState(true);
            }

            this.element.on('focus', this.setActiveState.bind(this, true));
            // Prevent spamming the server with requests
            this.element.on('input propertychange', _.debounce(this.onPropChange, this.options.suggestionDelay));
        },

        /**
         * Sets state of the w3w address field to provided value.
         *
         * @param {Boolean} isActive
         */
        setActiveState: function (isActive) {
            var addressValue;

            if (this.isExpandable) {
                this.element.attr('aria-expanded', isActive);
                addressValue = this.element.val();
                this.element.val('');
                this.element.val(addressValue);
            }
        },

        /**
         * @private
         * @return {Element} The last element in the suggestion list.
         */
        getLastElement: function () {
            return this.responseList.indexList ? this.responseList.indexList.last() : false;
        },

        /**
         * @private
         * @param {Boolean} show - Set attribute aria-haspopup to "true/false" for element.
         */
        updateAriaHasPopup: function (show) {
            if (show) {
                this.element.attr('aria-haspopup', 'true');
            } else {
                this.element.attr('aria-haspopup', 'false');
            }
        },

        /**
         * Clears the item selected from the suggestion list and resets the suggestion list.
         * @private
         * @param {Boolean} all - Controls whether to clear the suggestion list.
         */
        resetResponseList: function (all) {
            this.responseList.selected = null;

            if (all === true) {
                this.responseList.indexList = null;
            }
        },

        /**
         * Executes when the value of the w3w address input field changes. Executes a GET request
         * to populate a suggestion list based on entered text.
         * @private
         */
        onPropChange: function () {
            var addressField = this.element,
                clonePosition = {
                    position: 'absolute',
                    width: addressField.outerWidth()
                },
                source = this.options.template,
                template = mageTemplate(source),
                dropdown = $('<ul role="listbox"></ul>'),
                value = this.element.val(),
                countrySelected = 'clip-to-country=' + $('[name="country_id"] option:selected').val(),
                apiKey = '&key='+ atob($('input[name*="w3w"]').attr('apiKey')),
                params = countrySelected+apiKey;

            if (value.length >= parseInt(this.options.minaddressLength, 10)) {
                $.getJSON(this.options.url+value, params, $.proxy(function (data) {
                    if (data.suggestions.length) {
                        $.each(data.suggestions, function (index, element) {
                            var html,
                                nearest;

                            element.index = index;
                            if (typeof element.nearestPlace !== 'undefined') {
                                nearest = ' ( '+element.nearestPlace+' )';
                            } else {
                                nearest = '';
                            }
                            html = template({
                                address: element.words,
                                data: element.words + nearest
                            });
                            dropdown.append(html);
                        });

                        this.resetResponseList(true);

                        this.responseList.indexList = this.autoComplete.html(dropdown)
                            .css(clonePosition)
                            .show()
                            .find(this.options.responseFieldElements + ':visible');

                        this.element.removeAttr('aria-activedescendant');

                        if (this.responseList.indexList.length) {
                            this.updateAriaHasPopup(true);
                        } else {
                            this.updateAriaHasPopup(false);
                        }

                        this.responseList.indexList
                            .on('click', function (e) {
                                this.responseList.selected = $(e.currentTarget);
                                addressField.val(this.responseList.selected.data('address'));
                            }.bind(this))
                            .on('mouseenter mouseleave', function (e) {
                                this.responseList.indexList.removeClass(this.options.selectClass);
                                $(e.target).addClass(this.options.selectClass);
                                this.responseList.selected = $(e.target);
                                this.element.attr('aria-activedescendant', $(e.target).attr('id'));
                            }.bind(this))
                            .on('mouseout', function (e) {
                                if (!this.getLastElement() &&
                                    this.getLastElement().hasClass(this.options.selectClass)) {
                                    $(e.target).removeClass(this.options.selectClass);
                                    this.resetResponseList(false);
                                }
                            }.bind(this));
                    } else {
                        this.resetResponseList(true);
                        this.autoComplete.hide();
                        this.updateAriaHasPopup(false);
                        this.element.removeAttr('aria-activedescendant');
                    }
                }, this));
            } else {
                this.resetResponseList(true);
                this.autoComplete.hide();
                this.updateAriaHasPopup(false);
                this.element.removeAttr('aria-activedescendant');
            }
        }
    });

    return $.w3w.autocomplete;
});
