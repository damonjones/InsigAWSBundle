<?php

namespace Insig\AWSBundle;

use Insig\AWSBundle\Exception as AWSException;

/**
 * Response
 *
 * @author Damon Jones
 */
class Response
{
    /**
     * Xml
     *
     * @var SimpleXMLElement
     */
    protected $xml;

    /**
     * @param   string      $response       The XML data
     * @throws  \Insig\AWSBundle\Exception  If the data cannot be parsed by SimpleXML
     */
    public function __construct($response = null)
    {
        $response = trim($response);

        if (!$response) {
            throw new AWSException('Response is empty.');
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        if (count($errors)) {
            throw new AWSException('Error parsing XML from response.');
        }

        // Check for an error and throw an exception
        if (isset($xml->ItemLookupErrorResponse)) {
            throw new AWSException(
                (string) $xml->ItemLookupErrorResponse->Message,
                (int) $xml->ItemLookupErrorResponse->Code
            );
        }

        if (isset($xml->Items->Request->Errors)) {
            throw new AWSException(
                (string) $xml->Items->Request->Errors->Error->Message,
                (int) $xml->Items->Request->Errors->Error->Code
            );
        }

        $this->xml = $xml;
    }

    /**
     * @todo
     * Returns an associative array representation of the xml
     *
     * @return  array
     */
    public function asArray()
    {
        return array('root' => array());
    }

    /**
     * @todo
     * Returns a JSON-encoded array representation of the xml
     *
     * @return  string
     */
    public function asJson()
    {
        return json_encode($this->asArray());
    }

    /**
     * Returns the SimpleXMLObject
     *
     * @return  SimpleXMLObject|null
     */
    public function asXml()
    {
        return $this->xml;
    }

    /**
     * Returns a string representation of the xml
     *
     * @return  string|null
     */
    public function asString()
    {
        return (null !== $this->xml) ? $this->xml->asXML() : null;
    }

    public function __toString()
    {
        return $this->asString();
    }
}