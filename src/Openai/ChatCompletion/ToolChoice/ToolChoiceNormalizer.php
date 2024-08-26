<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\ToolChoice;

use Tolyan\Openai\ChatCompletion\CompletionRequest\ToolChoice;
use Tolyan\Openai\ChatCompletion\CompletionRequest\ToolInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ToolChoiceNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        assert($object instanceof ToolChoice);

        $choice = [
            'type' => $object->type->value,
        ];

        if ($object->type === ToolChoiceType::REQUIRED && $object->tool !== null) {
            assert(is_a($object->tool, ToolInterface::class, true));

            $choice = [
                'type'     => 'function',
                'function' => [
                    'name' => $object->tool::getName(),
                ],
            ];
        }

        return $choice;
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return $data instanceof ToolChoice;
    }
}
