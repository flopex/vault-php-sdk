<?php

namespace Jippi\Vault\Services\Transit;

use Jippi\Vault\Client;
use Jippi\Vault\OptionsResolver;

class datakey {

    const TRANSIT_datakey_PATH = '/v1/transit/datakey/';

    private $client;

    public function __construct(Client $client = null) {
        $this->client = $client ?: new Client();
    }

    public function datakey($returnType, $name, $body) {
        $body = OptionsResolver::resolve($body, ['nonce', 'context', 'bits']);
        $body = json_encode($body);

        return $this->client->post(self::TRANSIT_datakey_PATH.$returnType.'/'.$name, $body)->getBody();
    }
}
