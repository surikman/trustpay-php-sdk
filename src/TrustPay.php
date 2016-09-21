<?php

namespace TrustPay;

class TrustPay
{
    /** @var Configuration */
    private $configuration;

    /**
     * TrustPay constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * @param null $amount
     * @param null $reference
     *
     * @return Request
     */
    public function payment($amount = null, $reference = null)
    {
        $request = new Request(
            $this->configuration->getAccountId(),
            $this->configuration->getSecret(),
            $this->configuration->getApiUrl()
        );

        $request->setAmount($amount);
        $request->setReference($reference);
        $request->setCurrency($this->configuration->getCurrency());

        $request->setCancelUrl($this->configuration->getCancelUrl());
        $request->setSuccessUrl($this->configuration->getSuccessUrl());
        $request->setNotificationUrl($this->configuration->getNotificationUrl());
        $request->setErrorUrl($this->configuration->getErrorUrl());

        return $request;
    }

    /**
     * @param array $data
     *
     * @return Response
     */
    public function parsePayment(array $data)
    {
        return new Response($data);
    }

    /**
     * @param array $data
     *
     * @return Notification
     */
    public function parseNotification(array $data)
    {
        return new Notification($data, $this->configuration->getSuccessUrl());
    }
}
