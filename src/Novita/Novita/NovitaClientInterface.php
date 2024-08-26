<?php

declare(strict_types=1);

namespace Tolyan\Novita\Novita;

interface NovitaClientInterface
{
    public function post(string $method, string $json): string;
}
