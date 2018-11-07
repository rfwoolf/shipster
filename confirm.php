##Confirm endpoint_URL:
$Confirm_Endpoint_URL = "https://digitalapi-stest.npe.auspost.com.au/delivery-club/v2/confirm/";

##Use this API key for Sandbox Test:
$API_key = "5b9fd68d0bcc404387327c0e6e110194";

## The /confirm endpoint requires a JSON (application/json) formatted body

##Example JSON:
{
	"email": "0abab3f1b4d142c395f88c2054f535ae0ccf36e5",  
	"orderReference": "ABC1234",
	"orderTotal": 20.00,
	"shippingFee": 6.99,
	"shippingFeePaid": 0.00,
	"shippingMethod": "Standard",
	"deliveryAddress": {
		"firstName": "John",
		"lastName": "Smith",
		"line1": "9 Brighton st",
		"suburb": "Richmond",
		"state": "VIC",
		"postcode": "3121",
		"country": "AU"
	}
}

##Successful response always return a HTTP status code of 200 as a response message
##Example Response:
{
    "submitted": true,
    "transactionId": "44edc4d4defb25eb8e064fc936163cf880e9c945"
}

##Example Request Header    
## GET /api/v2/confirm/ HTTP/1.1
## Content-Type: application/json
## AUTH-KEY: 5b9fd68d0bcc404387327c0e6e110194

##Example curl request:
## curl -i -H "Accept: application/json" -H "Content-Type: application/json" -H "AUTH-KEY: 5b9fd68d0bcc404387327c0e6e110194" -X GET https://digitalapi-stest.npe.auspost.com.au/delivery-club/v2/verify/0abab3f1b4d142c395f88c2054f535ae0ccf36e5
