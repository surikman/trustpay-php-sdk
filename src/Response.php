<?php

namespace TrustPay;

class Response
{
    /** @var string */
    protected $reference;

    /** @var string */
    protected $result;

    /** @var string */
    protected $processingId;

    protected $messages = [
        0    => 'Payment was successfully processed.',
        1    => 'Payment is pending (offline payment)',
        5    => 'AuthorizedOnly – reserved for future use',
        1001 => 'Data sent is not properly formatted',
        1002 => 'Account with specified ID was not found.',
        1003 => 'Merchant account disabled',
        1004 => 'The message is not signed correctly',
        1005 => 'Customer has cancelled the payment',
        1006 => 'Request was not properly authenticated',
        1007 => 'Requested transaction amount is greater than disposable balance',
        1008 => 'Service cannot be used or permission to use given service has not been granted. Please contact TrustPay for more information.',
        1010 => 'Transaction with specified ID was not found',
        1011 => 'The requested action is not supported for the transaction',
        1100 => 'Internal error has occurred',
        1101 => 'Currency conversion for requested currencies is not supported',
    ];

    /**
     * Response constructor.
     *
     * @param array $data
     *
     * @throws Exceptions\InvalidResponse
     */
    public function __construct(array $data)
    {
        if (!isset($data['REF']) || !isset($data['RES'])) {
            throw new Exceptions\InvalidResponse;
        }

        $this->reference = $data['REF'];
        $this->result = $data['RES'];
        $this->processingId = isset($data['PID']) ? $data['PID'] : null;
    }

    /**
     * @return mixed|null
     */
    public function getMessage()
    {
        return $this->codeToMessage($this->result, 'Unknown status.');
    }

    /**
     * @return bool|mixed|null
     */
    public function getError()
    {
        $successCodes = [ 0, 1, 5 ];

        if (in_array($this->result, $successCodes)) {
            return false;
        }

        return $this->codeToMessage($this->result, 'Unknown error.');
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return mixed|null
     */
    public function getProcessingId()
    {
        return $this->processingId;
    }

    /**
     * @param      $code
     * @param null $default
     *
     * @return mixed|null
     */
    protected function codeToMessage($code, $default = null)
    {
        return isset($this->messages[$code]) ? $this->messages[$code] : $default;
    }
}
