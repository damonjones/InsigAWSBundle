<?php

namespace Insig\AWSBundle;

use Insig\AWSBundle\Client,
    Insig\AWSBundle\Exception
    ;

/**
 * Request
 *
 * @author Damon Jones
 */

class Request
{
    /**
     * Valid Response Groups
     */
    private static $responseGroups = array(
        'Accessories',
        'AlternateVersions',
        'BrowseNodeInfo',
        'BrowseNodes',
        'Cart',
        'CartNewReleases',
        'CartTopSellers',
        'CartSimilarities',
        'Collections',
        'CustomerFull',
        'CustomerInfo',
        'CustomerLists',
        'CustomerReviews',
        'EditorialReview',
        'Help',
        'Images',
        'ItemAttributes',
        'ItemIds',
        'Large',
        'ListFull',
        'ListInfo',
        'ListItems',
        'ListmaniaLists',
        'ListMinimum',
        'Medium',
        'NewReleases',
        'OfferFull',
        'Offers',
        'OfferSummary',
        'PromotionDetails',
        'PromotionSummary',
        'Request',
        'Reviews',
        'SalesRank',
        'SearchBins',
        'Seller',
        'SellerListing',
        'Similarities',
        'Small',
        'Subjects',
        'TopSellers',
        'Tracks',
        'TransactionDetails',
        'VariationMinimum',
        'Variations',
        'VariationImages',
        'VariationSummary'
    );

    /**
     * Request Query Parameters
     *
     * @var string
     */
    protected $requestQueryParams;

    public function __construct()
    {
        $this->requestQueryParams = array(
            'MerchantId'    => 'All',
            'ResponseGroup' => 'Small'
        );
    }

    /**
     * Validates the response groups
     *
     * Response groups can be passed as a comma-delimited string or an array.
     * Response groups only contain lower and upper alpha characters, so any other characters are stripped
     * (with the exception of the comma delimiter for a string).
     * Case is significant, the response groups must match exactly the ones in the validResponseGroups array.
     * Each response group can occur only once in the set.
     * Response groups are sorted alphabetically.
     * After validation the response groups are returned as a comma-delimited string.
     * If there are no valid response groups the default value of 'Small' is returned.
     *
     *
     * @param   array|string    $responseGroups Array or comma-separated string of response groups
     * @return  Request                         this
     */
    protected function validateResponseGroups($responseGroups = array())
    {
        if (is_string($responseGroups)) {
            // remove unwanted characters
            $responseGroups = trim(preg_replace('/[^a-zA-Z,]/', '', $responseGroups), ',');
            // create an array
            $responseGroups = explode(',', $responseGroups);
        } elseif (is_array($responseGroups)) {
            // remove unwanted characters
            array_walk(
                $responseGroups,
                function(&$value) { $value = preg_replace('/[^a-zA-Z,]/', '', $value); }
            );
        }

        if (!empty($responseGroups)) {
            $validResponseGroups = self::$responseGroups;
            // filter out any response groups that are not in the allowed set
            $responseGroups = array_filter(
                $responseGroups,
                function ($element) use ($validResponseGroups) {
                    return (in_array($element, $validResponseGroups));
                }
            );
            // remove any duplicates
            $responseGroups = array_unique($responseGroups);
            // sort alphabetically
            sort($responseGroups);
        }

        // the default response group is 'Small'
        if (empty($responseGroups)) {
            $responseGroups = array('Small');
        }

        // return a comma-delimited string of response groups
        return implode(',', $responseGroups);
    }

    /**
     * Sets a parameter in the request query parameters
     *
     * @param   string  $name   The parameter name
     * @param   string  $value  The parameter value
     * @return  Request         this
     */
    public function setParameter($name, $value = null)
    {
        if ('ResponseGroup' === $name) {
            $value = $this->validateResponseGroups($value);
        }

        $this->requestQueryParams[$name] = $value;

        return $this;
    }

    /**
     * Gets a parameter from the request query parameters
     *
     * @param   string  $name   The parameter name
     * @return  mixed           The parameter value
     */
    public function getParameter($name)
    {
        if (array_key_exists($name, $this->requestQueryParams)) {
            return $this->requestQueryParams[$name];
        } else {
            return null;
        }
    }

    /**
     * Gets the query parameters
     *
     * @return  array   The query parameters
     */
    public function getParameters()
    {
        return $this->requestQueryParams;
    }
}
