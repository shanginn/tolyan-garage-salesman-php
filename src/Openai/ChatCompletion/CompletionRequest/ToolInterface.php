<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\CompletionRequest;

use Tolyan\Openai\ChatCompletion\Tool\ToolSchemaInterface;

interface ToolInterface
{
    /**
     * @return class-string<ToolSchemaInterface>
     */
    public static function getSchemaClass(): string;

    public static function getName(): string;

    public static function getDescription(): string;
}
