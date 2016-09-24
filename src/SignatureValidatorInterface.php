<?php

namespace TrustPay;

interface SignatureValidatorInterface
{
    /**
     * @param $_ (like as ...$args in php5.6+)
     *
     * @return string
     */
    public function createMessage($_);

    /**
     * @param string $signature
     * @param string $message
     *
     * @return bool
     */
    public function isValid($signature, $message);

    /**
     * @param $message
     *
     * @return string
     */
    public function computeSignature($message);
}