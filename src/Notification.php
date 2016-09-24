<?php

namespace TrustPay;

use TrustPay\CardOnFile;

class Notification
{
    protected $accountId;
    protected $type;
    protected $amount;
    protected $currency;
    protected $reference;
    protected $result;
    protected $transactionId;
    protected $orderId;
    protected $signed;
    protected $signature;
    protected $cardPayment = false;
    protected $cardId;
    protected $cardMask;
    protected $cardExpiration;
    protected $cardAuthorizationNumber;
    protected $cardAcquirerResponseId;
    protected $cardRecTxSec;
    protected $cardSignature;
    protected $messages = [
        0 => 'Payment was successfully processed.',
        1 => 'Payment is pending (offline payment)',
        2 => 'TrustPay has been notified that the client placed a payment order or has made payment, but further confirmation from 3rd party is needed. Another notification (with result code 0 - success) will be sent when TrustPay receives and processes payment from 3rd party.',
        3 => 'Payment was successfully authorized. Another notification (with result code 0 - success) will be sent when TrustPay receives and processes payment from 3rd party.',
        4 => 'TrustPay has received the payment, but it must be internally processed before it is settled on the merchant‘s account. When the payment is successfully processed, another notification (with the result code 0 – success) will be sent.',
        5 => 'AuthorizedOnly – reserved for future use',
    ];

    /** @var SignatureValidator */
    private $signatureValidator;

    /** @var array */
    private $rawData;

    public function __construct(array $data, $secret)
    {
        $this->rawData = $data;
        $this->signatureValidator = new SignatureValidator($secret);

        $requiredFields = [ 'AID', 'TYP', 'AMT', 'CUR', 'REF', 'RES', 'TID', 'OID', 'TSS', 'SIG' ];

        foreach ($requiredFields as $required) {
            if (!isset($data[$required])) {
                throw new Exceptions\InvalidNotification;
            }
        }

        $this->accountId = $data['AID'];
        $this->type = $data['TYP'];
        $this->amount = $data['AMT'];
        $this->currency = $data['CUR'];
        $this->reference = $data['REF'];
        $this->result = $data['RES'];
        $this->transactionId = $data['TID'];
        $this->orderId = $data['OID'];
        $this->signed = $data['TSS'];
        $this->signature = $data['SIG'];

        if (!$this->verifySignature()) {
            throw new Exceptions\InvalidNotificationSignature;
        }

        if (array_key_exists('CardID', $data)) {
            $requiredFields = [ 'CardID', 'CardMask', 'CardExp', 'AuthNumber', 'CardRecTxSec', 'AcqResId', 'SIG2' ];

            foreach ($requiredFields as $required) {
                if (!array_key_exists($required, $data)) {
                    throw new Exceptions\InvalidNotification;
                }
            }

            $this->cardPayment = true;
            $this->cardId = $data['CardID'];
            $this->cardMask = $data['CardMask'];
            $this->cardExpiration = $data['CardExp'];
            $this->cardAuthorizationNumber = $data['AuthNumber'];
            $this->cardAcquirerResponseId = $data['AcqResId'];
            $this->cardRecTxSec = $data['CardRecTxSec'];
            $this->cardSignature = $data['SIG2'];

            if (!$this->verifyCardSignature()) {
                throw new Exceptions\InvalidNotificationSignature;
            }
        }
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return in_array($this->result, [ 0, 3, 4 ]);
    }

    /**
     * @return bool
     */
    public function isProcessing()
    {
        return in_array($this->result, [ 1, 2 ]);
    }

    /**
     * @return bool
     */
    public function isOnlyAuthorized()
    {
        return in_array($this->result, [ 5 ]);
    }

    /**
     * @return bool
     */
    public function isCardPayment()
    {
        return $this->cardPayment;
    }

    /**
     * @return string
     */
    public function getCardToken()
    {
        return (new CardOnFile\Serializer())->serialize($this->rawData);
    }

    /**
     * @return mixed|null
     */
    public function getMessage()
    {
        return $this->codeToMessage($this->result, 'Unknown status.');
    }

    /**
     * @return mixed
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
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
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return mixed
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * @return mixed
     */
    public function getCardMask()
    {
        return $this->cardMask;
    }

    /**
     * @return mixed
     */
    public function getCardExpiration()
    {
        return $this->cardExpiration;
    }

    /**
     * @return mixed
     */
    public function getCardAuthorizationNumber()
    {
        return $this->cardAuthorizationNumber;
    }

    /**
     * @return mixed
     */
    public function getCardAcquirerResponseId()
    {
        return $this->cardAcquirerResponseId;
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

    /**
     * @return bool
     */
    protected function verifySignature()
    {
        $message = $this->signatureValidator->createMessage(
            $this->accountId,
            $this->type,
            $this->amount,
            $this->currency,
            $this->reference,
            $this->result,
            $this->transactionId,
            $this->orderId,
            $this->signed
        );

        return $this->signatureValidator->isValid($this->signature, $message);
    }

    /**
     * @return bool
     */
    protected function verifyCardSignature()
    {
        $message = $this->signatureValidator->createMessage(
            $this->accountId,
            $this->type,
            $this->amount,
            $this->currency,
            $this->reference,
            $this->result,
            $this->transactionId,
            $this->orderId,
            $this->signed,
            $this->cardId,
            $this->cardMask,
            $this->cardExpiration,
            $this->cardAuthorizationNumber,
            $this->cardRecTxSec,
            $this->cardAcquirerResponseId
        );

        return $this->signatureValidator->isValid($this->cardSignature, $message);
    }
}
