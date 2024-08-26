<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\Message\Assistant;

enum ToolCallTypeEnum: string
{
    case FUNCTION = 'function';
}
