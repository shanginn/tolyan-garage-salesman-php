<?php

declare(strict_types=1);

namespace Tolyan\Novita\Novita;

interface NovitaSerializerInterface
{
    public function serialize(mixed $data): string;

    public function deserialize(mixed $serialized, string $to): object;
}