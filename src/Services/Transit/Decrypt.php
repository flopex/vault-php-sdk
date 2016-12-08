<?php

namespace Jippi\Vault\Services\Transit;

use Jippi\Vault\Client;
use Jippi\Vault\OptionsResolver;

class Decrypt {

    const TRANSIT_DECRYPT_PATH = '/v1/transit/decrypt/';

    private $client;

    public function __construct(Client $client = null) {
        $this->client = $client ?: new Client();
    }

    public function decrypt($name, $body) {
        $body = OptionsResolver::resolve($body, ['ciphertext', 'context']);
        $body = json_encode($body);

        return $this->client->post(self::TRANSIT_DECRYPT_PATH.$name, $body)->getBody();
    }
}
