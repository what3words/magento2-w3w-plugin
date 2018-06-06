/**
 * Mixin for shipping save processor
 * @author Vicki Tingle
 */
define([
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/resource-url-manager',
    'mage/storage',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/select-billing-address',
    'jquery'

], function(
    ko,
    quote,
    resourceUrlManager,
    storage,
    paymentService,
    methodConverter,
    errorProcessor,
    fullScreenLoader,
    selectBillingAddressAction,
    $
){
    'use strict';
    return function(processor) {
        /**
         * This override injects what3words into the 'address information' for the customer
         * and runs an AJAX request to create a record for the quote
         */
        processor.saveShippingInformation = function() {
            var payload;

            if (!quote.billingAddress()) {
                selectBillingAddressAction(quote.shippingAddress());
            }


            var what3words = $('#what3words-shipping').val(),
                saveInAddressBook = false,
                customerAddressId = quote.shippingAddress().customerAddressId;

            // Do we want to set a save customer address flag?
            if (quote.shippingAddress().saveInAddressBook != 'undefined'
                && quote.shippingAddress().saveInAddressBook) {
                saveInAddressBook = true;
            }

            // Save the what3words with an ajax request
            $.ajax({
                url: '../what3words/checkout/shipping',
                data: {
                    what3words: what3words,
                    saveInAddressBook: saveInAddressBook,
                    customerAddressId: customerAddressId
                },
                type: 'POST'
            });

            // Pass what3words into the address info payload just in case.
            payload = {
                addressInformation: {
                    shipping_address: quote.shippingAddress(),
                    billing_address: quote.billingAddress(),
                    shipping_method_code: quote.shippingMethod().method_code,
                    shipping_carrier_code: quote.shippingMethod().carrier_code
                },
                extension_attributes: {
                    what3words: what3words
                }
            };

            fullScreenLoader.startLoader();

            return storage.post(
                resourceUrlManager.getUrlForSetShippingInformation(quote),
                JSON.stringify(payload)
            ).done(
                function (response) {
                    quote.setTotals(response.totals);
                    paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                    fullScreenLoader.stopLoader();
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                    fullScreenLoader.stopLoader();
                }
            );
        };
        return processor;
    };
});
