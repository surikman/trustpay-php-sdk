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
    public function __construct(Configuration $configuration = null)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * @param null $amount
     * @param null $reference
     * @param null $currency
     * @param null $email
     * @param null $description
     *
     * @return Request
     */
    public function payment($amount = null, $reference = null, $email = null, $description = null, $currency = null)
    {
        if (null === $this->configuration) {
            throw new \InvalidArgumentException("Setup configuration first");
        }

        $request = new Request(
            $this->configuration->getAccountId(),
            $this->configuration->getSecret(),
            $this->configuration->getEndpoint()
        );

        $request->setAmount($amount);
        $request->setReference($reference);
        $request->setCurrency($currency ?: $this->configuration->getCurrency());
        $request->setLanguage($this->configuration->getLanguage());
        $request->setCustomerEmail($email);
        $request->setDescription($description);

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
     * @param null  $secret secret is not required if configuration with secret was provided
     *
     * @return Notification
     */
    public function parseNotification(array $data, $secret = null)
    {
        if (empty($secret) && (null === $this->configuration || null === $this->configuration->getSecret())) {
            throw new \InvalidArgumentException("Setup configuration first or insert secret");
        }

        return new Notification($data, $this->configuration->getSecret());
    }
}
