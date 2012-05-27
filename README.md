Insig AWS Bundle
=====================

This is a Symfony 2 bundle which can be used to query Amazon's Product Advertising API.

[![Build Status](https://secure.travis-ci.org/damonjones/InsigAWSBundle.png?branch=master)](http://travis-ci.org/damonjones/InsigAWSBundle)

Requirements
------------

PHP 5.3+ with libxml<br />
An Amazon AWS account (or developer account)

Client Configuration
-------------

Add your account credentials to your app/config/config.yml (or a configuration file included by it):
    insig_aws:
        client:
            access_key_id:     ACCESS_KEY_ID
            secret_access_key: SECRET_ACCESS_KEY

Basic Usage
-----------

    $client = $this->get('insig_aws.client');

    $request = new Insig\AWSBundle\ItemLookupRequest();
    $request->setASIN('B0051QVESA');

    $response = $client->execute($request);

    $item = $response->asXml()->Items->Item;

Authors
-------

Damon Jones - <damon@insig.net>

License
-------

Insig AWS Bundle is licensed under the MIT License - see the LICENSE file for details
