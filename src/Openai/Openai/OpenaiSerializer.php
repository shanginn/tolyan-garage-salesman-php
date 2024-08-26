<?php

declare(strict_types=1);

namespace Tolyan\Openai\Openai;

use Crell\Serde\SerdeCommon;
use Tolyan\Openai\ChatCompletion\Message\Assistant\UnknownFunctionCallImporter;
use Tolyan\Openai\ChatCompletion\Message\User\ImageContentPartNormalizer;
use Tolyan\Openai\ChatCompletion\Tool\ToolNormalizer;
use Tolyan\Openai\ChatCompletion\ToolChoice\ToolChoiceNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class OpenaiSerializer implements OpenaiSerializerInterface
{
    public SerializerInterface $serializer;
    private SerdeCommon $deserializer;

    public function __construct()
    {
        $encoders    = [new JsonEncoder()];
        $normalizers = [
            new BackedEnumNormalizer(),
            new ToolNormalizer(),
            new ToolChoiceNormalizer(),
            new ImageContentPartNormalizer(),
            new ObjectNormalizer(
                nameConverter: new CamelCaseToSnakeCaseNameConverter()
            ),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

        $this->deserializer = new SerdeCommon(
            handlers: [
                new UnknownFunctionCallImporter(),
            ]
        );
    }

    public function serialize(mixed $data): string
    {
        return $this->serializer->serialize(
            data: $data,
            format: 'json',
            context: [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]
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