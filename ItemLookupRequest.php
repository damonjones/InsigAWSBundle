<?php

/*
 * This file is part of the InsigAWSBundle package.
 *
 * (c) Damon Jones <damon@insig.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Insig\AWSBundle;

use Insig\AWSBundle\Exception as AWSException,
    Insig\AWSBundle\Validator\UpcEanValidator,
    Insig\AWSBundle\Validator\UpcEan
    ;

class ItemLookupRequest extends Request
{
    /**
     * Valid Response Groups
     */
    private static $responseGroups = array(
        'Accessories',
        'BrowseNodes',
        'EditorialReview',
        'Images',
        'ItemAttributes',
        'ItemIds',
        'Large',
        'ListmaniaLists',
        'Medium',
        'MerchantItemAttributes',
        'OfferFull',
        'Offers',
        'OfferSummary',
        'Request',
        'Reviews',
        'SalesRank',
        'Similarities',
        'Small',
        'Subjects',
        'Tracks',
        'VariationImage',
        'VariationMinimum',
        'Variations',
        'VariationSummary'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setParameter('Operation', 'ItemLookup');
    }

    /**
     * A convenience method which accepts a UPC/EAN,
     * validates it and sets the ItemId, IdType
     * and SearchIndex in the request query parameters
     */
    public function setUPC($upc = null)
    {
        if (!$upc) {
            throw new AWSException('UPC/EAN is required.');
        }

        $validator = new UpcEanValidator;
        if (!$validator->isValid($upc, new UpcEan)) {
            throw new \InvalidArgumentException(sprintf('UPC/EAN is invalid (%s).', $upc));
        }

        $this->setParameter('ItemId', $upc);
        $this->setParameter('IdType', 'UPC');
        $this->setParameter('SearchIndex', 'DVD');

        return $this;
    }

    /**
     * A convenience method which accepts an ASIN,
     * trims it and sets the ItemId and IdType
     * in the request query parameters
     */
    public function setASIN($asin = null)
    {
        if (!$asin) {
            throw new AWSException('ASIN is required.');
        }

        $this->setParameter('ItemId', trim($asin));
        $this->setParameter('IdType', 'ASIN');

        return $this;
    }
}
