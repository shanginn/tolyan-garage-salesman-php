<?php

declare(strict_types=1);

namespace Tolyan\Novita\Flux;

use Crell\Serde\Attributes as Serde;
use Crell\Serde\Renaming\Cases;

#[Serde\ClassSettings(
    renameWith: Cases::snake_case,
    omitNullFields: true
)]
final readonly class FluxResponse
{
    /**
     * @param array<FluxResponseImage> $images
     * @param FluxResponseTask         $task
     */
    public function __construct(
        #[Serde\SequenceField(arrayType: FluxResponseImage::class)]
        public array $images,
        public FluxResponseTask $task,
    ) {}
}