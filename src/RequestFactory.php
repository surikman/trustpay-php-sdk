<?php

namespace TrustPay;

use TrustPay\Payment;
use TrustPay\CardOnFile;
use TrustPay\Refund;

class RequestFactory
{
    /**
     * @param Configuration $configuration
     * @param               $amount
     * @param               $reference
     * @param null          $email
     * @param null          $description
     * @param null          $currency
     *
     * @return Payment\Request
     */
    public function createPayment(
        Configuration $configuration,
        $amount,
        $reference,
        $email = null,
        $description = null,
        $currency = null
    ) {

        return $this->createByClass(
            Payment\Request::class,
            $configuration,
            $amount,
            $reference,
            $email,
            $description,
            $currency
        );
    }

    /**
     * @param Configuration $configuration
     * @param               $amount
     * @param               $reference
     * @param null          $email
     * @param null          $description
     * @param null          $currency
     *
     * @return CardOnFile\Request
     */
    public function createCardOnFile(
        Configuration $configuration,
        $amount,
        $reference,
        $email = null,
        $description = null,
        $currency = null
    ) {

        return $this->createByClass(
            CardOnFile\Request::class,
            $configuration,
            $amount,
            $reference,
            $email,
            $description,
            $currency
        );
    }

    /**
     * @param Configuration $configuration
     * @param               $amount
     * @param               $reference
     * @param null          $currency
     *
     * @return Refund\Request
     */
    public function createRefund(
        Configuration $configuration,
        $amount,
        $reference,
        $currency = null
    ) {
        return $this->createByClass(
            Refund\Request::class,
            $configuration,
            $amount,
            $reference,
            null,
            null,
            $currency
        );
    }

    /**
     * @param               $className
     * @param Configuration $configuration
     * @param               $amount
     * @param               $reference
     * @param null          $email
     * @param null          $description
     * @param null          $currency
     *
     * @return mixed
     */
    private function createByClass(
        $className,
        Configuration $configuration,
        $amount,
        $reference,
        $email = null,
        $description = null,
        $currency = null
    ) {
        $request = new $className(
            $configuration->getAccountId(),
            $configuration->getSecret(),
            $configuration->getEndpoint()
        );

        $this->injectRequestAttributes($request, $configuration, $amount, $reference, $email, $description, $currency);

        return $request;
    }

    /**
     * @param RequestInterface $request
     * @param Configuration    $configuration
     * @param                  $amount
     * @param                  $reference
     * @param                  $email
     * @param                  $description
     * @param                  $currency
     *
     * @return void
     */
    private function injectRequestAttributes(
        RequestInterface $request,
        Configuration $configuration,
        $amount,
        $reference,
        $email,
        $description,
        $currency
    ) {
        $request->setAmount($amount);
        $request->setReference($reference);
        $request->setCustomerEmail($email);
        $request->setDescription($description);

        $request->setCurrency($currency ?: $configuration->getCurrency());
        $request->setLanguage($configuration->getLanguage());
        $request->setCancelUrl($configuration->getCancelUrl());
        $request->setSuccessUrl($configuration->getSuccessUrl());
        $request->setNotificationUrl($configuration->getNotificationUrl());
        $request->setErrorUrl($configuration->getErrorUrl());
    }
}
