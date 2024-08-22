<?php

/**
 * @package Spectrum
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Spectrum;

use DecodeLabs\Coercion;
use DecodeLabs\Exceptional;
use DecodeLabs\Glitch\Dumpable;
use DecodeLabs\Glitch\Proxy as Glitch;

use Stringable;
use Throwable;

/**
 * @property float $red
 * @property float $green
 * @property float $blue
 * @property float $alpha

 * @property float $hue
 * @property float $hslHue
 * @property float $saturation
 * @property float $hslSaturation
 * @property float $lightness
 * @property float $hslLightness

 * @property float $hsvHue
 * @property float $hsvSaturation
 * @property float $value
 * @property float $hsvValue
 */
class Color implements Stringable, Dumpable
{
    public const RGB = 'rgb';
    public const HSL = 'hsl';
    public const HSV = 'hsv';

    /**
     * Red or hue level
     */
    protected float $a;

    /**
     * Blue or saturation level
     */
    protected float $b;

    /**
     * Green, lightness or value level
     */
    protected float $c;

    /**
     * Alpha transparency between 0 and 1
     */
    protected float $alpha = 1.0;

    /**
     * One of rgb, hsl or hsv
     */
    protected string $mode;


    /**
     * Create a random color with optional saturation and lightness control
     */
    public static function random(
        ?float $saturation = null,
        ?float $lightness = null
    ): Color {
        if ($saturation === null) {
            $saturation = rand(1, 9) / 10;
        }

        if ($lightness === null) {
            $lightness = rand(3, 8) / 10;
        }

        return new self(rand(0, 359), $saturation, $lightness, null, self::HSL);
    }


    /**
     * Create or wrap new instance of Color
     *
     * @param Color|string|array<float>|null $color
     */
    public static function create(
        Color|string|array|float|null $color
    ): Color {
        if ($color instanceof Color) {
            return clone $color;
        }

        if (is_string($color)) {
            return self::fromString($color);
        }

        if (is_array($color)) {
            return new self(
                $color[0] ?? 0,
                $color[1] ?? 0,
                $color[2] ?? 0,
                $color[3] ?? 1,
                self::RGB
            );
        }

        return new self(0, 0, 0);
    }


    /**
     * Parse color string to Color
     */
    public static function fromString(
        string $color
    ): Color {
        if (!strlen($color)) {
            $color = 'black';
        }

        if (isset(self::Names[strtolower($color)])) {
            return self::fromName($color);
        }

        if (preg_match('@^(rgb|hsl|hsv)(a?)\((.*)\)@i', $color, $matches)) {
            return self::fromCssDefinition($matches);
        }

        return self::fromHex($color);
    }

    /**
     * Parse CSS definition regex matches
     *
     * @param array<string|null> $matches
     */
    protected static function fromCssDefinition(
        array $matches
    ): Color {
        $mode = $matches[1];
        $hasAlpha = $matches[2] === 'a';
        $args = (array)explode(',', trim((string)$matches[3]));

        $a = trim((string)($args[0] ?? '0'));
        $b = trim((string)($args[1] ?? '0'));
        $c = trim((string)($args[2] ?? '0'));
        $alpha = $hasAlpha ? trim((string)($args[3] ?? '1')) : '1';

        switch ($mode) {
            case self::RGB:
                if (substr($a, -1) === '%') {
                    $a = Coercion::toFloat(trim($a, '%')) / 100;
                } else {
                    $a = Coercion::toFloat($a) / 255;
                }

                if (substr($b, -1) === '%') {
                    $b = Coercion::toFloat(trim($b, '%')) / 100;
                } else {
                    $b = Coercion::toFloat($b) / 255;
                }

                if (substr($c, -1) === '%') {
                    $c = Coercion::toFloat(trim($c, '%')) / 100;
                } else {
                    $c = Coercion::toFloat($c) / 255;
                }

                break;

            case self::HSL:
            case self::HSV:
                $b = Coercion::toFloat(trim($b, '%')) / 100;
                $c = Coercion::toFloat(trim($c, '%')) / 100;
                break;
        }


        if (substr($alpha, -1) == '%') {
            $alpha = Coercion::toFloat(trim($alpha, '%')) / 100;
        }

        return new self(
            (float)$a,
            (float)$b,
            (float)$c,
            (float)$alpha,
            $mode
        );
    }


    /**
     * Create from name string
     */
    public static function fromName(
        string $name
    ): Color {
        $name = strtolower($name);

        if (isset(self::Names[$name])) {
            return new self(
                self::Names[$name][0] / 255,
                self::Names[$name][1] / 255,
                self::Names[$name][2] / 255,
                self::Names[$name][3] ?? 1
            );
        }

        throw Exceptional::{'InvalidArgument,Color'}(
            'Color name ' . $name . ' is not recognized'
        );
    }

    public static function isValidName(
        string $name
    ): bool {
        return isset(self::Names[strtolower($name)]);
    }

    public static function fromHex(
        string $hex
    ): Color {
        $hex = trim($hex);

        if (substr($hex, 0, 2) === '0x') {
            $hex = substr($hex, 2);
        } else {
            $hex = ltrim($hex, '#');
        }

        if (strlen($hex) === 6) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        } elseif (strlen($hex) === 3) {
            $r = substr($hex, 0, 1);
            $r = hexdec($r . $r);
            $g = substr($hex, 1, 1);
            $g = hexdec($g . $g);
            $b = substr($hex, 2, 1);
            $b = hexdec($b . $b);
        } else {
            throw Exceptional::{'InvalidArgument,Color'}(
                'Invalid color ' . $hex
            );
        }

        return new self($r / 255, $g / 255, $b / 255);
    }



    /**
     * Init with values, alpha and mode
     */
    public function __construct(
        float $a,
        float $b,
        float $c,
        ?float $alpha = null,
        string $mode = null
    ) {
        switch ($mode) {
            case self::RGB:
                $this->setRgba($a, $b, $c, $alpha);
                break;

            case self::HSL:
                $this->setHsla($a, $b, $c, $alpha);
                break;

            case self::HSV:
                $this->setHsva($a, $b, $c, $alpha);
                break;

            default:
                $this->setRgba($a, $b, $c, $alpha);
                break;
        }
    }


    /**
     * Shortcut accessors for common values
     */
    public function __get(
        string $member
    ): float {
        switch ($member) {
            case 'red': return $this->getRed();
            case 'green': return $this->getGreen();
            case 'blue': return $this->getBlue();
            case 'alpha': return $this->getAlpha();

            case 'hue':
            case 'hslHue': return $this->getHslHue();
            case 'saturation':
            case 'hslSaturation': return $this->getHslSaturation();
            case 'lightness':
            case 'hslLightness': return $this->getHslLightness();

            case 'hsvHue': return $this->getHsvHue();
            case 'hsvSaturation': return $this->getHsvSaturation();
            case 'value':
            case 'hsvValue': return $this->getHsvValue();

            default:
                throw Exceptional::InvalidArgument('Unknown property: ' . $member);
        }
    }



    /**
     * Set and convert format mode
     *
     * @return $this
     */
    public function setMode(
        string $mode
    ): Color {
        if ($mode === $this->mode) {
            return $this;
        }

        switch ($mode) {
            case self::RGB:
                $this->toRgb();
                break;

            case self::HSL:
                $this->toHsl();
                break;

            case self::HSV:
                $this->toHsv();
                break;

            default:
                throw Exceptional::InvalidArgument(
                    message: 'Invalid color mode',
                    data: $mode
                );
        }

        return $this;
    }



    /**
     * Convert to RGB mode
     *
     * @return $this
     */
    public function toRgb(): Color
    {
        switch ($this->mode) {
            case self::HSL: return $this->hslToRgb();
            case self::HSV: return $this->hsvToRgb();
        }

        return $this;
    }

    /**
     * Convert from HSL to RGB
     *
     * @return $this
     */
    protected function hslToRgb(): Color
    {
        $h = $this->a / 360;
        $s = $this->b;
        $l = $this->c;

        $m2 = $l <= 0.5 ? $l * ($s + 1) : $l + $s - $l * $s;
        $m1 = $l * 2 - $m2;

        $this->mode = self::RGB;
        $this->setRed(self::hslHueToRgb($m1, $m2, $h + 0.33333));
        $this->setGreen(self::hslHueToRgb($m1, $m2, $h));
        $this->setBlue(self::hslHueToRgb($m1, $m2, $h - 0.33333));

        return $this;
    }

    /**
     * Convert HSL hue to RGB tone
     */
    protected static function hslHueToRgb(
        float $m1,
        float $m2,
        float $h
    ): float {
        if ($h < 0) {
            $h += 1;
        }

        if ($h > 1) {
            $h -= 1;
        }

        if ($h < 1 / 6) {
            return $m1 + ($m2 - $m1) * 6 * $h;
        }

        if ($h < 1 / 2) {
            return $m2;
        }

        if ($h < 2 / 3) {
            return $m1 + ($m2 - $m1) * (2 / 3 - $h) * 6;
        }

        return $m1;
    }

    /**
     *  Convert HSV to RGB
     *
     * @return $this
     */
    protected function hsvToRgb(): Color
    {
        Glitch::incomplete('HSV to RGB is not yet supported');
    }



    /**
     * Convert to HSL
     *
     * @return $this
     */
    public function toHsl(): Color
    {
        if ($this->mode === self::HSV) {
            $this->hsvToRgb();
        }

        $r = $this->a;
        $g = $this->b;
        $b = $this->c;

        $min = min($r, min($g, $b));
        $max = max($r, max($g, $b));
        $delta = $max - $min;
        $l = ($min + $max) / 2;
        $s = 0;

        if ($l > 0 && $l < 1) {
            $s = $delta / ($l < 0.5 ? 2 * $l : (2 - 2 * $l));
        }

        $h = 0;

        if ($delta > 0) {
            if ($max == $r && $max != $g) {
                $h += ($g - $b) / $delta;
            }

            if ($max == $g && $max != $b) {
                $h += (2 + ($b - $r) / $delta);
            }

            if ($max == $b && $max != $r) {
                $h += (4 + ($r - $g) / $delta);
            }

            $h /= 6;
        }

        $this->mode = self::HSL;
        $this->setHslHue($h * 360);
        $this->setHslSaturation($s);
        $this->setHslLightness($l);

        return $this;
    }


    /**
     * Convert to HSV format
     *
     * @return $this
     */
    public function toHsv(): Color
    {
        if ($this->mode === self::HSL) {
            $this->hslToRgb();
        }

        $r = $this->a * 255;
        $g = $this->b * 255;
        $b = $this->c * 255;

        $minVal = min($r, $g, $b);
        $maxVal = max($r, $g, $b);
        $delta = $maxVal - $minVal;

        $v = $maxVal / 255;

        if ($delta == 0) {
            $h = 0;
            $s = 0;
        } else {
            $s = $delta / $maxVal;
            $deltaR = ((($maxVal - $r) / 6) + ($delta / 2)) / $delta;
            $deltaG = ((($maxVal - $g) / 6) + ($delta / 2)) / $delta;
            $deltaB = ((($maxVal - $b) / 6) + ($delta / 2)) / $delta;

            if ($r == $maxVal) {
                $h = $deltaB - $deltaG;
            } elseif ($g == $maxVal) {
                $h = (1 / 3) + $deltaR - $deltaB;
            } elseif ($b == $maxVal) {
                $h = (2 / 3) + $deltaG - $deltaR;
            } else {
                $h = 0;
            }

            if ($h < 0) {
                $h++;
            }

            if ($h > 1) {
                $h--;
            }
        }

        $this->setHsvHue($h * 360);
        $this->setHsvSaturation($s);
        $this->setHsvValue($v);

        return $this;
    }





    /**
     * Export to hex string (#0F0F0F)
     */
    public function toHexString(
        bool $allowShort = false
    ): string {
        if ($this->mode !== self::RGB) {
            $this->setMode(self::RGB);
        }

        $r = dechex((int)($this->a * 255));
        $g = dechex((int)($this->b * 255));
        $b = dechex((int)($this->c * 255));

        if (strlen($r) === 1) {
            $r = '0' . $r;
        }

        if (strlen($g) === 1) {
            $g = '0' . $g;
        }

        if (strlen($b) === 1) {
            $b = '0' . $b;
        }

        if (
            $allowShort &&
            $r[0] === $r[1] &&
            $g[0] === $g[1] &&
            $b[0] === $b[1]
        ) {
            $r = $r[0];
            $g = $g[0];
            $b = $b[0];
        }

        return '#' . $r . $g . $b;
    }


    /**
     * Convert to most appropriate CSS string
     */
    public function toCssString(): string
    {
        $this->setMode(self::RGB);

        if ($this->alpha < 1) {
            return 'rgba(' .
                round($this->a * 255) . ', ' .
                round($this->b * 255) . ', ' .
                round($this->c * 255) . ', ' .
                $this->alpha .
            ')';
        }

        return $this->toHexString(false);
    }


    /**
     * Convert to string
     */
    public function __toString(): string
    {
        try {
            return $this->toCssString();
        } catch (Throwable $e) {
            return '';
        }
    }




    /**
     * Set as RGBa with float values
     *
     * @return $this
     */
    public function setRgba(
        float $r,
        float $g,
        float $b,
        ?float $a = null
    ): Color {
        $this->mode = self::RGB;

        $this->setRed($r);
        $this->setGreen($g);
        $this->setBlue($b);
        $this->setAlpha($a);

        return $this;
    }

    /**
     * Set as RGB with float values
     *
     * @return $this
     */
    public function setRgb(
        float $r,
        float $g,
        float $b
    ): Color {
        return $this->setRgba($r, $g, $b, 1.0);
    }



    /**
     * Convert to RGBa and set red value
     *
     * @return $this
     */
    public function setRed(
        float $r
    ): Color {
        if ($this->mode !== self::RGB) {
            $this->setMode(self::RGB);
        }

        $this->a = self::clampFloat($r, 0, 1);
        return $this;
    }


    /**
     * Get RGB red value
     */
    public function getRed(): float
    {
        if ($this->mode !== self::RGB) {
            $this->setMode(self::RGB);
        }

        return $this->a;
    }


    /**
     * Convert to RGBa and set green value
     *
     * @return $this
     */
    public function setGreen(
        float $g
    ): Color {
        if ($this->mode !== self::RGB) {
            $this->setMode(self::RGB);
        }

        $this->b = self::clampFloat($g, 0, 1);
        return $this;
    }

    /**
     * Get RGB green value
     */
    public function getGreen(): float
    {
        if ($this->mode !== self::RGB) {
            $this->setMode(self::RGB);
        }

        return $this->b;
    }


    /**
     * Convert to RGBa and set blue balue
     *
     * @return $this
     */
    public function setBlue(
        float $b
    ): Color {
        if ($this->mode !== self::RGB) {
            $this->setMode(self::RGB);
        }

        $this->c = self::clampFloat($b, 0, 1);
        return $this;
    }

    /**
     * Get RGB blue value
     */
    public function getBlue(): float
    {
        if ($this->mode !== self::RGB) {
            $this->setMode(self::RGB);
        }

        return $this->c;
    }



    /**
     * Set HSLa mode with float values
     *
     * @return $this
     */
    public function setHsla(
        float $h,
        float $s,
        float $l,
        ?float $a = null
    ): Color {
        $this->mode = self::HSL;

        $this->setHslHue($h);
        $this->setHslSaturation($s);
        $this->setHslLightness($l);
        $this->setAlpha($a);

        return $this;
    }

    /**
     * Set HSL mode with float values
     *
     * @return $this
     */
    public function setHsl(
        float $h,
        float $s,
        float $l
    ): Color {
        return $this->setHsla($h, $s, $l, 1.0);
    }


    /**
     * Convert to HSL and set hue
     *
     * @return $this
     */
    public function setHslHue(
        float $h
    ): Color {
        if ($this->mode !== self::HSL) {
            $this->setMode(self::HSL);
        }

        $this->a = self::clampDegrees($h);
        return $this;
    }

    /**
     * Get HSL hue
     */
    public function getHslHue(): float
    {
        if ($this->mode !== self::HSL) {
            $this->setMode(self::HSL);
        }

        return $this->a;
    }

    /**
     * Convert to HSL and set saturation
     *
     * @return $this
     */
    public function setHslSaturation(
        float $s
    ): Color {
        if ($this->mode !== self::HSL) {
            $this->setMode(self::HSL);
        }

        $this->b = self::clampFloat($s, 0, 1);
        return $this;
    }

    /**
     * Get HSL saturation
     */
    public function getHslSaturation(): float
    {
        if ($this->mode !== self::HSL) {
            $this->setMode(self::HSL);
        }

        return $this->b;
    }

    /**
     * Convert to HSL and set lightness
     *
     * @return $this
     */
    public function setHslLightness(
        float $l
    ): Color {
        if ($this->mode !== self::HSL) {
            $this->setMode(self::HSL);
        }

        $this->c = self::clampFloat($l, 0, 1);
        return $this;
    }

    /**
     * Get HSL lightness
     */
    public function getHslLightness(): float
    {
        if ($this->mode !== self::HSL) {
            $this->setMode(self::HSL);
        }

        return $this->c;
    }



    /**
     * Set to HSVa mode with float values
     *
     * @return $this
     */
    public function setHsva(
        float $h,
        float $s,
        float $v,
        ?float $a = null
    ): Color {
        $this->mode = self::HSV;

        $this->setHsvHue($h);
        $this->setHsvSaturation($s);
        $this->setHsvValue($v);
        $this->setAlpha($a);

        return $this;
    }

    /**
     * Set to HSV mode with float values
     *
     * @return $this
     */
    public function setHsv(
        float $h,
        float $s,
        float $v
    ): Color {
        return $this->setHsva($h, $s, $v, 1.0);
    }


    /**
     * Convert to HSV and set hue
     *
     * @return $this
     */
    public function setHsvHue(
        float $h
    ): Color {
        if ($this->mode !== self::HSV) {
            $this->setMode(self::HSV);
        }

        $this->a = self::clampDegrees($h);
        return $this;
    }

    /**
     * Get HSV hue
     */
    public function getHsvHue(): float
    {
        if ($this->mode !== self::HSV) {
            $this->setMode(self::HSV);
        }

        return $this->a;
    }

    /**
     * Convert to HSV and set saturation
     *
     * @return $this
     */
    public function setHsvSaturation(
        float $s
    ): Color {
        if ($this->mode !== self::HSV) {
            $this->setMode(self::HSV);
        }

        $this->b = self::clampFloat($s, 0, 1);
        return $this;
    }

    /**
     * Get HSV saturation
     */
    public function getHsvSaturation(): float
    {
        if ($this->mode != self::HSV) {
            $this->setMode(self::HSV);
        }

        return $this->b;
    }


    /**
     * Convert to HSV and set value
     *
     * @return $this
     */
    public function setHsvValue(
        float $l
    ): Color {
        if ($this->mode !== self::HSV) {
            $this->setMode(self::HSV);
        }

        $this->b = self::clampFloat($l, 0, 1);
        return $this;
    }

    /**
     * Get HSV value
     */
    public function getHsvValue(): float
    {
        if ($this->mode !== self::HSV) {
            $this->setMode(self::HSV);
        }

        return $this->c;
    }



    /**
     * Set alpha opacity
     *
     * @return $this
     */
    public function setAlpha(
        ?float $alpha
    ): Color {
        if ($alpha === null) {
            $alpha = 1.0;
        }

        $this->alpha = self::clampFloat($alpha, 0, 1);
        return $this;
    }

    /**
     * Get alpha opacity
     */
    public function getAlpha(): float
    {
        return $this->alpha;
    }



    /**
     * Add another Color value to this
     *
     * @param Color|string|array<float>|null $color
     * @return $this
     */
    public function add(
        Color|string|array|null $color
    ): Color {
        $this->setMode(self::RGB);
        $color = self::create($color)
            ->setMode(self::RGB);

        $this->setRed($this->a + $color->a);
        $this->setGreen($this->b + $color->b);
        $this->setBlue($this->c + $color->c);

        return $this;
    }

    /**
     * Subtract another Color value to this
     *
     * @param Color|string|array<float>|null $color
     * @return $this
     */
    public function subtract(
        Color|string|array|null $color
    ): Color {
        $this->setMode(self::RGB);
        $color = self::create($color)
            ->setMode(self::RGB);

        $this->setRed($this->a - $color->a);
        $this->setGreen($this->b - $color->b);
        $this->setBlue($this->c - $color->c);

        return $this;
    }


    /**
     * Convert to HSL and apply lightness transformation
     *
     * @return $this
     */
    public function lighten(
        float $lightness
    ): Color {
        return $this->affectHslLightness($lightness);
    }

    /**
     * Convert to HSL and apply lightness transformation
     *
     * @return $this
     */
    public function darken(
        float $darkness
    ): Color {
        return $this->affectHslLightness(-1 * $darkness);
    }


    /**
     * Add amount to each HSLa property
     *
     * @return $this
     */
    public function affectHsl(
        float $h,
        float $s,
        float $l,
        ?float $a = null
    ): Color {
        $this->setMode(self::HSL);

        $this->setHslHue($this->a + $h);
        $this->setHslSaturation($this->b + $s);
        $this->setHslLightness($this->c + $l);

        if ($a !== null) {
            $this->affectAlpha($a);
        }

        return $this;
    }

    /**
     * Add amount to HSL hue
     *
     * @return $this
     */
    public function affectHslHue(
        float $h
    ): Color {
        $this->setMode(self::HSL);
        $this->setHslHue($this->a + $h);

        return $this;
    }

    /**
     * Add amount to HSL saturation
     *
     * @return $this
     */
    public function affectHslSaturation(
        float $s
    ): Color {
        $this->setMode(self::HSL);
        $this->setHslSaturation($this->b + $s);

        return $this;
    }

    /**
     * Add amount to HSL lightness
     *
     * @return $this
     */
    public function affectHslLightness(
        float $l
    ): Color {
        $this->setMode(self::HSL);
        $this->setHslLightness($this->c + $l);

        return $this;
    }



    /**
     * Add amount to each HSV property
     *
     * @return $this
     */
    public function affectHsv(
        float $h,
        float $s,
        float $v,
        ?float $a = null
    ): Color {
        $this->setMode(self::HSV);

        $this->setHsvHue($this->a + $h);
        $this->setHsvSaturation($this->b + $s);
        $this->setHsvValue($this->c + $v);

        if ($a !== null) {
            $this->affectAlpha($a);
        }

        return $this;
    }

    /**
     * Add amount to HSV hue
     *
     * @return $this
     */
    public function affectHsvHue(
        float $h
    ): Color {
        $this->setMode(self::HSV);
        $this->setHsvHue($this->a + $h);

        return $this;
    }

    /**
     * Add amount to HSV saturation
     *
     * @return $this
     */
    public function affectHsvSaturation(
        float $s
    ): Color {
        $this->setMode(self::HSV);
        $this->setHsvSaturation($this->b + $s);

        return $this;
    }

    /**
     * Add amount to HSV value
     *
     * @return $this
     */
    public function affectHsvValue(
        float $v
    ): Color {
        $this->setMode(self::HSV);
        $this->setHsvValue($this->c + $v);

        return $this;
    }


    /**
     * Add amount to alpha opacity
     *
     * @return $this
     */
    public function affectAlpha(
        float $a
    ): Color {
        return $this->setAlpha($this->alpha + $a);
    }



    /**
     * Add contrast value
     *
     * @return $this
     */
    public function affectContrast(
        float $amount
    ): Color {
        $this->setMode(self::HSL);
        $amount = self::clampFloat($amount, -1, 1);
        $ratio = $this->c - 0.5;

        return $this->setHslLightness(($ratio * $amount) + 0.5);
    }

    /**
     * Convert to midtone shade
     *
     * @return $this
     */
    public function toMidtone(
        float $amount = 1.0
    ): Color {
        $this->setMode(self::HSL);
        $amount = self::clampFloat($amount, 0, 1);
        $delta = $this->c - 0.5;

        return $this->setHslLightness($this->c - ($delta * $amount));
    }

    /**
     * Create opposite contrasting lightness from $color
     *
     * @param Color|string|array<float>|null $color
     * @return $this
     */
    public function contrastAgainst(
        Color|string|array|null $color,
        float $amount = 0.5
    ): Color {
        $this->setMode(self::RGB);
        $color = self::create($color)->setMode(self::RGB);

        $amount = self::clampFloat($amount, 0, 1);
        $delta1 = $this->c - 0.5;
        $delta2 = $color->c - 0.5;

        if ($delta2 < 0 && $delta1 < $delta2 + $amount) {
            $delta1 = $delta2 + $amount;
        } elseif ($delta2 > 0 && $delta1 > $delta2 - $amount) {
            $delta1 = $delta2 - $amount;
        }

        return $this->setHslLightness($delta1 + 0.5);
    }

    /**
     * Get ideal color for text on this color
     */
    public function getTextContrastColor(): Color
    {
        $this->setMode(self::HSL);

        if ($this->c > 0.8) {
            return self::create('black');
        } else {
            return self::create('white');
        }
    }


    /**
     * Clamp float between two numbers
     */
    protected static function clampFloat(
        float $number,
        float $min,
        float $max
    ): float {
        return max($min, min($max, $number));
    }

    /**
     * Clamp degrees float in range or between two numbers
     */
    protected static function clampDegrees(
        float $degrees,
        ?float $min = null,
        ?float $max = null
    ): float {
        while ($degrees < 0) {
            $degrees += 360;
        }

        while ($degrees > 359) {
            $degrees -= 360;
        }

        if ($min !== null) {
            $degrees = max($min, $degrees);
        }

        if ($max !== null) {
            $degrees = min($max, $degrees);
        }

        return $degrees;
    }



    // Preset colors
    protected const Names = [
        'aliceblue' => [240, 248, 255],
        'antiquewhite' => [250, 235, 215],
        'aqua' => [0,   255, 255],
        'aquamarine' => [127, 255, 212],
        'azure' => [240, 255, 255],
        'beige' => [245, 245, 220],
        'bisque' => [255, 228, 196],
        'black' => [0,   0,   0],
        'blanchedalmond' => [255, 235, 205],
        'blue' => [0,   0,   255],
        'blueviolet' => [138, 43,  226],
        'brown' => [165, 42,  42],
        'burlywood' => [222, 184, 135],
        'cadetblue' => [95,  158, 160],
        'chartreuse' => [127, 255, 0],
        'chocolate' => [210, 105, 30],
        'coral' => [255, 127, 80],
        'cornflowerblue' => [100, 149, 237],
        'cornsilk' => [255, 248, 220],
        'crimson' => [220, 20,  60],
        'cyan' => [0,   255, 255],
        'darkblue' => [0,   0,   13],
        'darkcyan' => [0,   139, 139],
        'darkgoldenrod' => [184, 134, 11],
        'darkgray' => [169, 169, 169],
        'darkgreen' => [0,   100, 0],
        'darkkhaki' => [189, 183, 107],
        'darkmagenta' => [139, 0,   139],
        'darkolivegreen' => [85,  107, 47],
        'darkorange' => [255, 140, 0],
        'darkorchid' => [153, 50,  204],
        'darkred' => [139, 0,   0],
        'darksalmon' => [233, 150, 122],
        'darkseagreen' => [143, 188, 143],
        'darkslateblue' => [72,  61,  139],
        'darkslategray' => [47,  79,  79],
        'darkturquoise' => [0,   206, 209],
        'darkviolet' => [148, 0,   211],
        'deeppink' => [255, 20,  147],
        'deepskyblue' => [0,   191, 255],
        'dimgray' => [105, 105, 105],
        'dodgerblue' => [30,  144, 255],
        'firebrick' => [178, 34,  34],
        'floralwhite' => [255, 250, 240],
        'forestgreen' => [34,  139, 34],
        'fuchsia' => [255, 0,   255],
        'gainsboro' => [220, 220, 220],
        'ghostwhite' => [248, 248, 255],
        'gold' => [255, 215, 0],
        'goldenrod' => [218, 165, 32],
        'gray' => [128, 128, 128],
        'green' => [0,   128, 0],
        'greenyellow' => [173, 255, 47],
        'honeydew' => [240, 255, 240],
        'hotpink' => [255, 105, 180],
        'indianred' => [205, 92,  92],
        'indigo' => [75,  0,   130],
        'ivory' => [255, 255, 240],
        'khaki' => [240, 230, 140],
        'lavender' => [230, 230, 250],
        'lavenderblush' => [255, 240, 245],
        'lawngreen' => [124, 252,  0],
        'lemonchiffon' => [255, 250, 205],
        'lightblue' => [173, 216, 230],
        'lightcoral' => [240, 128, 128],
        'lightcyan' => [224, 255, 255],
        'lightgoldenrodyellow' => [250, 250, 210],
        'lightgreen' => [144, 238, 144],
        'lightgrey' => [211, 211, 211],
        'lightpink' => [255, 182, 193],
        'lightsalmon' => [255, 160, 122],
        'lightseagreen' => [32, 178, 170],
        'lightskyblue' => [135, 206, 250],
        'lightslategray' => [119, 136, 153],
        'lightsteelblue' => [176, 196, 222],
        'lightyellow' => [255, 255, 224],
        'lime' => [0,   255, 0],
        'limegreen' => [50,  205, 50],
        'linen' => [250, 240, 230],
        'magenta' => [255, 0,   255],
        'maroon' => [128, 0,   0],
        'mediumaquamarine' => [102, 205, 170],
        'mediumblue' => [0,   0,   205],
        'mediumorchid' => [186, 85,  211],
        'mediumpurple' => [147, 112, 219],
        'mediumseagreen' => [60,  179, 113],
        'mediumslateblue' => [123, 104, 238],
        'mediumspringgreen' => [0,   250, 154],
        'mediumturquoise' => [72,  209, 204],
        'mediumvioletred' => [199, 21,  133],
        'midnightblue' => [25,  25,  112],
        'mintcream' => [245, 255, 250],
        'mistyrose' => [255, 228, 225],
        'moccasin' => [255, 228, 181],
        'navajowhite' => [255, 222, 173],
        'navy' => [0,   0,   128],
        'oldlace' => [253, 245, 230],
        'olive' => [128, 128, 0],
        'olivedrab' => [107, 142, 35],
        'orange' => [255, 165, 0],
        'orangered' => [255, 69,  0],
        'orchid' => [218, 112, 214],
        'palegoldenrod' => [238, 232, 170],
        'palegreen' => [152, 251, 152],
        'paleturquoise' => [175, 238, 238],
        'palevioletred' => [219, 112, 147],
        'papayawhip' => [255, 239, 213],
        'peachpuff' => [255, 218, 185],
        'peru' => [205, 133, 63],
        'pink' => [255, 192, 203],
        'plum' => [221, 160, 221],
        'powderblue' => [176, 224, 230],
        'purple' => [128, 0,   128],
        'red' => [255, 0,   0],
        'rosybrown' => [188, 143, 143],
        'royalblue' => [65,  105, 225],
        'saddlebrown' => [139, 69,  19],
        'salmon' => [250, 128, 114],
        'sandybrown' => [244, 164, 96],
        'seagreen' => [46,  139, 87],
        'seashell' => [255, 245, 238],
        'sienna' => [160, 82,  45],
        'silver' => [192, 192, 192],
        'skyblue' => [135, 206, 235],
        'slateblue' => [106, 90,  205],
        'slategray' => [112, 128, 144],
        'snow' => [255, 250, 250],
        'springgreen' => [0,   255, 127],
        'steelblue' => [70,  130, 180],
        'tan' => [210, 180, 140],
        'teal' => [0,   128, 128],
        'thistle' => [216, 191, 216],
        'tomato' => [255, 99,  71],
        'turquoise' => [64,  224, 208],
        'violet' => [238, 130, 238],
        'wheat' => [245, 222, 179],
        'white' => [255, 255, 255],
        'whitesmoke' => [245, 245, 245],
        'yellow' => [255, 255, 0],
        'yellowgreen' => [154, 205, 50],
        'transparent' => [0,   0,   0,   0]
    ];

    /**
     * Export for dump inspection
     */
    public function glitchDump(): iterable
    {
        $def = clone $this;
        yield 'definition' => $def->toCssString();

        $properties = [];

        switch ($this->mode) {
            case self::RGB:
                $properties = [
                    'r' => $this->a,
                    'g' => $this->b,
                    'b' => $this->c
                ];
                break;

            case self::HSL:
                $properties = [
                    'h' => $this->a,
                    's' => $this->b,
                    'l' => $this->c
                ];
                break;

            case self::HSV:
                $properties = [
                    'h' => $this->a,
                    's' => $this->b,
                    'v' => $this->c
                ];
                break;
        }

        $properties['alpha'] = $this->alpha;

        yield 'properties' => $properties;

        yield 'sections' => [
            'properties' => false
        ];
    }
}
