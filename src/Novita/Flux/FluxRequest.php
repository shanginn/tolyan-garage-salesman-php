<?php

declare(strict_types=1);

namespace Tolyan\Novita\Flux;

use Crell\Serde\Renaming\Cases;
use Crell\Serde\Attributes as Serde;

#[Serde\ClassSettings(
    renameWith: Cases::snake_case,
    omitNullFields: true
)]
final readonly class FluxRequest
{
    /**
     * @var int|null A seed is a number from which Stable Diffusion generates noise
     */
    public ?int $seed;

    /**
     * Constructor to initialize properties with promoted parameters.
     *
     *                                    Enum: png, webp, jpeg
     * @param string $prompt              Text input required to guide the image generation, divided by `,`.
     *                                    making generation deterministic.
     * @param int    $steps               The number of denoising steps. More steps usually produce higher quality images,
     *                                    but take more time to generate.
     * @param int    $width               Width of the image.
     * @param int    $height              Height of the image.
     * @param int    $imageNum           Number of images generated in one single generation.
     * @param string $responseImageType The returned image type. Default is png.
     */
    public function __construct(
        public string $prompt,
        public int    $steps,
        $seed = null,
        public int    $width = 1024,
        public int    $height = 1024,
        public int    $imageNum = 1,
        public string $responseImageType = 'png',
    ) {
        $this->seed = $seed ?? random_int(1, 4294967295);

        if (!in_array($this->responseImageType, ['png', 'webp', 'jpeg'])) {
            throw new \InvalidArgumentException('Invalid value for $responseImageType');
        }
    }
}