<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\Message;

use Tolyan\Openai\ChatCompletion\CompletionRequest\Role;
use Tolyan\Openai\Util\BackedEnumTypeMap;

#[BackedEnumTypeMap(key: 'role', map: [
    Role::USER->value      => UserMessage::class,
    Role::ASSISTANT->value => AssistantMessage::class,
    Role::SYSTEM->value    => SystemMessage::class,
    Role::TOOL->value      => ToolMessage::class,
])]
interface MessageInterface {}