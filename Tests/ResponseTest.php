<?php

/*
 * This file is part of the InsigAWSBundle package.
 *
 * (c) Damon Jones <damon@insig.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Insig\AWSBundle\Tests;

use Insig\AWSBundle\Response;

/**
 * Test class for Response.
 * Generated by PHPUnit on 2011-02-17 at 12:03:59.
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Response
     */
    protected $object;
    protected $xml;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->xml =<<< EOD
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <item>test</item>
</root>

EOD;
        $this->object = new Response($this->xml);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @expectedException Insig\AWSBundle\Exception
     */
    public function testConstructorException()
    {
        $obj = new Response();
    }

    /**
     * @expectedException Insig\AWSBundle\Exception
     */
    public function testInvalidDataException()
    {
        $response = new Response('Foo');
    }

    /**
     * @expectedException Insig\AWSBundle\Exception
     */
    public function testXMLHasErrorsException()
    {
        $errorXml = <<< EOD
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <Items>
        <Request>
            <Errors>
                <Error>
                    <Message>Error</Message>
                    <Code>12345</Code>
                </Error>
            </Errors>
        </Request>
    </Items>
</root>
EOD;
        $response = new Response($errorXml);
    }

    public function testAsArray()
    {
        $arr = $this->object->asArray();
        $this->assertSame(array('root' => array()), $arr);
    }

    public function testAsJson()
    {
        $json = $this->object->asJson();
        $this->assertSame('{"root":[]}', $json);
    }

    public function testAsXml()
    {
        $xml = $this->object->asXml();
        $this->assertInstanceOf('SimpleXMLElement', $xml);
    }

    public function testAsString()
    {
        $str = $this->object->asString();
        $this->assertSame($this->xml, $str);
    }

    public function testCastToString()
    {
        $str = (string) $this->object;
        $this->assertSame($this->xml, $str);
    }
}
