<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 09/03/2015
 */

namespace Arthurh\RestProxifier;


use GuzzleHttp\Client;
use GuzzleHttp\Message\MessageFactoryInterface;
use Proxy\Adapter\Guzzle\GuzzleAdapter;

class AdapterProxy extends GuzzleAdapter
{
    public function __construct(Client $client = null, MessageFactoryInterface $messageFactory = null)
    {
        parent::__construct($client, $messageFactory);
        $this->client->setDefaultOption('verify', false);
    }
}
