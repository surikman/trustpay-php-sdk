<?php
namespace TrustPay\HttpClient;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\StreamInterface;

class GuzzleResponseParser
{
    public static function parse($response)
    {
        if ($response instanceof StreamInterface) {

            $result = (string)$response;

            $response->close();

            return [
                'isOk'     => true,
                'response' => $result,
            ];
        }

        if ($response instanceof ClientException) {

            return [
                'isOk'    => false,
                'code'    => $response->getCode(),
                'message' => $response->getResponseBodySummary($response->getResponse()),
            ];

        }

        throw new \HttpRuntimeException(__METHOD__ . " Fail to parse response");
    }
}