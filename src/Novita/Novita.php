<?php

declare(strict_types=1);

namespace Tolyan\Novita;

use Tolyan\Novita\Flux\FluxRequest;
use Tolyan\Novita\Flux\FluxResponse;
use Tolyan\Novita\Novita\NovitaClientInterface;
use Tolyan\Novita\Novita\NovitaSerializer;
use Tolyan\Novita\Novita\NovitaSerializerInterface;

final readonly class Novita
{
    private NovitaSerializerInterface $serializer;

    public function __construct(
        public NovitaClientInterface $client,
    ) {
        $this->serializer   = new NovitaSerializer();
    }

    public function flux(
        string $prompt,
        int $steps = 4,
        ?int $seed = null,
        int $width = 1024,
        int $height = 1024,
        int $imageNum = 1,
        string $model = 'schnell',
    ): FluxResponse {
        $body = $this->serializer->serialize(new FluxRequest(
            prompt: $prompt,
            steps: $steps,
            seed: $seed,
            width: $width,
            height: $height,
            imageNum: $imageNum,
        ));

        $responseJson = $this->client->post("/v3beta/flux-1-{$model}", $body);

        dump($responseJson);

        $response = $this->serializer->deserialize($responseJson, FluxResponse::class);

        return $response;
    }
}
