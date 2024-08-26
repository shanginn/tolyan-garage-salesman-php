<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\CompletionRequest;

use Crell\Serde\Attributes\Field;
use Crell\Serde\Deserializer;
use Crell\Serde\PropertyHandler\Exporter;
use Crell\Serde\PropertyHandler\Importer;
use Crell\Serde\Serializer;
use InvalidArgumentException;

/** @deprecated  */
class ResponseFormatExporter implements Exporter, Importer
{
    public function exportValue(Serializer $serializer, Field $field, mixed $value, mixed $runningValue): mixed
    {
        return [
            'type' => $value->value,
        ];
    }

    public function canExport(Field $field, mixed $value, string $format): bool
    {
        return $value instanceof ResponseFormatEnum;
    }

    public function importValue(Deserializer $deserializer, Field $field, mixed $source): mixed
    {
        return match ($source['type']) {
            ResponseFormatEnum::TEXT->value => ResponseFormatEnum::TEXT,
            ResponseFormatEnum::JSON->value => ResponseFormatEnum::JSON,
            default                         => throw new InvalidArgumentException('Invalid response format type'),
        };
    }

    public function canImport(Field $field, string $format): bool
    {
        return $field->phpType === ResponseFormatEnum::class;
    }
}
