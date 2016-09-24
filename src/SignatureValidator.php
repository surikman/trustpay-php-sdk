<?php

namespace TrustPay;

class SignatureValidator implements SignatureValidatorInterface
{
    /** @var string */
    private $secret;

    /**
     * Signature constructor.
     *
     * @param string $secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @param $_ (like as ...$args in php5.6+)
     *
     * @return string
     */
    public function createMessage($_)
    {
        $args = func_get_args();
        $message = '';
        foreach ($args as $arg) {
            $message .= $arg;
        }

        return $message;
    }

    /**
     * @param string $signature
     * @param string $message
     *
     * @return bool
     */
    public function isValid($signature, $message)
    {
        return $signature === $this->computeSignature($message);
    }


    /**
     * @param $message
     *
     * @return string
     */
    public function computeSignature($message)
    {
        return strtoupper(hash_hmac('sha256', pack('A*', $message), pack('A*', $this->secret)));
    }
}