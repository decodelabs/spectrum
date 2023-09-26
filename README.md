# Spectrum

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/spectrum?style=flat)](https://packagist.org/packages/decodelabs/spectrum)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/spectrum.svg?style=flat)](https://packagist.org/packages/decodelabs/spectrum)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/spectrum.svg?style=flat)](https://packagist.org/packages/decodelabs/spectrum)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/decodelabs/spectrum/integrate.yml?branch=develop)](https://github.com/decodelabs/spectrum/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/spectrum?style=flat)](https://packagist.org/packages/decodelabs/spectrum)

### Parse and manipulate colors

Spectrum offers a simple but powerful system for parsing, inspecting, manipulating and exporting colors.

_Get news and updates on the [DecodeLabs blog](https://blog.decodelabs.com)._

---

## Installation

```bash
composer require decodelabs/spectrum
```

## Usage

Load and manipulate any color with ease through RGB, HSL and HSV formats.

```php
use DecodeLabs\Spectrum\Color;

$color = Color::create('#5AB3CD');
$color = Color::create('darkblue');
$color = Color::create('rgba(25,25,25,0.4)');
$color = Color::random();

$color->toHsl();
$color->lighten(0.3); // 30% lighter
$color->setAlpha(0.5); // 50% opacity
$color->toMidtone(); // Medium saturation and lightness

echo $color; // Converts to appropriate CSS value

$contrastColor = $color->contrastAgainst('pink');
$textColor = $color->getTextContrastColor();
```

## Licensing
Spectrum is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
