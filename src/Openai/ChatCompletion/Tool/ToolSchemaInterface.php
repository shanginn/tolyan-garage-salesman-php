<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\Tool;

use Tolyan\Openai\ChatCompletion\CompletionRequest\ToolInterface;

interface ToolSchemaInterface
{
    /**
     * @return class-string<ToolInterface>
     */
    public static function getTool(): string;
}
