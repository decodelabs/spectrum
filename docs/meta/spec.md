# Spectrum — Package Specification

> **Cluster:** `core`
> **Language:** `php`
> **Milestone:** `m4`
> **Repo:** `https://github.com/decodelabs/spectrum`
> **Role:** Colour parsing

## Overview

### Purpose

Spectrum offers a simple but powerful system for parsing, inspecting, manipulating and exporting colors. It brings color to PHP with support for multiple color formats (RGB, HSL, HSV) and various input formats (hex, CSS functions, named colors).

Key features:
- **Multiple color formats**: RGB, HSL, and HSV color spaces
- **Flexible parsing**: Parse from hex strings, CSS functions, named colors, or arrays
- **Color manipulation**: Lighten, darken, adjust saturation, contrast, and more
- **Format conversion**: Convert between RGB, HSL, and HSV formats
- **CSS output**: Export colors as CSS-compatible strings
- **Named colors**: Support for 140+ CSS named colors
- **Alpha channel**: Full support for transparency/opacity

### Non-Goals

- Spectrum does not provide color palette generation or color scheme tools.
- It does not handle color profiles or color management (ICC profiles).
- It does not provide color blindness simulation or accessibility tools.
- It does not handle image color extraction or analysis.
- It does not provide color matching or color distance calculations.

## Role in the Ecosystem

### Cluster & Positioning

Spectrum belongs to the **core** cluster, providing fundamental color manipulation capabilities. It complements other core packages like `coercion` (type coercion) and `nuance` (debugging) by providing color parsing and manipulation.

### Usage Contexts

- **CSS generation**: Generating CSS color values for stylesheets
- **Color manipulation**: Adjusting colors for themes and UI components
- **Data validation**: Validating color inputs via Lucid constraints
- **Color conversion**: Converting between different color formats
- **Random colors**: Generating random colors for UI elements

## Public Surface

### Key Types

- **`Color`** (class): Main color class providing parsing, manipulation, and export capabilities. Supports RGB, HSL, and HSV color spaces. Implements `Stringable` and `Dumpable` interfaces.

- **`Mode`** (enum): Color space mode enumeration: `RGB`, `HSL`, `HSV`.

- **`ColorStop`** (class): Represents a color stop for gradients with optional size. Implements `Stringable` and `Dumpable` interfaces.

- **`Lucid\Processor\Color`** (class): Lucid processor for color coercion and validation.

- **`Lucid\Constraint\Color\MinLightness`** (class): Lucid constraint for minimum lightness validation.

- **`Lucid\Constraint\Color\MaxLightness`** (class): Lucid constraint for maximum lightness validation.

- **`Lucid\Constraint\Color\MinSaturation`** (class): Lucid constraint for minimum saturation validation.

- **`Lucid\Constraint\Color\MaxSaturation`** (class): Lucid constraint for maximum saturation validation.

### Main Entry Points

**Color Creation:**
- `Color::create(Color|string|array|float|null $color): Color` — Create color from various formats
- `Color::fromString(string $color): Color` — Parse color from string
- `Color::fromName(string $name): Color` — Create color from CSS color name
- `Color::fromHex(string $hex): Color` — Parse color from hex string
- `Color::random(?float $saturation = null, ?float $lightness = null): Color` — Generate random color
- `new Color(float $a, float $b, float $c, ?float $alpha = null, Mode $mode = Mode::RGB)` — Constructor

**RGB Access:**
- `$color->red` — Red component (0-1, property with getter/setter)
- `$color->green` — Green component (0-1, property with getter/setter)
- `$color->blue` — Blue component (0-1, property with getter/setter)
- `$color->getRed(): float` — Get red component
- `$color->setRed(float $r): Color` — Set red component
- `$color->getGreen(): float` — Get green component
- `$color->setGreen(float $g): Color` — Set green component
- `$color->getBlue(): float` — Get blue component
- `$color->setBlue(float $b): Color` — Set blue component
- `$color->setRgb(float $r, float $g, float $b): Color` — Set RGB values
- `$color->setRgba(float $r, float $g, float $b, ?float $a = null): Color` — Set RGBA values

**HSL Access:**
- `$color->hue` — HSL hue (0-360, property with getter/setter)
- `$color->hslHue` — HSL hue (0-360, property with getter/setter)
- `$color->saturation` — HSL saturation (0-1, property with getter/setter)
- `$color->hslSaturation` — HSL saturation (0-1, property with getter/setter)
- `$color->lightness` — HSL lightness (0-1, property with getter/setter)
- `$color->hslLightness` — HSL lightness (0-1, property with getter/setter)
- `$color->getHslHue(): float` — Get HSL hue
- `$color->setHslHue(float $h): Color` — Set HSL hue
- `$color->getHslSaturation(): float` — Get HSL saturation
- `$color->setHslSaturation(float $s): Color` — Set HSL saturation
- `$color->getHslLightness(): float` — Get HSL lightness
- `$color->setHslLightness(float $l): Color` — Set HSL lightness
- `$color->setHsl(float $h, float $s, float $l): Color` — Set HSL values
- `$color->setHsla(float $h, float $s, float $l, ?float $a = null): Color` — Set HSLA values

**HSV Access:**
- `$color->hsvHue` — HSV hue (0-360, property with getter/setter)
- `$color->hsvSaturation` — HSV saturation (0-1, property with getter/setter)
- `$color->value` — HSV value (0-1, property with getter/setter)
- `$color->hsvValue` — HSV value (0-1, property with getter/setter)
- `$color->getHsvHue(): float` — Get HSV hue
- `$color->setHsvHue(float $h): Color` — Set HSV hue
- `$color->getHsvSaturation(): float` — Get HSV saturation
- `$color->setHsvSaturation(float $s): Color` — Set HSV saturation
- `$color->getHsvValue(): float` — Get HSV value
- `$color->setHsvValue(float $v): Color` — Set HSV value
- `$color->setHsv(float $h, float $s, float $v): Color` — Set HSV values
- `$color->setHsva(float $h, float $s, float $v, ?float $a = null): Color` — Set HSVA values

**Alpha Channel:**
- `$color->alpha` — Alpha channel (0-1, property with getter/setter)
- `$color->getAlpha(): float` — Get alpha channel
- `$color->setAlpha(?float $alpha): Color` — Set alpha channel

**Mode Management:**
- `$color->mode` — Current color mode (readonly property)
- `$color->setMode(Mode $mode): Color` — Set color mode (converts if needed)
- `$color->toRgb(): Color` — Convert to RGB mode
- `$color->toHsl(): Color` — Convert to HSL mode
- `$color->toHsv(): Color` — Convert to HSV mode

**Color Manipulation:**
- `$color->add(Color|string|array|null $color): Color` — Add color values
- `$color->subtract(Color|string|array|null $color): Color` — Subtract color values
- `$color->lighten(float $lightness): Color` — Lighten color
- `$color->darken(float $darkness): Color` — Darken color
- `$color->affectHsl(float $h, float $s, float $l, ?float $a = null): Color` — Adjust HSL values
- `$color->affectHslHue(float $h): Color` — Adjust HSL hue
- `$color->affectHslSaturation(float $s): Color` — Adjust HSL saturation
- `$color->affectHslLightness(float $l): Color` — Adjust HSL lightness
- `$color->affectHsv(float $h, float $s, float $v, ?float $a = null): Color` — Adjust HSV values
- `$color->affectHsvHue(float $h): Color` — Adjust HSV hue
- `$color->affectHsvSaturation(float $s): Color` — Adjust HSV saturation
- `$color->affectHsvValue(float $v): Color` — Adjust HSV value
- `$color->affectAlpha(float $a): Color` — Adjust alpha channel
- `$color->affectContrast(float $amount): Color` — Adjust contrast
- `$color->toMidtone(float $amount = 1.0): Color` — Convert to midtone
- `$color->contrastAgainst(Color|string|array|null $color, float $amount = 0.5): Color` — Adjust contrast against another color
- `$color->getTextContrastColor(): Color` — Get contrasting text color (black or white)

**Output:**
- `$color->toHexString(bool $allowShort = false): string` — Convert to hex string
- `$color->toCssString(): string` — Convert to CSS string (hex or rgba)
- `(string)$color` — String conversion returns CSS string
- `Color::isValidName(string $name): bool` — Check if color name is valid

**ColorStop:**
- `ColorStop::create(ColorStop|string $colorStop): ColorStop` — Create color stop from string
- `new ColorStop(Color|string $color, string|int|null $size)` — Constructor
- `$stop->color` — Color (property with getter/setter)
- `$stop->size` — Size with unit (property with getter/setter, nullable)
- `(string)$stop` — String conversion returns CSS color stop format

## Dependencies

### Decode Labs

- **`decodelabs/coercion`**: Used for type coercion in color parsing (CSS function parsing, percentage handling).
- **`decodelabs/exceptional`**: Used for exception handling throughout the package.
- **`decodelabs/nuance`**: Used for `Dumpable` interface support, enabling debugging and inspection capabilities.

### External

- **PHP**: See `composer.json` for supported PHP versions.

### Optional

- **`decodelabs/lucid`**: Detected at runtime if installed, used for color validation via `Lucid\Processor\Color` and color constraints (`MinLightness`, `MaxLightness`, `MinSaturation`, `MaxSaturation`). If available, colors can be validated and coerced using Lucid's validation system.

## Behaviour & Contracts

### Invariants

- Color components are clamped to valid ranges (RGB: 0-1, HSL/HSV: hue 0-360, saturation/value/lightness 0-1, alpha 0-1).
- Mode conversion preserves color appearance (conversions are mathematically accurate).
- Color instances are mutable (methods modify the instance and return `$this`).
- String conversion always returns a valid CSS color string (or empty string on error).

### Input & Output Contracts

**Color Parsing:**
- `fromString()` accepts: hex strings (`#RGB`, `#RRGGBB`, `#RRGGBBAA`), CSS functions (`rgb()`, `rgba()`, `hsl()`, `hsla()`, `hsv()`, `hsva()`), named colors, or empty string (defaults to black).
- `fromHex()` accepts: hex strings with or without `#` prefix, `0x` prefix, 3-digit (`#RGB`), 6-digit (`#RRGGBB`), or 8-digit (`#RRGGBBAA`) formats.
- `fromName()` accepts: CSS color names (case-insensitive).
- `fromCssDefinition()` parses CSS function syntax with percentage or numeric values.

**Component Access:**
- RGB components are normalized to 0-1 range internally.
- HSL/HSV hue is normalized to 0-360 degrees (wraps around).
- HSL/HSV saturation/value/lightness are normalized to 0-1 range.
- Alpha is normalized to 0-1 range.
- Accessing components in wrong mode automatically converts mode.

**Mode Conversion:**
- `toRgb()` converts from HSL or HSV to RGB.
- `toHsl()` converts from RGB or HSV to HSL (HSV → RGB → HSL).
- `toHsv()` converts from RGB or HSL to HSV (HSL → RGB → HSV).
- Conversions preserve color appearance (mathematically accurate).

**Color Manipulation:**
- `lighten()` and `darken()` operate on HSL lightness.
- `add()` and `subtract()` operate on RGB components.
- `affect*()` methods add to existing values (can exceed normal ranges, then clamped).
- `contrastAgainst()` adjusts lightness to contrast with another color.
- `toMidtone()` moves color toward medium lightness.

**Output Formatting:**
- `toHexString()` returns hex format (`#RRGGBB` or `#RGB` if short format allowed).
- `toCssString()` returns hex format if alpha is 1.0, otherwise `rgba()` format.
- String conversion uses `toCssString()` (returns empty string on error).

## Error Handling

- **Invalid color name**: `fromName()` throws `InvalidArgument` exception for unrecognized color names.
- **Invalid hex string**: `fromHex()` throws `InvalidArgument` exception for invalid hex formats.
- **Invalid CSS definition**: `fromCssDefinition()` throws `InvalidArgument` exception for unparseable CSS functions.
- **HSV to RGB**: `hsvToRgb()` throws `ComponentUnavailable` exception (not yet implemented).
- **String conversion errors**: `__toString()` catches exceptions and returns empty string.

## Configuration & Extensibility

### Custom Color Names

Color names are defined in the `Names` constant array. To add custom names, extend the `Color` class and override the constant (though this is not recommended; prefer using hex or RGB values).

### Custom Color Formats

To add support for custom color formats, extend `Color` and add parsing methods:

```php
class CustomColor extends Color
{
    public static function fromCustomFormat(string $value): Color
    {
        // Parse custom format
        return new self(...);
    }
}
```

### Lucid Integration

Colors can be validated using Lucid constraints:

```php
use DecodeLabs\Lucid\Constraint\Color\MinLightness;
use DecodeLabs\Lucid\Constraint\Color\MaxSaturation;

// Validate color lightness and saturation
$validator->addConstraint(new MinLightness(0.3));
$validator->addConstraint(new MaxSaturation(0.8));
```

## Interactions with Other Packages

- **Coercion**: Used for type coercion in CSS function parsing (converting strings to floats, handling percentages).
- **Nuance**: Used for `Dumpable` interface support, enabling debugging and inspection.
- **Lucid**: Optional integration for color validation and coercion. Provides processor and constraints for color validation.

## Usage Examples

### Basic Color Creation

```php
use DecodeLabs\Spectrum\Color;

// From hex
$color = Color::create('#5AB3CD');

// From CSS name
$color = Color::create('darkblue');

// From CSS function
$color = Color::create('rgba(25,25,25,0.4)');

// From array
$color = Color::create([0.5, 0.3, 0.2, 0.8]);

// Random color
$color = Color::random();
```

### Color Manipulation

```php
use DecodeLabs\Spectrum\Color;

$color = Color::create('#5AB3CD');

// Convert to HSL
$color->toHsl();

// Lighten by 30%
$color->lighten(0.3);

// Set alpha to 50%
$color->setAlpha(0.5);

// Convert to midtone
$color->toMidtone();

// Output as CSS
echo $color; // Converts to appropriate CSS value
```

### Contrast Operations

```php
use DecodeLabs\Spectrum\Color;

$color = Color::create('#5AB3CD');

// Get contrasting color against pink
$contrastColor = $color->contrastAgainst('pink');

// Get text contrast color (black or white)
$textColor = $color->getTextContrastColor();
```

### Format Conversion

```php
use DecodeLabs\Spectrum\Color;
use DecodeLabs\Spectrum\Mode;

$color = Color::create('#FF0000');

// Convert to HSL
$color->toHsl();
echo $color->hue; // 0
echo $color->saturation; // 1
echo $color->lightness; // 0.5

// Convert to HSV
$color->toHsv();
echo $color->hsvHue; // 0
echo $color->hsvSaturation; // 1
echo $color->value; // 1

// Convert back to RGB
$color->toRgb();
echo $color->red; // 1
echo $color->green; // 0
echo $color->blue; // 0
```

### Color Arithmetic

```php
use DecodeLabs\Spectrum\Color;

$color1 = Color::create('#FF0000');
$color2 = Color::create('#00FF00');

// Add colors
$color1->add($color2);
// Result: yellow (#FFFF00)

// Subtract colors
$color1->subtract($color2);
// Result: red (#FF0000)
```

### HSL Manipulation

```php
use DecodeLabs\Spectrum\Color;

$color = Color::create('#5AB3CD');

// Adjust HSL values
$color->affectHsl(
    h: 10,    // Shift hue by 10 degrees
    s: 0.1,   // Increase saturation by 0.1
    l: -0.2   // Decrease lightness by 0.2
);

// Individual adjustments
$color->affectHslHue(30);
$color->affectHslSaturation(0.2);
$color->affectHslLightness(-0.1);
```

### ColorStop

```php
use DecodeLabs\Spectrum\ColorStop;

// Create color stop
$stop = ColorStop::create('red 50%');
echo $stop; // 'rgb(255,0,0) 50%'

// Or with explicit color and size
$stop = new ColorStop('blue', '100px');
echo $stop; // 'rgb(0,0,255) 100px'
```

### Lucid Validation

```php
use DecodeLabs\Lucid\Processor\Color as ColorProcessor;
use DecodeLabs\Lucid\Constraint\Color\MinLightness;
use DecodeLabs\Lucid\Constraint\Color\MaxSaturation;

// Coerce to color
$processor = new ColorProcessor();
$color = $processor->coerce('#FF0000');

// Validate color constraints
$validator->addConstraint(new MinLightness(0.3));
$validator->addConstraint(new MaxSaturation(0.8));
```

## Implementation Notes (for Contributors)

### Color Storage

- Colors are stored internally as three float values (`$a`, `$b`, `$c`) plus alpha.
- The meaning of `$a`, `$b`, `$c` depends on the current mode:
  - RGB: red, green, blue
  - HSL: hue, saturation, lightness
  - HSV: hue, saturation, value
- Mode conversion updates these values accordingly.

### Mode Conversion Algorithms

**RGB to HSL:**
- Lightness: average of min and max RGB values
- Saturation: calculated based on lightness
- Hue: calculated based on which RGB component is maximum

**HSL to RGB:**
- Uses standard HSL to RGB conversion algorithm
- Handles hue wrapping (0-360 degrees)
- Uses helper function `hslHueToRgb()` for hue component conversion

**RGB to HSV:**
- Value: maximum RGB component
- Saturation: calculated based on value
- Hue: calculated based on which RGB component is maximum

**HSV to RGB:**
- Currently throws `ComponentUnavailable` exception (not yet implemented)

### Component Clamping

- RGB components: clamped to 0-1 range
- HSL/HSV hue: wrapped to 0-360 degrees (not clamped, wraps around)
- HSL/HSV saturation/value/lightness: clamped to 0-1 range
- Alpha: clamped to 0-1 range

### CSS Parsing

- CSS functions parsed using regex: `/^(rgb|hsl|hsv)(a?)\((.*)\)/i`
- Percentage values converted to 0-1 range
- RGB values can be 0-255 (converted to 0-1) or percentages
- HSL/HSV values are percentages (converted to 0-1)
- Alpha can be 0-1 or percentage

### Hex Parsing

- Supports 3-digit (`#RGB`), 6-digit (`#RRGGBB`), and 8-digit (`#RRGGBBAA`) formats
- 3-digit format expanded by doubling each digit
- 8-digit format includes alpha channel
- Strips `#` and `0x` prefixes

### Named Colors

- 140+ CSS named colors defined in `Names` constant
- Names are case-insensitive
- Values stored as RGB arrays (0-255) with optional alpha
- Converted to 0-1 range during creation

### Color Manipulation

- `lighten()` and `darken()` operate on HSL lightness
- `add()` and `subtract()` operate on RGB components (can exceed 0-1 range, then clamped)
- `affect*()` methods add to existing values
- `contrastAgainst()` adjusts lightness based on contrast calculation
- `toMidtone()` moves lightness toward 0.5

### Output Formatting

- `toHexString()` formats RGB values as hex
- Short format (`#RGB`) used if all components have matching hex digits
- `toCssString()` uses hex if alpha is 1.0, otherwise `rgba()` format
- RGB values rounded to integers for CSS output

## Testing & Quality

**Current Status:**
- Code quality: 4/5
- README quality: 3/5
- Documentation: 0/5 (no formal docs yet)
- Tests: 0/5 (no test suite yet)

**Testing Considerations:**
- Color parsing should be tested for:
  - Hex strings (3, 6, 8 digit formats)
  - CSS functions (rgb, rgba, hsl, hsla, hsv, hsva)
  - Named colors
  - Edge cases (empty strings, invalid formats)

- Mode conversion should be tested for:
  - RGB ↔ HSL conversion accuracy
  - RGB ↔ HSV conversion accuracy (when implemented)
  - HSL ↔ HSV conversion accuracy
  - Edge cases (grayscale, pure colors, etc.)

- Color manipulation should be tested for:
  - Lighten/darken operations
  - Add/subtract operations
  - HSL/HSV adjustments
  - Contrast operations
  - Midtone conversion

- Component access should be tested for:
  - Automatic mode conversion
  - Component clamping
  - Hue wrapping
  - Alpha handling

- Output formatting should be tested for:
  - Hex string formatting
  - CSS string formatting
  - Short hex format
  - Alpha handling in CSS output

- Edge cases should be tested for:
  - Boundary values (0, 1, 360)
  - Invalid inputs
  - Mode conversion edge cases
  - Color arithmetic edge cases

## Roadmap & Future Ideas

- **HSV to RGB conversion**: Complete HSV to RGB conversion implementation
- **Additional color spaces**: Support for more color spaces (LAB, XYZ, CMYK, etc.)
- **Color palettes**: Tools for generating color palettes and schemes
- **Color distance**: Calculate color distance and similarity
- **Color blending**: Support for color blending modes (multiply, screen, overlay, etc.)
- **Accessibility tools**: Color contrast ratio calculation and WCAG compliance checking
- **Performance optimization**: Caching of conversion results
- **Extended color names**: Support for more color name standards

## References

- Package repository: https://github.com/decodelabs/spectrum
- Composer package: https://packagist.org/packages/decodelabs/spectrum
- CSS Color specification: https://www.w3.org/TR/css-color-4/
- Related packages:
  - `decodelabs/coercion` — Type coercion
  - `decodelabs/exceptional` — Exception handling
  - `decodelabs/nuance` — Debugging and inspection
  - `decodelabs/lucid` — Validation (optional, for color constraints)

