<?php

declare(strict_types=1);

namespace Tolyan\Novita\Flux;

use Crell\Serde\Renaming\Cases;
use Crell\Serde\Attributes as Serde;

#[Serde\ClassSettings(
    renameWith: Cases::snake_case,
    omitNullFields: true
)]
final readonly class FluxResponseTask
{
    public function __construct(
        public string $taskId,
    ) {
    }
}