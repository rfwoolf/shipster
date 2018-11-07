<?php
##############################################################################
##Log the raw Rate Request -- this makes debugging much easier
##############################################################################
$input = file_get_contents('php://input');
file_put_contents("php://stderr", $input);
##Output looks something like this:
##{"rate":{"origin":{"country":"AU","postal_code":"2100","province":"NSW","city":"Brookvale","name":null,"address1":"10\/107 Old Pittwater Rd","address2":"","address3":null,"phone":"0422729639","fax":null,"email":null,"address_type":null,"company_name":"Biltong To Go"},"destination":{"country":"AU","postal_code":"3365","province":"NSW","city":"Manly","name":"richard woolf","address1":"U410B, 9-15 Central AVE","address2":"","address3":null,"phone":"0422 729 639","fax":null,"email":null,"address_type":null,"company_name":""},"items":[{"name":"Classic Traditional Biltong - \"Original\" flavour - 990g \/ Average Dryness \/ Sliced","sku":"","quantity":1,"grams":990,"price":5799,"vendor":"Biltong To Go","requires_shipping":true,"taxable":false,"fulfillment_service":"manual","properties":null,"product_id":237541373,"variant_id":539673037}],"currency":"AUD","locale":"en"}}
##############################################################################

##############################################################################
##Parse the Rate Request
##############################################################################
$rates = json_decode($input, true);
// log the array format for easier interpreting
file_put_contents("php://stderr", print_r($rates, true));
##Output looks something like this:
##(
##[rate] => Array
##(
##[origin] => Array
##(
##[country] => AU
##[postal_code] => 2100
##[province] => NSW
##[city] => Brookvale
##[name] =>
##[address1] => 10/111 Old Pittwater Rd
##[address2] =>
##[address3] =>
##[phone] => 0422123456
##[fax] =>
##[email] =>
##[address_type] =>
##[company_name] => Biltong To Go
##)
##
##[destination] => Array
##(
##[country] => AU
##[postal_code] => 2095
##[province] => NSW
##[city] => Manly
##[name] => richard woolf
##[address1] => 7 Central AVE
##[address2] =>
##[address3] =>
##[phone] => 0422 123 456
##[fax] =>
##[email] =>
##[address_type] =>
##[company_name] =>
##)
##
##[items] => Array
##(
##[0] => Array
##(
##[name] => Classic Traditional Biltong - "Original" flavour - 990g / Average Dryness / Sliced
##[sku] =>
##[quantity] => 1
##[grams] => 990
##[price] => 5799
##[vendor] => Biltong To Go
##[requires_shipping] => 1
##[taxable] =>
##[fulfillment_service] => manual
##[properties] =>
##[product_id] => 237541373
##[variant_id] => 539673037
##)
##
##)
##
##[currency] => AUD
##[locale] => en
##)
##
##)
##############################################################################

##############################################################################
##Check that the customer's basket is $25 or more
##############################################################################
//Add up the price in the [items] array]
//$basket_price = //

//If #basket_price < 25.00, return no shipping options, and exit
##############################################################################

##############################################################################
##Check that shipping is to a domestic address
##Check that destination.country = 'AU'
##############################################################################
//If destination.country <> 'AU, return no shipping options, and exit

##############################################################################

##############################################################################
##Get the customer's Abandoned Checkout (order)
##To proceed further, we will need:
## 1. the (default) shipping charge for their order. It must be < $20
## 2. the customer's email address

##As you can see in the above Rate Request:
## 1. we don't know how much shipping is
## 2. the customer (destination) email field is blank/null
## To solve this problem, we will get the customer's checkout using Shopify's Abandoned Checkouts API:
##https://help.shopify.com/en/api/reference/orders/abandoned_checkouts#index
##As an example, you can request like this:
##https://eb2cda841f518d6d4a90eb41151a1187:38f9f2678fce8381e9612b538da01cc6@biltongtogo.myshopify.com/admin/checkouts.json?updated_at_min=2018-10-29T16:15:47-04:00
##We want to fetch the latest 'Abandoned Checkout's within the past X minutes.
##Below is an example of the response.
##Of course, there may be multiple 'Abandoned Checkouts' returned.
##We will find the correct abandoned checkout by matching the Rate Request's destination (customer address) with the Abandoned Checkout's customer address
##Then we just extract the customers email address from the abandoned checkout.
##Example of Abandoned Checkouts:
/*
{
	"checkouts": [{
		"id": 5585094017082,
		"token": "8b8235e1f1d20d5f55d3ce0d15ac5953",
		"cart_token": "d57b8794da55aa86bdfb18980eb8a3b8",
		"email": "rfwoolf@gmail.com",
		"gateway": null,
		"buyer_accepts_marketing": false,
		"created_at": "2018-10-29T17:36:31+11:00",
		"updated_at": "2018-10-29T17:37:14+11:00",
		"landing_site": "\/",
		"note": null,
		"note_attributes": [],
		"referring_site": "https:\/\/www.google.com.au\/",
		"shipping_lines": [{
			"applied_discounts": [],
			"code": "Shipping \u0026 Handling",
			"price": "9.99",
			"markup": "0.00",
			"source": "shopify",
			"title": "Shipping \u0026 Handling",
			"phone": null,
			"delivery_category": null,
			"carrier_identifier": null,
			"carrier_service_id": null,
			"api_client_id": null,
			"requested_fulfillment_service_id": null,
			"tax_lines": [{
				"price": "0.91",
				"position": 1,
				"rate": 0.1,
				"title": "GST",
				"source": "Shopify",
				"zone": "country",
				"compare_at": "0.1"
			}],
			"custom_tax_lines": null,
			"validation_context": null,
			"id": "cf867fd17b18ed71f12c6f438a7b637f"
		}],
		"taxes_included": true,
		"total_weight": 990,
		"currency": "AUD",
		"completed_at": null,
		"closed_at": null,
		"user_id": null,
		"location_id": null,
		"source_identifier": null,
		"source_url": null,
		"device_id": null,
		"phone": null,
		"customer_locale": "en",
		"line_items": [{
			"applied_discounts": [],
			"key": "ebb1bed97d9e2289b7f6a26119270f86",
			"destination_location_id": null,
			"fulfillment_service": "manual",
			"gift_card": false,
			"grams": 990,
			"origin_location_id": 282560001,
			"product_id": 237541373,
			"properties": null,
			"quantity": 1,
			"requires_shipping": true,
			"sku": "",
			"tax_lines": [{
				"price": "0.00",
				"position": 1,
				"rate": 0.1,
				"title": "GST",
				"source": "Shopify",
				"zone": "country",
				"compare_at": "0.1"
			}],
			"taxable": false,
			"title": "Classic Traditional Biltong - \"Original\" flavour",
			"variant_id": 1605136065,
			"variant_title": "990g \/ Wet \/ Sliced",
			"vendor": "Biltong To Go",
			"compare_at_price": "80.00",
			"line_price": "57.99",
			"price": "57.99"
		}],
		"name": "#5585094017082",
		"source": null,
		"discount_codes": [],
		"abandoned_checkout_url": "https:\/\/biltong.com.au\/3725673\/checkouts\/8b8235e1f1d20d5f55d3ce0d15ac5953\/recover?key=df28c4b9c98a836a5add2251a67df348",
		"tax_lines": [{
			"price": "0.91",
			"rate": 0.1,
			"title": "GST"
		}],
		"source_name": "web",
		"presentment_currency": "AUD",
		"total_discounts": "0.00",
		"total_line_items_price": "57.99",
		"total_price": "67.98",
		"total_tax": "0.91",
		"subtotal_price": "57.99",
		"shipping_address": {
			"first_name": "Richard",
			"address1": "7 central ave",
			"phone": "0422 123 456",
			"city": "Manly",
			"zip": "2095",
			"province": "New South Wales",
			"country": "Australia",
			"last_name": "Woolf",
			"address2": "",
			"company": "",
			"latitude": -33.7313448,
			"longitude": 150.9391998,
			"name": "Richard Woolf",
			"country_code": "AU",
			"province_code": "NSW"
		},
		"customer": {
			"id": 55867211789,
			"email": "rfwoolf@gmail.com",
			"accepts_marketing": false,
			"created_at": "2018-10-29T22:08:47+11:00",
			"updated_at": "2018-10-29T17:36:31+11:00",
			"first_name": "Richard",
			"last_name": "Woolf",
			"orders_count": 3,
			"state": "enabled",
			"total_spent": "169.93",
			"last_order_id": 128807174157,
			"note": null,
			"verified_email": false,
			"multipass_identifier": null,
			"tax_exempt": false,
			"phone": null,
			"tags": "Accept_Marketing",
			"last_order_name": "#521794",
			"admin_graphql_api_id": "gid:\/\/shopify\/Customer\/55867211789",
			"default_address": {
				"id": 60481208333,
				"customer_id": 55867211789,
				"first_name": "Richard",
				"last_name": "Woolf",
				"company": "",
				"address1": "7 Central Ave",
				"address2": "",
				"city": "Brookvale",
				"province": "New South Wales",
				"country": "Australia",
				"zip": "2095",
				"phone": "0422 729 639",
				"name": "Richard Woolf",
				"province_code": "NSW",
				"country_code": "AU",
				"country_name": "Australia",
				"default": true
			}
		}
	}]
}
*/
##############################################################################
//Check if shipping_lines.price < $20
//If > $20, return no response, and exit

//If customer's email address NOT found:
//Return no response, and exit

//If customer's email address IS found:
//Trim the email string for any possible whitespace (beginning and end)
//Convert the email string to lowercase characters
//Hash the email with the SHA1 algorithm
//For example:
//Customer input: John.Smith@example.com
//Hash = sha1(john.smith@example.com) = fda41864fbb29233921a90289c83dfb346325313
$customer_email_hashed = //Assign customer email address.
##############################################################################
    
##############################################################################
##Verify that customer is a Shipster member
##Now that we have the customer's email address, we need to ask Shipster if the customer is a member
##All requests use JSON data (if applicable) and responses
##It is recommended to set a Content-Type: application/json header for every request you send to the API.
##Each request must contain an AUTH-KEY header with the key as a value.
	
##Use this API key for Sandbox Test:
$API_key = "5b9fd68d0bcc404387327c0e6e110194";

##Use this Sandbox  endpoint_URL for the verify method:
$verify_endpoint_URL = 'https://digitalapi-stest.npe.auspost.com.au/delivery-club/v2/verify/';

##Sandbox Test email addresses:
## appwsample1@mailinator.com (SHA-1 Hash: 0abab3f1b4d142c395f88c2054f535ae0ccf36e5)
## appwsample2@mailinator.com (SHA-1 Hash: face8a6532d302eedc2355f9038d63db92d3dd75)
## appwsample3@mailinator.com (SHA-1 Hash: 9e734370915725dd25afbd429dc51b1e3dc45d13)
//For testing purposes, you can use http://www.sha1-online.com/ to generate a SHA1 hash from a plain email.

##Example Request Header    
## GET /api/v2/verify/245f310ea00c0fbedd12b2dc33a289ec9969531d HTTP/1.1
## Content-Type: application/json
## AUTH-KEY: 5b9fd68d0bcc404387327c0e6e110194

##Example curl request:
## curl -i -H "Accept: application/json" -H "Content-Type: application/json" -H "AUTH-KEY: 5b9fd68d0bcc404387327c0e6e110194" -X GET https://digitalapi-stest.npe.auspost.com.au/delivery-club/v2/verify/0abab3f1b4d142c395f88c2054f535ae0ccf36e5

##Example response:
/*
{
"verified": true
}
*/
##############################################################################

//Verify that the customer is a Shipster member
//If verified = FALSE, return no shipping options, and exit
//If verified = TRUE, return $0.00 shipping:
// use number_format because shopify api expects the price to be "25.00" instead of just "25"

// build the array of line items using the prior values
$output = array('rates' => array(
    array(
        'service_name' => 'Shipster. Free shipping with your Shipster membership',
        'service_code' => 'Shipster',
        'total_price' => number_format(0, 2, '', ''),
        'currency' => 'AUD'//,
    )
));

// encode into a json response
$json_output = json_encode($output);

// log it so we can debug the response
file_put_contents("php://stderr", $json_output);

// send it back to shopify
print $json_output;
