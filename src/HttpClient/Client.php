<?php
namespace TrustPay\HttpClient;

use GuzzleHttp\Exception\ClientException;

class Client
{
    /** @var string */
    private $endpoint;

    /** @var \GuzzleHttp\Client */
    private $httpClient;

    /**
     * Client constructor.
     *
     * @param string $endpoint
     */
    public function __construct($endpoint)
    {
        $this->endpoint = $endpoint;
    }


    /**
     * @param $uri
     *
     * @return array
     */
    public function get($uri)
    {
        try {
            $response = $this->getHttpClient()->get($uri)->getBody();
        } catch (ClientException $e) {
            $response = $e;
        }

        return GuzzleResponseParser::parse($response);
    }

    /**
     * @return \GuzzleHttp\Client
     */
    private function getHttpClient()
    {
        if (null === $this->httpClient) {
            $this->httpClient = new \GuzzleHttp\Client([
                'base_uri' => $this->endpoint,
            ]);
        }

        return $this->httpClient;
    }
}
