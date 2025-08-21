<?php

/**
 * @package Spectrum
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Lucid\Processor;

use DecodeLabs\Exceptional;
use DecodeLabs\Lucid\Processor;
use DecodeLabs\Lucid\ProcessorTrait;
use DecodeLabs\Spectrum\Color as Spectrum;

/**
 * @implements Processor<Spectrum>
 */
class Color implements Processor
{
    /**
     * @use ProcessorTrait<Spectrum>
     */
    use ProcessorTrait;

    public const array OutputTypes = ['Spectrum:Color', Spectrum::class];

    public function coerce(
        mixed $value
    ): ?Spectrum {
        if (!class_exists(Spectrum::class)) {
            throw Exceptional::ComponentUnavailable(
                message: 'Color validation requires decodelabs-spectrum package'
            );
        }

        if ($value === null) {
            return null;
        }

        if (
            is_string($value) ||
            is_float($value) ||
            is_array($value) ||
            $value instanceof Spectrum
        ) {
            // @phpstan-ignore-next-line
            return Spectrum::create($value);
        }

        throw Exceptional::UnexpectedValue(
            message: 'Could not coerce value to Spectrum Color',
            data: $value
        );
    }
}
