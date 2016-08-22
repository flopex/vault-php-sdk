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

    public function createKey($name, $body) {
        $body = OptionsResolver::resolve($body, ['derived']);
        $body = json_encode($body);

        return $this->client->post(self::TRANSIT_KEYS_PATH.$name, $body);

    }
}
