<?php

require_once "../vendor/autoload.php";

$configuration = new \TrustPay\Configuration();
$configuration->setSecret("abcd1234");
$trustPay = new \TrustPay\TrustPay($configuration);


$data = $_GET;


// for testing
$url = "AID=9876543210&TYP=CRDT&AMT=123.45&CUR=EUR&REF=1234567890&RES=3&TID=3213123123&OID=0&TSS=Y&CardID=12345654&CardMask=1234******3212&CardExp=0999&AuthNumber=5411612&AcqResId=&CardRecTxSec=&SIG=CBAA57C482332A924A58F69B29973A28EC6E26B9A39FADAB344D3E4A4EDEAF58&SIG2=B03918E76C8E8D27F6EFAE375502E8790B7A1F55CBF2F4DA28AC5707730E683F";
parse_str($url, $data);


// or paste secret as second parameter to parseNotification if you not provided configuration with secret
$response = $trustPay->parseNotification($data);


printf("IS PAID: %s\n", var_export($response->isPaid(), true));
printf("getAmount: %s\n", $response->getAmount());
printf("isCardPayment: %s\n", var_export($response->isCardPayment(), true));
printf("isProcessing: %s\n", var_export($response->isProcessing(), true));
printf("isOnlyAuthorized: %s\n", var_export($response->isOnlyAuthorized(), true));
printf("getMessage: %s\n", $response->getMessage());
printf("getReference: %s\n", $response->getReference());
printf("getResult: %s\n", $response->getResult());
printf("getCardAcquirerResponseId: %s\n", $response->getCardAcquirerResponseId());
printf("getCardAuthorizationNumber: %s\n", $response->getCardAuthorizationNumber());
printf("getCardMask: %s\n", $response->getCardMask());
printf("getCardId: %s\n", $response->getCardId());
printf("getCardExpiration: %s\n", $response->getCardExpiration());
printf("getCardToken: %s\n", $response->getCardToken());
printf("getCardToken (composed from): %s\n", print_r((new \TrustPay\CardOnFile\Serializer())->deserialize($response->getCardToken()), true));
