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
use GuzzleHttp\Message\MessageFactory;
use GuzzleHttp\Message\MessageFactoryInterface;
use Proxy\Adapter\Guzzle\GuzzleAdapter;
use Symfony\Component\HttpFoundation\Request;

class AdapterProxy extends GuzzleAdapter
{
    public function __construct(Client $client = null, MessageFactoryInterface $messageFactory = null)
    {
        $this->client = $client ?: new Client([
            'defaults' => [
                'verify' => __DIR__ . '/../../../assets/cert/cacert.pem'
            ]
        ]);
        $this->messageFactory = $messageFactory ?: new MessageFactory();

    }

    public function send(Request $request, $url)
    {

        var_dump($this->client->getDefaultOption());
        $guzzleRequest = $this->convertRequest($request);

        $guzzleRequest->setUrl($url);

        $guzzleResponse = $this->client->send($guzzleRequest);

        return $this->convertResponse($guzzleResponse);
    }
}
