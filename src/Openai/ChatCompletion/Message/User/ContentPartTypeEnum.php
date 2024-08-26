<?php

declare(strict_types=1);

namespace Tolyan\Openai\ChatCompletion\Message\User;

enum ContentPartTypeEnum: string
{
    case TEXT  = 'text';
    case IMAGE = 'image_url';
}
