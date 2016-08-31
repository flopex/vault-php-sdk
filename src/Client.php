<?php
namespace Jippi\Vault;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Jippi\Vault\Exception\ClientException;
use Jippi\Vault\Exception\ServerException;

class Client
{
    private $client;
    private $logger;

    public function __construct(array $options = array(), LoggerInterface $logger = null, GuzzleClient $client = null)
    {
        $options = array_replace(array(
            'base_uri' => 'http://127.0.0.1:8200',
            'headers'  => ['exceptions' => false],
        ), $options);

        $this->client = $client ?: new GuzzleClient($options);
        $this->logger = $logger ?: new NullLogger();
    }

    public function get($url, $body = '', array $headers = array())
    {
        $request = new Request('GET',$url, $headers, $body);
        return $this->send($request);
    }

    public function head($url, $body = '', array $headers = array())
    {
        $request = new Request('HEAD',$url, $headers, $body);
        return $this->send($request);
    }

    public function delete($url, $body = '', array $headers = array())
    {
        $request = new Request('DELETE',$url, $headers, $body);
        return $this->send($request);
    }

    public function put($url, $body = '', array $headers = array())
    {
        $request = new Request('PUT',$url, $headers, $body);
        return $this->send($request);
    }

    public function patch($url, $body = '', array $headers = array())
    {
        $request = new Request('PATCH',$url, $headers, $body);
        return $this->send($request);
    }

    public function post($url, $body = '', array $headers = array())
    {
        $request = new Request('POST',$url, $headers, $body);
        return $this->send($request);
    }

    public function options($url, $body = '', array $headers = array())
    {
        $request = new Request('OPTIONS',$url, $headers, $body);
        return $this->send($request);
    }

    public function send(Request $request)
    {
        $this->logger->info(sprintf('%s "%s"', $request->getMethod(), $request->getUri()));
        $this->logger->debug(sprintf("Request:\n%s", \GuzzleHttp\Psr7\str($request)));

        try {
            $response = $this->client->send($request);
        } catch (TransferException $e) {
            $message = sprintf('Something went wrong when calling vault (%s).', $e->getMessage());

            $this->logger->error($message);

            throw new ServerException($message);
        }

        $this->logger->debug(sprintf("Response:\n%s", \GuzzleHttp\Psr7\str($response)));

        if (400 <= $response->getStatusCode()) {
            $message = sprintf('Something went wrong when calling vault (%s - %s).', $response->getStatusCode(), $response->getReasonPhrase());

            $this->logger->error($message);

            $message .= "\n$response";
            if (500 <= $response->getStatusCode()) {
                throw new ServerException($message, $response->getStatusCode(), $response);
            }

            throw new ClientException($message, $response->getStatusCode(), $response);
        }

        return $response;
    }
}
