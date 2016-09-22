<?php

require_once "../vendor/autoload.php";

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
