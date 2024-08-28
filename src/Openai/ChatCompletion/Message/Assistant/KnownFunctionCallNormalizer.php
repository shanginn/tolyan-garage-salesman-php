<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\Message\Assistant;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class KnownFunctionCallNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        assert($object instanceof KnownFunctionCall);

        return [
            'id'       => $object->id,
            'type'     => $object->type,
            'function' => [
                'name'      => $object->tool::getName(),
                'arguments' => json_encode($object->arguments),
            ],
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return $data instanceof KnownFunctionCall;
    }
}