<?php

/**
 * @package Spectrum
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Spectrum;

use DecodeLabs\Nuance\Dumpable;
use DecodeLabs\Nuance\Entity\NativeObject as NuanceEntity;
use Stringable;

class ColorStop implements
    Stringable,
    Dumpable
{
    protected const Units = [
        'cm', 'mm', 'in', 'px', 'pt', 'pc',
        'em', 'ex', 'ch', 'rem', 'vw', 'vh', 'vmin', 'vmax',
        '%'
    ];

    public Color $color {
        get => $this->color;
        set(string|Color $color) => Color::create($color);
    }

    public ?string $size = null {
        get => $this->size;
        set(string|int|null $size) {
            if (is_int($size)) {
                $size .= 'px';
            }

            $this->size = $size;
        }
    }

    /**
     * Create a new color stop from string or ColorStop
     */
    public static function create(
        ColorStop|string $colorStop
    ): ColorStop {
        if ($colorStop instanceof ColorStop) {
            return clone $colorStop;
        }

        $parts = explode(' ', (string)$colorStop);
        $size = array_pop($parts);

        if (preg_match('/^([0-9]{1,4})(' . implode('|', self::Units) . ')$/', $size)) {
            $color = implode(' ', $parts);
        } else {
            $color = $colorStop;
            $size = null;
        }

        return new self($color, $size);
    }

    /**
     * Init with color and size
     */
    public function __construct(
        Color|string $color,
        string|int|null $size
    ) {
        $this->color = $color;
        $this->size = $size;
    }


    /**
     * Convert to string
     */
    public function __toString(): string
    {
        $output = $this->color->toCssString();

        if ($this->size !== null) {
            $output .= ' ' . $this->size;
        }

        return $output;
    }

    public function toNuanceEntity(): NuanceEntity
    {
        $entity = new NuanceEntity($this);
        $entity->definition = $this->__toString();
        return $entity;
    }
}
