<?php

require_once "../vendor/autoload.php";

$configuration = new \TrustPay\Configuration();
$configuration->setSecret("abcd1234");
$trustPay = new \TrustPay\TrustPay($configuration);


$data = $_GET;


// for testing
$url = "AID=9876543210&TYP=CRDT&AMT=123.45&CUR=EUR&REF=1234567890&RES=3&TID=3213123123&OID=0&TSS=Y&CardID=&CardMask=1234******3212&CardExp=0999&AuthNumber=5411612&AcqResId=&CardRecTxSec=&SIG=CBAA57C482332A924A58F69B29973A28EC6E26B9A39FADAB344D3E4A4EDEAF58&SIG2=B6FFC3B9C428CC1CD23FB51A78A290C8A902EE4A98C18D8451070AEC8F702B26";
parse_str($url, $data);


// or paste secret as second parameter to parseNotification if you not provided configuration with secret
$response = $trustPay->parseNotification($data);


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
