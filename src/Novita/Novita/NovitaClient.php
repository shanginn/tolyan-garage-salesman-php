<?php

declare(strict_types=1);

namespace Tolyan\Novita\Novita;

use Http\Client\Exception\HttpException;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final readonly class NovitaClient implements NovitaClientInterface
{
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;

    public function __construct(
        private string $apiKey,
        private string $apiUrl = 'https://api.novita.ai',
    ) {
        $this->httpClient     = Psr18ClientDiscovery::find();
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
    public function post(string $method, string $json): string
    {
        $url = "{$this->apiUrl}{$method}";

        $request = $this->requestFactory
            ->createRequest('POST', $url)
            ->withHeader('Authorization', "Bearer {$this->apiKey}")
            ->withHeader('Content-Type', 'application/json')
            ->withBody(
                $this->streamFactory->createStream($json)
            );

        $response = $this->httpClient->sendRequest($request);

//        dump($response);

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