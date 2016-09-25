<?php

namespace TrustPay\Refund;

use TrustPay\Enums\CardTransactionType;
use TrustPay\HttpClient\Client;
use TrustPay\RequestAwareTrait;
use TrustPay\RequestInterface;
use TrustPay\SignatureValidator;

class Request implements RequestInterface
{
    use RequestAwareTrait;

    /** @var integer */
    private $transactionId;

    /** @var Client */
    private $httpClient;

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
     * @param $transactionId
     *
     * @return \TrustPay\Response
     */
    public function refund($transactionId)
    {
        $this->transactionId = $transactionId;

        $response = $this->httpClient->get($this->getUrl());

        $response = $this->parseBackgroundResponse($response);
        $response->setRequestedUrl($this->getUrl());

        return $response;
    }

    /**
     * @return mixed
     */
    protected function buildQuery()
    {
        $message = $this->signatureValidator->createMessage(
            $this->accountId,
            $this->amount,
            $this->currency,
            $this->reference,
            CardTransactionType::REFUND,
            $this->transactionId
        );

        $queryData = [
            'CTY'        => CardTransactionType::REFUND,
            'CardTranID' => $this->transactionId,
            'SIG2'       => $this->signatureValidator->computeSignature($message),
            'SIG'        => $this->createStandardSignature(),
        ];

        $queryData = array_filter($queryData, function ($value) {
            return $value !== null;
        });

        return http_build_query($queryData);
    }
}
