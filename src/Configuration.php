<?php

namespace TrustPay;

use TrustPay\Exceptions\InvalidInputArguments;

class Configuration
{
    const PAYMENT_TYPE_BANK = 1;
    const PAYMENT_TYPE_CARD = 2;
    const PAYMENT_TYPE_CARD_EXTENSION = 3;

    const DEFAULT_PAYMENT_TYPE = self::PAYMENT_TYPE_BANK;

    /** @var array */
    private static $allowedPaymentTypes = [
        self::PAYMENT_TYPE_BANK,
        self::PAYMENT_TYPE_CARD,
        self::PAYMENT_TYPE_CARD_EXTENSION,
    ];

    /** @var string */
    private $cardPaymentsEndpoint = 'https://ib.trustpay.eu/mapi5/Card/Pay';

    /** @var string */
    private $bankPaymentsEndpoint = 'https://ib.trustpay.eu/mapi/pay.aspx';

    /** @var string */
    private $cardPaymentsExtensionEndpoint = 'https://ib.trustpay.eu/mapi/cardpaymentshandler.aspx';

    /** @var int */
    private $paymentType = self::DEFAULT_PAYMENT_TYPE;

    /** @var string */
    private $accountId;

    /** @var string */
    private $secret;

    /** @var string */
    private $currency;

    /** @var string */
    private $language;

    /** @var string */
    private $notificationUrl;

    /** @var string */
    private $successUrl;

    /** @var string */
    private $cancelUrl;

    /** @var string */
    private $errorUrl;

    /**
     * @param int $paymentType Insert one of constant PAYMENT_TYPE_
     *
     * @throws InvalidInputArguments
     */
    public function setPaymentType($paymentType)
    {
        if (!in_array($paymentType, static::$allowedPaymentTypes)) {
            throw new InvalidInputArguments($paymentType . " is not in allowed payment types");
        }

        $this->paymentType = $paymentType;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getErrorUrl()
    {
        return $this->errorUrl;
    }

    /**
     * @param string $errorUrl
     */
    public function setErrorUrl($errorUrl)
    {
        $this->errorUrl = $errorUrl;
    }

    /**
     * @return string
     */
    public function getNotificationUrl()
    {
        return $this->notificationUrl;
    }

    /**
     * @param string $notificationUrl
     */
    public function setNotificationUrl($notificationUrl)
    {
        $this->notificationUrl = $notificationUrl;
    }

    /**
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->successUrl;
    }

    /**
     * @param string $successUrl
     */
    public function setSuccessUrl($successUrl)
    {
        $this->successUrl = $successUrl;
    }

    /**
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    /**
     * @param string $cancelUrl
     */
    public function setCancelUrl($cancelUrl)
    {
        $this->cancelUrl = $cancelUrl;
    }

    /**
     * @return string
     */
    public function getCardPaymentsEndpoint()
    {
        return $this->cardPaymentsEndpoint;
    }

    /**
     * @param string $cardPaymentsEndpoint
     */
    public function setCardPaymentsEndpoint($cardPaymentsEndpoint)
    {
        $this->cardPaymentsEndpoint = $cardPaymentsEndpoint;
    }

    /**
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param string $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @throws InvalidInputArguments
     * @return string
     */
    public function getEndpoint()
    {
        switch ($this->paymentType) {
            case static::PAYMENT_TYPE_BANK:
                return $this->bankPaymentsEndpoint;
            case static::PAYMENT_TYPE_CARD:
                return $this->cardPaymentsEndpoint;
            case static::PAYMENT_TYPE_CARD_EXTENSION:
                return $this->cardPaymentsExtensionEndpoint;
            default:
                throw new InvalidInputArguments("Undefined paymentType");
        }
    }
}
