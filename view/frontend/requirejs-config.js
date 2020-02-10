var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-billing-address': {
                'What3Words_What3Words/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'What3Words_What3Words/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/create-shipping-address': {
                'What3Words_What3Words/js/action/create-shipping-address-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'What3Words_What3Words/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/create-billing-address': {
                'What3Words_What3Words/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender': {
                'What3Words_What3Words/js/model/payload-extender': true
            },
            'mage/validation': {
                'What3Words_What3Words/js/w3wValidation': true
            },
            'Magento_Ui/js/lib/validation/rules': {
                'What3Words_What3Words/js/ui-w3wValidation': true
            }
        }
    },
    map: {
        '*': {
            w3wAutocomplete: 'What3Words_What3Words/js/widget/w3wAutosuggest'
        }
    }
};
