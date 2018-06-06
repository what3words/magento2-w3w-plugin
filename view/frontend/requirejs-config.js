var config = {
    paths: {
        'jquery-typeahead': 'What3Words_What3Words/js/resource/magento.jquery.typeahead'
    },
    map : {
        '*' : {
            autoSuggestPlugin: 'What3Words_What3Words/js/resource/magento.jquery.w3w-autosuggest-plugin',
            'Magento_Checkout/model/shipping-save-processor': 'What3Words_What3Words/js/model/default',
            "Magento_Checkout/template/shipping-address/address-renderer/default.html":
                "What3Words_What3Words/template/checkout/address.html"
        }
    },
    shim: {
        'jquery-typeahead': {
            deps: ['jquery']
        }
    }
};
