<?php

namespace TrustPay;

interface RequestInterface
{
    /**
     * @param string $amount
     */
    public function setAmount($amount);

    /**
     * @param string $reference
     */
    public function setReference($reference);

    /**
     * @param string $currency
     */
    public function setCurrency($currency);

    /**
     * @param string $language
     */
    public function setLanguage($language);

    /**
     * @param string $country
     */
    public function setCountry($country);

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @param string $customerEmail
     */
    public function setCustomerEmail($customerEmail);

    /**
     * @param string $returnUrl
     */
    public function setReturnUrl($returnUrl);

    /**
     * @param string $successUrl
     */
    public function setSuccessUrl($successUrl);

    /**
     * @param string $cancelUrl
     */
    public function setCancelUrl($cancelUrl);

    /**
     * @param string $errorUrl
     */
    public function setErrorUrl($errorUrl);

    /**
     * @param string $notificationUrl
     */
    public function setNotificationUrl($notificationUrl);
}