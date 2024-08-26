<?php

declare(strict_types=1);

namespace Tolyan\Openai\Openai;

interface OpenaiClientInterface
{
    public function sendRequest(string $method, string $json): string;
}
