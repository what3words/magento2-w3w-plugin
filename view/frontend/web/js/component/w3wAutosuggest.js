define([
  "Magento_Ui/js/form/element/abstract",
  "mage/url",
  "ko",
  "jquery",
  "Magento_Checkout/js/model/quote",
  "Magento_Customer/js/customer-data",
  "jquery/ui",
  "domReady!",
], function (Abstract, url, ko, $, quote, customerData) {
  "use strict";

  ko.bindingHandlers.autoComplete = {
    init: function () {
      const inputParent = document.getElementById("autosuggest-w3w"),
        customData = window.w3wConfig,
        quoteAddress = quote.shippingAddress(),
        checkoutData = customerData.get("checkout-data")(),
        headers =
          '{"X-W3W-Plugin": "what3words-Magento/' +
          customData.w3w_version +
          " ({Magento_version:" +
          customData.magento_version +
          ', Location: Checkout})"}';

      inputParent.setAttribute("headers", headers);
      if (customData.api_key) {
        inputParent.setAttribute("api_key", customData.api_key);
      }

      $(document).on("focus", ".what3words-autosuggest", function () {
        const country = $('[name="country_id"] option:selected').val();
        if (customData.clipping === "clip_to_circle") {
          inputParent.removeAttribute("clip_to_country");
          inputParent.setAttribute("clip_to_circle", customData.circle_data);
        } else if (customData.clipping === "clip_to_polygon") {
          inputParent.removeAttribute("clip_to_country");
          inputParent.setAttribute("clip_to_polygon", customData.polygon_data);
        } else if (customData.clipping === "clip_to_bounding_box") {
          inputParent.removeAttribute("clip_to_country");
          inputParent.setAttribute("clip_to_bounding_box", customData.box_data);
        } else if (
          customData.clipping === "clip_to_country" &&
          typeof customData.country_iso !== "undefined"
        ) {
          inputParent.setAttribute("clip_to_country", customData.country_iso);
        } else {
          inputParent.setAttribute("clip_to_country", country);
        }
        if (customData.save_coordinates === "1") {
          inputParent.setAttribute("return_coordinates", true);
        }
      });
      if (customData.autosuggest_focus === "1") {
        navigator.geolocation.getCurrentPosition(function (position) {
          inputParent.setAttribute(
            "autosuggest_focus",
            position.coords.latitude + "," + position.coords.longitude
          );
        });
      }
      if (customData.lang !== "null") {
        inputParent.setAttribute("lang", customData.lang);
      }
      if (customData.invalid_error_message) {
        inputParent.setAttribute(
          "invalid_address_error_message",
          customData.invalid_error_message
        );
      }

      if (customData.rtl === "1") {
        inputParent.setAttribute("dir", "rtl");
      }

      inputParent.addEventListener("coordinates_changed", function (e) {
        if (customData.save_coordinates === "1") {
          if (quoteAddress["custom_attributes"] === undefined) {
            quoteAddress["custom_attributes"] = {};
          }

          if (quoteAddress["extension_attributes"] === undefined) {
            quoteAddress["extension_attributes"] = {};
          }

          if (!checkoutData.shippingAddressFromData) {
            checkoutData.shippingAddressFromData = {};
          }

          if (!checkoutData.shippingAddressFromData.custom_attributes) {
            checkoutData.shippingAddressFromData.custom_attributes = {};
          }

          const coords =
            e.detail.coordinates.lat + "," + e.detail.coordinates.lng;
          quoteAddress["extension_attributes"]["w3w_coordinates"] = coords;
          quoteAddress["custom_attributes"]["w3w_coordinates"] = coords;
          $("input[name*=w3w_coordinates]").val(coords);
          checkoutData.shippingAddressFromData.custom_attributes.w3w_coordinates =
            coords;
        }
      });
      inputParent.addEventListener("selected_suggestion", function (e) {
        if (customData.save_nearest === "1") {
          if (quoteAddress["custom_attributes"] === undefined) {
            quoteAddress["custom_attributes"] = {};
          }

          if (quoteAddress["extension_attributes"] === undefined) {
            quoteAddress["extension_attributes"] = {};
          }

          if (customData.save_nearest === "1") {
            const nearestPlace = e.detail.suggestion.nearestPlace;
            quoteAddress["extension_attributes"]["w3w_coordinates"] =
              nearestPlace;
            quoteAddress["custom_attributes"]["w3w_coordinates"] = nearestPlace;
            $("input[name*=w3w_nearest]").val(nearestPlace);
            checkoutData.shippingAddressFromData.custom_attributes.w3w_nearest =
              nearestPlace;
          }
        }
      });
    },
  };

  return Abstract.extend();
});
