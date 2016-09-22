# trustpay-php-sdk
Easy unofficial trustpay PHP SDK

Technical documentation - https://www.trustpay.eu/documents/technical/ 

## Install

```bash
composer require surikman/trustpay-php-sdk
```

## Usage

### Payment

```php

$configuration = new \TrustPay\Configuration();
$configuration->setSuccessUrl("https://example.com/success");
$configuration->setErrorUrl("https://example.com/error");
$configuration->setCancelUrl("https://example.com/cancel");
$configuration->setNotificationUrl("https://example.com/notification");

$configuration->setAccountId("987654321");
$configuration->setSecret("abcd1234");

$configuration->setCurrency(\TrustPay\Currency::_EUR_);
$configuration->setLanguage(\TrustPay\Language::SK);

$trustPay = new \TrustPay\TrustPay($configuration);

$request = $trustPay->payment(
    0.01,
    "reference/Variable Symbol",
    "email.address-of-customer@example.com",
    "Payment description (Order ID 1234) etc... "
);

echo $request->getUrl(); // redirect to this url

```

### Returns Url

```php
$trustPay = new \TrustPay\TrustPay();

$data = $_GET;

// for testing
$data = [
    'RES' => 0,
    'REF' => 999666333,
];

$response = $trustPay->parsePayment($data);
var_dump($response->getError());
var_dump($response->getMessage());
var_dump($response->getProcessingId());
var_dump($response->getReference());
var_dump($response->getResult());
```

### Notification URL
```php

$configuration = new \TrustPay\Configuration();
$configuration->setSecret("abcd1234");
$trustPay = new \TrustPay\TrustPay($configuration);


$data = $_GET;


// for testing
$url = "AID=9876543210&TYP=CRDT&AMT=123.45&CUR=EUR&REF=1234567890&RES=3&TID=3213123123&OID=0&TSS=Y&CardID=&CardMask=1234******3212&CardExp=0999&AuthNumber=5411612&AcqResId=&CardRecTxSec=&SIG=CBAA57C482332A924A58F69B29973A28EC6E26B9A39FADAB344D3E4A4EDEAF58&SIG2=B6FFC3B9C428CC1CD23FB51A78A290C8A902EE4A98C18D8451070AEC8F702B26";
parse_str($url, $data);


$response = $trustPay->parseNotification($data);

// or paste secret as second parameter to parseNotification if you not provided configuration with secret
// $trustPay = new \TrustPay\TrustPay();
// $response = $trustPay->parseNotification($data, 'abcd1234');


var_dump($response->isPaid());
var_dump($response->isCardPayment());
var_dump($response->getMessage());
var_dump($response->isProcessing());
var_dump($response->isOnlyAuthorized());
var_dump($response->getReference());
var_dump($response->getResult());
var_dump($response->getCardId());
var_dump($response->getCardNumber());
var_dump($response->getCardAcquirerResponseId());
var_dump($response->getCardAuthorizationNumber());
var_dump($response->getCardExpiration());
var_dump($response->getAmount());

```