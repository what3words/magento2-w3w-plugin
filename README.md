# what3words Magento 2 Extension
v.1.0.1 Updated 01/07/19

## Configuration
* Found under 'what3words' in Store Configuration
* Enable and enter your what3words API key - http://accounts.what3words.com/register
* Choose which countries to display the field to
* Choose your display settings

## Frontend usage
* Displays in customer address book and with autosuggest plugin while editing a customer address
* Displays with autosuggest plugin in shipping form in checkout
* Validates input based on country, regex match and existence in the what3words database
* Shows against orders in customer account area

## Admin usage
* Displays against orders
* Displays against possible shipments
* Prints onto shipment labels
* Can be added to a customer address by an admin user without autosuggest plugin
* Despite the lack of plugin, the above will still run through the same validation as the frontend

## Technical and data storage
* Stores to 3 tables throughout a w3w lifecycle - quote, order and customer.
* Saves to w3w_sales_quote from shipping address form via a JS mixin and AJAX call
* Saves to w3w_sales_order from order placement via an Observer
* Saves to w3w_customer_address from the above two, from the customer account area and from the admin
* A customer address attribute is required to edit this value from the admin; this is populated with a plugin in the above areas.

## Implementation
* Field is added to checkout with a LayoutProcessorPlugin linked to a JS component, `shipping.js`.
* Field is added to customer form using an HTML script tag, appended with jQuery. This is linked to a JS component, `customer/edit.js`. 
* Custom jQuery validation system is used to handle AJAX results.
* The `SaveToVarchar` plugin handles saving the address attribute.
* The `AddressRendererPlugin` handles displaying the saved 3 word address against addresses on orders.
