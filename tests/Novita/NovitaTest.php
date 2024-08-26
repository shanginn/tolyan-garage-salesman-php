<?php

namespace Tolyan\Tests\Novita;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Discovery\Strategy\MockClientStrategy;
use Http\Mock\Client;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Tolyan\Novita\Flux\FluxResponse;
use Tolyan\Novita\Novita;
use PHPUnit\Framework\TestCase;

class NovitaTest extends TestCase
{
    private Novita $novita;
    private ClientInterface $client;

    public function setUp(): void
    {
        Psr18ClientDiscovery::prependStrategy(MockClientStrategy::class);

        $this->novita = new Novita(
            new Novita\NovitaClient('api_key')
        );

        $reflection = new \ReflectionClass($this->novita->client);
        $this->client = $reflection->getProperty('httpClient')->getValue($this->novita->client);
    }

    public function testFlux()
    {
        assert($this->client instanceof Client);

        $response = $this->createMock('Psr\Http\Message\ResponseInterface');
        $response->method('getBody')->willReturn(
            Psr17FactoryDiscovery::findStreamFactory()->createStream('{
                "images": [
                    {
                        "image_url": "https://example.com/image.jpg",
                        "image_url_ttl": 3600,
                        "image_type": "image/jpeg"
                    }
                ],
                "task": {
                    "task_id": "xxx"
                }
            }')
        );

        $response->method('getStatusCode')->willReturn(200);

        $this->client->addResponse($response);

        $response = $this->novita->flux('prompt', 1);

        self::assertInstanceOf(FluxResponse::class, $response);
        self::assertEquals('https://example.com/image.jpg', $response->images[0]->imageUrl);
        self::assertEquals('xxx', $response->task->taskId);



    }
}
