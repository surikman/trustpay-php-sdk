<?php
/**
 * @author  SuRiKmAn <surikman@surikman.sk>
 * @link    API documentation https://www.trustpay.eu/wp-content/uploads/2016/03/Merchant-API-integration-2.16.pdf
 * @created 2016-09-23
 */

namespace TrustPay;

use TrustPay\Exceptions\InvalidInputArguments;
use TrustPay\Payment;
use TrustPay\Refund;

class TrustPay
{
    /** @var Configuration */
    private $configuration;

    /** @var RequestFactory */
    private $requestFactory;

    /**
     * TrustPay constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration = null)
    {
        $this->configuration = $configuration;
        $this->requestFactory = new RequestFactory();
    }

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * If you use only one payment type each configuration, use just payment() method,
     * but you are able to use @see paymentWithBank, paymentWithCard to exactly setup endpoint
     *
     * @param float       $amount      exactly 2 decimal places - Decimal(13, 2)
     *
     * @param string      $reference   Char(500) (except “<”, “>”)
     *                                 Reference allowed format is Alphanumeric(19) - only first 19 characters of the
     *                                 REF parameter are stored by TrustPay for later support inquiries.
     *
     * @param string|null $currency    Char(3)
     *
     * @param string|null $email       Varchar(254)
     *
     * @param string|null $description Only characters a-z, A-Z, 0-9 and space character of parameter description are
     *                                 displayed to a customer (payer) on the payment page. Other characters are
     *                                 changed
     *                                 to ‘ ‘ (space). Max description length is Char(256)
     *
     * @return Payment\Request
     */
    public function payment($amount, $reference, $email = null, $description = null, $currency = null)
    {
        return $this->createPaymentRequest($amount, $reference, $email, $description, $currency);
    }


    /**
     * @see payment() as description of input arguments
     *
     * @param      $amount
     * @param      $reference
     * @param null $email
     * @param null $description
     * @param null $currency
     *
     * @return Payment\Request
     */
    public function paymentWithCard($amount, $reference, $email = null, $description = null, $currency = null)
    {
        $this->assertConfiguration();

        $this->configuration->setPaymentType(Configuration::PAYMENT_TYPE_CARD);

        return $this->createPaymentRequest($amount, $reference, $email, $description, $currency);
    }


    /**
     * @see payment() as description of input arguments
     *
     * @param      $amount
     * @param      $reference
     * @param null $email
     * @param null $description
     * @param null $currency
     *
     * @return Payment\Request
     */
    public function paymentWithBank($amount, $reference, $email = null, $description = null, $currency = null)
    {
        $this->assertConfiguration();

        $this->configuration->setPaymentType(Configuration::PAYMENT_TYPE_BANK);

        return $this->createPaymentRequest($amount, $reference, $email, $description, $currency);
    }

    /**
     * @see payment() as description of input arguments
     *
     * @param        $amount
     * @param        $reference
     * @param null   $email
     * @param null   $description
     * @param null   $currency
     *
     * @return Payment\Request
     */
    public function paymentWithCardStoring(
        $amount,
        $reference,
        $email = null,
        $description = null,
        $currency = null
    ) {
        $this->assertConfiguration();

        $this->configuration->setPaymentType(Configuration::PAYMENT_TYPE_CARD);

        $request = $this->createPaymentRequest($amount, $reference, $email, $description, $currency);
        $request->setAuthorizedStoreCard(true);

        return $request;
    }

    /**
     * @param      $cardToken
     * @param      $amount
     * @param      $reference
     * @param null $email
     * @param null $description
     * @param null $currency
     *
     * @return Response
     * @throws InvalidInputArguments
     */
    public function paymentWithStoredCard(
        $cardToken,
        $amount,
        $reference,
        $email = null,
        $description = null,
        $currency = null
    ) {

        $this->assertConfiguration();

        $this->configuration->setPaymentType(Configuration::PAYMENT_TYPE_CARD);

        $request = $this->requestFactory->createCardOnFile(
            $this->configuration,
            $amount,
            $reference,
            $email,
            $description,
            $currency
        );

        return $request->payment($cardToken);
    }


    /**
     * @param      $transactionId
     * @param      $amount
     * @param      $reference
     * @param null $currency
     *
     * @return Response
     */
    public function refund(
        $transactionId,
        $amount,
        $reference,
        $currency = null
    ) {

        $this->assertConfiguration();

        $this->configuration->setPaymentType(Configuration::PAYMENT_TYPE_CARD);

        $request = $this->requestFactory->createRefund(
            $this->configuration,
            $amount,
            $reference,
            $currency
        );

        return $request->refund($transactionId);
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
     * @throws InvalidInputArguments
     */
    public function parseNotification(array $data, $secret = null)
    {
        if (empty($secret) && (null === $this->configuration || null === $this->configuration->getSecret())) {
            throw new InvalidInputArguments("Setup configuration first or insert secret");
        }

        $secret = $secret ?: $this->configuration->getSecret();

        return new Notification($data, $secret);
    }

    /**
     * @param $amount
     * @param $reference
     * @param $email
     * @param $description
     * @param $currency
     *
     * @return Payment\Request
     * @throws InvalidInputArguments
     */
    protected function createPaymentRequest($amount, $reference, $email, $description, $currency)
    {
        $this->assertConfiguration();

        return $this->requestFactory->createPayment(
            $this->configuration,
            $amount,
            $reference,
            $email,
            $description,
            $currency
        );
    }

    /**
     * @throws InvalidInputArguments
     * @return void
     */
    private function assertConfiguration()
    {
        if (null === $this->configuration) {
            throw new InvalidInputArguments("Setup configuration first");
        }
    }

}
