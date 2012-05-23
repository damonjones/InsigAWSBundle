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

use Symfony\Component\Validator\Constraint;

class UpcEan extends Constraint
{
    public $message = 'The UPC/EAN is invalid.';

    public function getTargets()
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }
}
