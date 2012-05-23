<?php

/*
 * This file is part of the InsigAWSBundle package.
 *
 * (c) Damon Jones <damon@insig.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Insig\AWSBundle\Validator;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator,
    Symfony\Component\Validator\Exception\UnexpectedTypeException
    ;

class UpcEanValidator extends ConstraintValidator
{
    public function isValid($value, Constraint $constraint)
    {
        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!preg_match('/^\d{12,13}$/', $value)) {
            $this->setMessage($constraint->message, array('{{ value }}' => $value));

            return false;
        }

        $lastDigit = strlen($value) - 1;
        $accumulator = 0;
        $checkDigit = (int) $value[$lastDigit];

        // reverse the actual digits (excluding the check digit)
        $str = strrev(substr($value, 0, $lastDigit));

        /*
         *  scanning from right to left
         *  even digits are just added in (multiplied by one)
         *  odd digits are multiplied by three
         */
        for ($i = 0; $i < $lastDigit; $i++) {
          $accumulator += (int) $str[$i] * (($i % 2) ? 1 : 3);
        }

        $checksum = (10 - ($accumulator % 10)) % 10;

        if ($checksum !== $checkDigit) {
            $this->setMessage($constraint->message, array('{{ value }}' => $value));

            return false;
        }

        return true;
    }
}
