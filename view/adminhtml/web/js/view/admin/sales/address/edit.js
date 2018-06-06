define([
    'uiComponent',
    'jquery',
    'ko'
], function(Component, $, ko) {
    return Component.extend({

        /**
         * Initialise element block
         * @param config
         */
        initialize: function (config) {
            this.inputElement = $('input[name="w3w"]');
            this.infoUrl = config.fetchInfoUrl;
            var threeWordAddress = config.w3w;

            if (threeWordAddress && threeWordAddress !== '') {
                this.inputElement.val(threeWordAddress);
            }

            $('#save').on('click', function() {
                $.ajax({
                    url: self.fetchUrl,
                    data: {
                        addressId: addressId
                    }, success: function(response) {
                        // if (response.success && response.w3w !== '') {
                        //     addressItem.children('address').append('/// ' + response.w3w);
                        // }
                    }
                });
            })
        }
    })
});
