<?php

namespace TrustPay\Payment;

use TrustPay\RequestAwareTrait;
use TrustPay\RequestInterface;
use TrustPay\SignatureValidator;

class Request implements RequestInterface
{
    use RequestAwareTrait;

    /** @var bool */
    protected $authorizedStoreCard = false;

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
    }

    /**
     * @return boolean
     */
    public function isAuthorizedStoreCard()
    {
        return $this->authorizedStoreCard;
    }

    /**
     * @param boolean $authorizedStoreCard
     */
    public function setAuthorizedStoreCard($authorizedStoreCard)
    {
        $this->authorizedStoreCard = $authorizedStoreCard;
    }

    /**
     * @return string
     */
    protected function buildQuery()
    {
        $queryData = $this->getDefaultQueryData();

        if ($this->isAuthorizedStoreCard()) {
            $queryData = array_merge($queryData, [
                'CTY' => 3 // Card transaction type - For initial transaction set to 3
            ]);
        }

        return http_build_query($queryData);
    }
}
