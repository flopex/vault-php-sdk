<?php

namespace Jippi\Vault\Services\Transit;

use Jippi\Vault\Client;
use Jippi\Vault\OptionsResolver;

class Keys {

    const TRANSIT_KEYS_PATH = '/v1/transit/keys/';

    private $client;

    public function __construct(Client $client = null) {
        $this->client = $client ?: new Client();
    }

    public function createKey($name, $params) {
        $body = OptionsResolver::resolve($params['body'], ['derived']);
        $params['body'] = json_encode($body);

        return $this->client->post(self::TRANSIT_KEYS_PATH.$name, $params);

    }
}