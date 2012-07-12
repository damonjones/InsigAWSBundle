<?php

/*
 * This file is part of the InsigAWSBundle package.
 *
 * (c) Damon Jones <damon@insig.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Insig\AWSBundle\Tests\Validator;

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\ConstraintValidatorFactory;

use \Insig\AWSBundle\Validator\UpcEan,
    \Insig\AWSBundle\Validator\UpcEanValidator
    ;

class UpcEanValidatorTest extends \PHPUnit_Framework_TestCase
{
    protected $validator;
    protected $validatorConstraint;

    static public function data()
    {
        return array(
            array('085391139676', true),    // UPC
            array('012345678905', true),
            array('8718011202666', true),   // EAN
            array('', false),               // empty string
            array('08539113967', false),    // too short
            array('08539113967600', false), // too long
            array('085391139670', false),   // incorrect check digit
        );
    }

    static public function illegalTypes()
    {
        return array(
           array(null),             // null
           array(new \DateTime),    // object
           array(false),            // boolean
           array(1),                // integer
           array(1.23),             // float
        );
    }

    public function setUp()
    {
        $this->validator = new Validator(
            new ClassMetadataFactory(new StaticMethodLoader()),
            new ConstraintValidatorFactory()
        );
        $this->validatorConstraint = new UpcEan;
    }

    /**
     * @dataProvider data()
     * @param string $upcean
     * @param string $isValid
     */
    public function testIsValid($upcean, $isValid)
    {
        $violations = $this->validator->validateValue($upcean, $this->validatorConstraint);
        $this->assertSame($isValid, 0 === count($violations));
    }

    /**
     * @dataProvider illegalTypes()
     * @expectedException Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @param string $upcean
     */
    public function testIllegalTypeThrowsException($upcean)
    {
        $violations = $this->validator->validateValue($upcean, $this->validatorConstraint);
    }
}
