<?php

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