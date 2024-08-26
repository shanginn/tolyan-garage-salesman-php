<?php

declare(strict_types=1);

namespace Tolyan\Openai\Openai;

interface OpenaiSerializerInterface
{
    public function serialize(mixed $data): string;

    public function deserialize(mixed $serialized, string $to): object;
}