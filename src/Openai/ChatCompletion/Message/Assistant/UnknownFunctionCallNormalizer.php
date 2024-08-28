<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\Message\Assistant;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UnknownFunctionCallNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        assert($object instanceof UnknownFunctionCall);

        return [
            'id'       => $object->id,
            'type'     => $object->type,
            'function' => [
                'name'      => $object->name,
                'arguments' => $object->arguments,
            ],
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return $data instanceof UnknownFunctionCall;
    }
}