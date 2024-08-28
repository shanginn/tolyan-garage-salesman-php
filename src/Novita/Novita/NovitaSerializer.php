<?php

declare(strict_types=1);

namespace Tolyan\Novita\Novita;

use Crell\Serde\SerdeCommon;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Tolyan\Openai\ChatCompletion\Message\User\ImageContentPartNormalizer;
use Tolyan\Openai\ChatCompletion\Tool\ToolNormalizer;
use Tolyan\Openai\ChatCompletion\ToolChoice\ToolChoiceNormalizer;

class NovitaSerializer implements NovitaSerializerInterface
{
    public SerdeCommon $serializer;
    private SerdeCommon $deserializer;

    public function __construct()
    {
        //        $encoders    = [new JsonEncoder()];
        //        $normalizers = [
        //            new BackedEnumNormalizer(),
        //            new ToolNormalizer(),
        //            new ToolChoiceNormalizer(),
        //            new ImageContentPartNormalizer(),
        //            new ObjectNormalizer(
        //                nameConverter: new CamelCaseToSnakeCaseNameConverter()
        //            ),
        //        ];

        $this->serializer = new SerdeCommon();

        $this->deserializer = new SerdeCommon();
    }

    public function serialize(mixed $data): string
    {
        return $this->serializer->serialize(
            object: $data,
            format: 'json'
        );
    }

    public function deserialize(mixed $serialized, string $to): object
    {
        return $this->deserializer->deserialize(
            serialized: $serialized,
            from: 'json',
            to: $to
        );
    }
}