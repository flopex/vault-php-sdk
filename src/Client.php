<?php
namespace Jippi\Vault;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
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
            'base_url' => 'http://127.0.0.1:8200',
            'headers'  => ['exceptions' => false],
        ), $options);

        $this->client = $client ?: new GuzzleClient($options);
        $this->logger = $logger ?: new NullLogger();
    }

    public function get($url = null, array $options = array())
    {
        $request = new Request('GET',$url, $options);
        return $this->send($request);
    }

    public function head($url, array $options = array())
    {
        $request = new Request('HEAD',$url, $options);
        return $this->send($request);
    }

    public function delete($url, array $options = array())
    {
        $request = new Request('DELETE',$url, $options);
        return $this->send($request);
    }

    public function put($url, array $options = array())
    {
        $request = new Request('PUT',$url, $options);
        return $this->send($request);
    }

    public function patch($url, array $options = array())
    {
        $request = new Request('PATCH',$url, $options);
        return $this->send($request);
    }

    public function post($url, array $options = array())
    {
        $request = new Request('POST',$url, $options);
        return $this->send($request);
    }

    public function options($url, array $options = array())
    {
        $request = new Request('OPTIONS',$url, $options);
        return $this->send($request);
    }

    public function send(Request $request)
    {
        $this->logger->info(sprintf('%s "%s"', $request->getMethod(), $request->getUrl()));
        $this->logger->debug(sprintf("Request:\n%s", (string) $request));

        try {
            $response = $this->client->send($request);
        } catch (TransferException $e) {
            $message = sprintf('Something went wrong when calling vault (%s).', $e->getMessage());

            $this->logger->error($message);

            throw new ServerException($message);
        }

        $this->logger->debug(sprintf("Response:\n%s", $response));

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
