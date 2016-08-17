<?php

namespace Jippi\Vault\Services\Transit;

use Jippi\Vault\Client;
use Jippi\Vault\OptionsResolver;

class Encrypt {

    const TRANSIT_ENCRYPT_PATH = '/v1/transit/encrypt/';

    private $client;

    public function __construct(Client $client = null) {
        $this->client = $client ?: new Client();
    }

    public function encrypt($name, $params) {
        $body = OptionsResolver::resolve($params['body'], ['plaintext', 'context']);
        $params['body'] = json_encode($body);

        return $this->client->post(self::TRANSIT_ENCRYPT_PATH.$name, $params)->json();
    }

}
