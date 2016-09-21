<?php

namespace TrustPay;

class Request
{
    protected $accountId;
    protected $key;
    protected $baseUrl;

    protected $amount;
    protected $reference;
    protected $currency;

    protected $language;
    protected $country;

    protected $description;
    protected $customerEmail;

    protected $returnUrl;
    protected $successUrl;
    protected $cancelUrl;
    protected $errorUrl;
    protected $notificationUrl;

    /**
     * Request constructor.
     *
     * @param $accountId
     * @param $key
     * @param $baseUrl
     */
    public function __construct($accountId, $key, $baseUrl)
    {
        $this->accountId = $accountId;
        $this->key = $key;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @throws Exceptions\RequestMissingRequiredField
     * @return string
     */
    public function getUrl()
    {
        foreach ([ 'amount', 'reference', 'currency' ] as $required) {
            if ($this->$required === null) {
                throw new Exceptions\RequestMissingRequiredField("The {$required} field is required.");
            }
        }

        return $this->baseUrl . '?' . $this->buildQuery();
    }

    /**
     * @param $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param $reference
     *
     * @return $this
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @param $currency
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @param mixed $language
     *
     * @return Request
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @param mixed $country
     *
     * @return Request
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @param mixed $description
     *
     * @return Request
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param mixed $customerEmail
     *
     * @return Request
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    /**
     * @param mixed $returnUrl
     *
     * @return Request
     */
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }

    /**
     * @param mixed $successUrl
     *
     * @return Request
     */
    public function setSuccessUrl($successUrl)
    {
        $this->successUrl = $successUrl;

        return $this;
    }

    /**
     * @param mixed $cancelUrl
     *
     * @return Request
     */
    public function setCancelUrl($cancelUrl)
    {
        $this->cancelUrl = $cancelUrl;

        return $this;
    }

    /**
     * @param mixed $errorUrl
     *
     * @return Request
     */
    public function setErrorUrl($errorUrl)
    {
        $this->errorUrl = $errorUrl;

        return $this;
    }

    /**
     * @param mixed $notificationUrl
     *
     * @return Request
     */
    public function setNotificationUrl($notificationUrl)
    {
        $this->notificationUrl = $notificationUrl;

        return $this;
    }


    /**
     * @return string
     */
    protected function buildQuery()
    {
        $queryData = [
            'AID'  => $this->accountId,
            'AMT'  => $this->amount,
            'CUR'  => $this->currency,
            'REF'  => $this->reference,
            'URL'  => $this->returnUrl,
            'RURL' => $this->successUrl,
            'CURL' => $this->cancelUrl,
            'EURL' => $this->errorUrl,
            'NURL' => $this->notificationUrl,
            'SIG'  => $this->computeSignature(),
            'LNG'  => $this->language,
            'CNT'  => $this->country,
            'DSC'  => $this->description,
            'EMA'  => $this->customerEmail,
        ];

        $queryData = array_filter($queryData, function ($value) {
            return $value !== null;
        });

        return http_build_query($queryData);
    }

    /**
     * @return string
     */
    protected function computeSignature()
    {
        $message = $this->accountId . $this->amount . $this->currency . $this->reference;

        return strtoupper(hash_hmac('sha256', pack('A*', $message), pack('A*', $this->key)));
    }
}
