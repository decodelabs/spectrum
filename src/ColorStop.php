<?php

/**
 * @package Spectrum
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Spectrum;

use DecodeLabs\Glitch\Dumpable;
use Stringable;

class ColorStop implements Stringable, Dumpable
{
    public const UNITS = [
        'cm', 'mm', 'in', 'px', 'pt', 'pc',
        'em', 'ex', 'ch', 'rem', 'vw', 'vh', 'vmin', 'vmax',
        '%'
    ];

    protected Color $color;
    protected ?string $size = null;

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

        if (preg_match('/^([0-9]{1,4})(' . implode('|', self::UNITS) . ')$/', $size)) {
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
        $this->setColor($color);
        $this->setSize($size);
    }


    /**
     * Set base color
     *
     * @return $this
     */
    public function setColor(
        Color|string $color
    ): ColorStop {
        $this->color = Color::create($color);
        return $this;
    }

    /**
     * Get base color
     */
    public function getColor(): Color
    {
        return $this->color;
    }


    /**
     * Set gradient size in CSS units
     *
     * @return $this
     */
    public function setSize(
        string|int|null $size
    ): ColorStop {
        if (is_int($size)) {
            $size .= 'px';
        }

        $this->size = $size;
        return $this;
    }

    /**
     * Get gradient size in CSS units
     */
    public function getSize(): ?string
    {
        return $this->size;
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

    /**
     * Export for dump inspection
     */
    public function glitchDump(): iterable
    {
        yield 'definition' => $this->__toString();
    }
}
