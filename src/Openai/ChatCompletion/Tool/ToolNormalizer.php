<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\Tool;

use Spiral\JsonSchemaGenerator\Generator;
use Spiral\JsonSchemaGenerator\GeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tolyan\Openai\ChatCompletion\CompletionRequest\ToolInterface;

final readonly class ToolNormalizer implements NormalizerInterface
{
    private GeneratorInterface $jsonSchemaGenerator;

    public function __construct()
    {
        $this->jsonSchemaGenerator = new Generator();
    }

    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        assert(is_a($object, ToolInterface::class, true));

        return [
            'type'     => 'function',
            'function' => [
                'name'        => $object::getName(),
                'description' => $object::getDescription(),
                'parameters'  => [
                    'type' => 'object',
                    ...$this->jsonSchemaGenerator->generate($object::getSchemaClass())->jsonSerialize(),
                ],
            ],
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return is_a($data, ToolInterface::class, true);
    }
}
