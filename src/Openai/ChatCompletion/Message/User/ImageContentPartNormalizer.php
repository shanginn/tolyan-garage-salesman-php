<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\Message\User;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ImageContentPartNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        return [
            'type'      => $object->type,
            'image_url' => array_filter([
                'url'    => $object->url,
                'detail' => $object->detail,
            ]),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return $data instanceof ImageContentPart;
    }
}