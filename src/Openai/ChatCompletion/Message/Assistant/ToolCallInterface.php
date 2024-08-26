<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\Message\Assistant;

use Tolyan\Openai\Util\BackedEnumTypeMap;

#[BackedEnumTypeMap(key: 'type', map: [
    ToolCallTypeEnum::FUNCTION->value => UnknownFunctionCall::class,
])]
interface ToolCallInterface {}