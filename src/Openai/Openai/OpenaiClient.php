<?php

declare(strict_types=1);

namespace Tolyan\Openai\Openai;

use Http\Adapter\React\Client;
use Http\Client\Exception\HttpException;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final readonly class OpenaiClient implements OpenaiClientInterface
{
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;

    public function __construct(
        private string $apiKey,
        private string $apiUrl = 'https://api.openai.com/v1',
    ) {
        $this->httpClient     = new Client();
        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory  = Psr17FactoryDiscovery::findStreamFactory();
    }

    /**
     * @param string $method
     * @param string $json
     *
     * @throws HttpException
     * @throws ClientExceptionInterface
     *
     * @return string
     */
    public function sendRequest(string $method, string $json): string
    {
        $url = "{$this->apiUrl}{$method}";

        dump($url, $json);

        $request = $this->requestFactory
            ->createRequest('POST', $url)
            ->withHeader('Authorization', "Bearer {$this->apiKey}")
            ->withHeader('Content-Type', 'application/json')
            ->withBody(
                $this->streamFactory->createStream($json)
            );

        $response = $this->httpClient->sendRequest($request);
        dump($response);

        if ($response->getStatusCode() !== 200) {
            throw new HttpException(
                message: "Request failed with status code {$response->getStatusCode()} ({$response->getReasonPhrase()}): {$response->getBody()->getContents()}",
                request: $request,
                response: $response,
            );
        }

        return $response->getBody()->getContents();
    }
}
