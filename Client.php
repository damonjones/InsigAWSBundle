<?php

namespace Insig\AWSBundle;

use Insig\AWSBundle\Exception as AWSException;

/**
 * Client
 *
 * @author Damon Jones
 */

class Client
{
    /**
     * Enums for country codes
     */
    const LOCALE_UK = 'UK';
    const LOCALE_US = 'US';

    /**
     * Valid Country Codes
     */
    private static $countryCodes = array(
        self::LOCALE_UK,
        self::LOCALE_US,
    );

    /**
     * URLs for each country code
     */
    private static $domains = array(
        self::LOCALE_UK => 'ecs.amazonaws.co.uk',
        self::LOCALE_US => 'ecs.amazonaws.com',
    );

    /**
     * Service
     */
    const SERVICE   = 'AWSECommerceService';

    /**
     * Version
     */
    const VERSION   = '2010-11-01';

    /**
     * AWS Secret Access Key
     *
     * @var string
     */
    private $secretAccessKey;

    /**
     * AWS Access Key ID
     *
     * @var string
     */
    private $accessKeyId;

    /**
     * Country Code
     *
     * @var string
     */
    private $countryCode;

    /**
     * @param   string      $secretAccessKey    The secret access key for the AWS account
     * @param   string      $accessKeyId        The access key id for the AWS account
     * @throws  \Insig\AWSBundle\Exception      If either access key parameter is not set
     */
    public function __construct($secretAccessKey = null, $accessKeyId = null)
    {
        if (!$secretAccessKey || !$accessKeyId) {
            throw new AWSException('Secret/Access keys are required.');
        }

        $this->secretAccessKey = $secretAccessKey;
        $this->accessKeyId     = $accessKeyId;
        $this->countryCode     = self::LOCALE_US;
    }

    /**
     * Sets the country code
     *
     * @param   string      $countryCode    The country code
     * @return  Client      this
     * @throws  \Insig\AWSBundle\Exception  If the country code is invalid
     */
    public function setCountryCode($countryCode = null)
    {
        if (!$countryCode || !in_array($countryCode, self::$countryCodes)) {
            throw new AWSException(sprintf('Country code [%s] is invalid.', $countryCode));
        }

        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Gets the country code
     *
     * @return  string                      The country code
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Creates and returns a signed request url from the Request
     *
     * @param   array     $requestQueryParameters   The request query parameters to use
     * @return  string                              The signed request url
     * @throws  \Insig\AWSBundle\Exception          If the request query parameters array is empty
     * @coverage
     */
    protected function getSignedRequestURL(array $requestQueryParameters = array())
    {
        if (empty($requestQueryParameters)) {
            throw new AWSException('Request query parameters are required.');
        }

        $defaultQueryParameters = array(
            'AWSAccessKeyId'  =>  $this->accessKeyId,
            'Service'         =>  self::SERVICE,
            'Timestamp'       =>  gmdate('Y-m-d\TH:i:s\Z'),
            'Version'         =>  self::VERSION
        );
        $queryParameters = array_merge($defaultQueryParameters, $requestQueryParameters);
        ksort($queryParameters);

        $arr = array();
        foreach ($queryParameters as $key => $value) {
            $key        = str_replace('%7E', '~', rawurlencode($key));
            $value      = str_replace('%7E', '~', rawurlencode($value));
            $kvpairs[]  = $key . '=' . $value;
        }
        $queryString = implode('&', $kvpairs);
        $url = self::$domains[$this->countryCode];
        $requestString = sprintf("GET\n%s\n/onca/xml\n%s", $url, $queryString);
        $signature = str_replace('%7E', '~', rawurlencode(base64_encode(hash_hmac('sha256', $requestString, $this->secretAccessKey, true))));
        $signedRequestURL = sprintf('http://%s/onca/xml?%s&Signature=%s', $url, $queryString, $signature);

        return $signedRequestURL;
    }

    /**
     * execute
     *
     * @param   Request     $request        The Request to execute
     * @return  Response                    The Response
     * @throws  \Insig\AWSBundle\Exception  If the remote request call fails
     */
    public function execute(Request $request)
    {
        $signedRequestURL = $this->getSignedRequestURL($request->getParameters());

        try {
            $data = file_get_contents($signedRequestURL);
            if (false === $data) {
                // @codeCoverageIgnoreStart
                throw new AWSException('Data could not be loaded from URL.');
                // @codeCoverageIgnoreEnd
            }
        } catch (\ErrorException $e) {
            throw new AWSException('Data could not be loaded from URL.');
        }

        return new Response($data);
    }
}
