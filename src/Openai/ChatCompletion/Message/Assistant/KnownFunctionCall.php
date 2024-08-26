<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\Message\Assistant;

use Tolyan\Openai\ChatCompletion\CompletionRequest\ToolInterface;
use Tolyan\Openai\ChatCompletion\Tool\ToolSchemaInterface;

final readonly class KnownFunctionCall implements ToolCallInterface
{
    public ToolCallTypeEnum $type;

    /**
     * @param string                      $id        the ID of the tool call
     * @param class-string<ToolInterface> $tool      the tool class of the called function
     * @param ToolSchemaInterface         $arguments The arguments to call the function with
     */
    public function __construct(
        public string $id,
        public string $tool,
        public ToolSchemaInterface $arguments,
    ) {
        $this->type = ToolCallTypeEnum::FUNCTION;
    }
}