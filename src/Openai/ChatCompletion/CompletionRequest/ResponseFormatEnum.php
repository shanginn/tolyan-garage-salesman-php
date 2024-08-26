<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\CompletionRequest;

enum ResponseFormatEnum: string
{
    case TEXT = 'text';
    case JSON = 'json_object';
}
