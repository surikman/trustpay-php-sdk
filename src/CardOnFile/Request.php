<?php

namespace TrustPay\CardOnFile;

use TrustPay\HttpClient\Client;
use TrustPay\RequestAwareTrait;
use TrustPay\RequestInterface;
use TrustPay\Response;
use TrustPay\SignatureValidator;

class Request implements RequestInterface
{
    use RequestAwareTrait;

    /** @var Client */
    private $httpClient;

    /** @var string */
    private $storedCardToken;

    /**
     * Request constructor.
     *
     * @param $accountId
     * @param $secret
     * @param $endpoint
     */
    public function __construct($accountId, $secret, $endpoint)
    {
        $this->setAccountId($accountId);
        $this->setSignatureValidator(new SignatureValidator($secret));
        $this->setEndpoint($endpoint);
        $this->httpClient = new Client($endpoint);
    }

    /**
     * @param $storedCardToken
     *
     * @return Response
     */
    public function payment($storedCardToken)
    {
        $this->storedCardToken = $storedCardToken;

        $response = $this->httpClient->get($this->getUrl());

        return $this->parseBackgroundResponse($response);
    }

    /**
     * @return string
     */
    protected function buildQuery()
    {
        $queryData = $this->getDefaultQueryData();

        $queryData = array_merge($queryData, $this->getCardOnFileQueryData());

        return http_build_query($queryData);
    }

    /**
     * @param $result
     *
     * @return Response
     */
    private function getResponse($result)
    {

    }

    /**
     * @return array
     */
    private function getCardOnFileQueryData()
    {
        $CTY = 2; // Card transaction type For card-on-file transaction set to 2

        $card = (new Serializer())->deserialize($this->storedCardToken);

        $message = $this->signatureValidator->createMessage(
            $this->accountId,
            $this->amount,
            $this->currency,
            $this->reference,
            $CTY,
            $card['CardID'],
            $card['CardExp']
        );

        $queryData = [
            'CTY'     => $CTY,
            'CardId'  => $card['CardID'],
            'CardExp' => $card['CardExp'],
            'SIG2'    => $this->signatureValidator->computeSignature($message),
        ];

        $queryData = array_filter($queryData, function ($value) {
            return $value !== null;
        });

        return $queryData;
    }
}