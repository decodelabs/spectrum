<?php

/**
 * @package Spectrum
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Lucid\Constraint\Color;

use DecodeLabs\Coercion;
use DecodeLabs\Lucid\Constraint;
use DecodeLabs\Lucid\ConstraintTrait;
use DecodeLabs\Lucid\Validate\Error;
use DecodeLabs\Spectrum\Color;
use Generator;

/**
 * @implements Constraint<float,Color>
 */
class MaxLightness implements Constraint
{
    /**
     * @use ConstraintTrait<float,Color>
     */
    use ConstraintTrait;

    public const int Weight = 20;

    public const array OutputTypes = [
        'Spectrum:Color'
    ];

    protected function validateParameter(
        mixed $parameter
    ): mixed {
        return Coercion::asFloat($parameter);
    }

    public function validate(
        mixed $value
    ): Generator {
        if ($value === null) {
            return false;
        }

        if ($value->getHslLightness() > $this->parameter) {
            yield new Error(
                $this,
                $value,
                '%type% value must not have lightness greater than %maxLightness%'
            );
        }

        return true;
    }
}
