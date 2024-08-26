<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\CompletionResponse;

use Crell\Serde\Attributes as Serde;
use Crell\Serde\Renaming\Cases;
use Tolyan\Openai\ChatCompletion\Message\AssistantMessage;

#[Serde\ClassSettings(
    renameWith: Cases::snake_case,
    omitNullFields: true
)]
final class Choice
{
    /**
     * @param int              $index        The index of the choice in the list of choices
     * @param AssistantMessage $message      A chat completion message generated by the model
     * @param string           $finishReason The reason the model stopped generating tokens
     * @param ?array           $logprobs     Log probability information for the choice
     */
    public function __construct(
        public int $index,
        public AssistantMessage $message,
        public string $finishReason,
        public ?array $logprobs = null,
    ) {}
}