# twig-a11y-rules

> Accessibility linting rules for Twig templates, built on top of [`vincentlanglet/twig-cs-fixer`](https://github.com/VincentLanglet/Twig-CS-Fixer).

[![Latest Stable Version](http://poser.pugx.org/phildaiguille/twig-a11y-rules/v)](https://packagist.org/packages/phildaiguille/twig-a11y-rules) [![Total Downloads](http://poser.pugx.org/phildaiguille/twig-a11y-rules/downloads)](https://packagist.org/packages/phildaiguille/twig-a11y-rules) [![Latest Unstable Version](http://poser.pugx.org/phildaiguille/twig-a11y-rules/v/unstable)](https://packagist.org/packages/phildaiguille/twig-a11y-rules) [![License](http://poser.pugx.org/phildaiguille/twig-a11y-rules/license)](https://packagist.org/packages/phildaiguille/twig-a11y-rules) [![PHP Version Require](http://poser.pugx.org/phildaiguille/twig-a11y-rules/require/php)](https://packagist.org/packages/phildaiguille/twig-a11y-rules)
[![CI](https://github.com/PhilDaiguille/twig-a11y-rules/workflows/Tests/badge.svg)](https://github.com/PhilDaiguille/twig-a11y-rules/actions?query=workflow%3ATests)
[![codecov](https://codecov.io/github/PhilDaiguille/twig-a11y-rules/graph/badge.svg?token=CWK2T8325J)](https://codecov.io/github/PhilDaiguille/twig-a11y-rules)
---

## What is this?

`twig-a11y-rules` is a standalone package of accessibility rules for Twig templates. It integrates with `twig-cs-fixer` and statically checks your templates for known accessibility issues — missing `alt` attributes, empty buttons, invalid ARIA roles, and more.

> **Note:** Static analysis cannot guarantee full accessibility. Manual testing remains essential.

Inspired by [Deque's Axe Linter](https://axe-linter.deque.com/) and built as a modern successor to the unmaintained [`nielsdeblaauw/twigcs-a11y`](https://github.com/nielsdeblaauw/twigcs-a11y).

---

## Requirements

- PHP >= 8.2
- [`vincentlanglet/twig-cs-fixer`](https://packagist.org/packages/vincentlanglet/twig-cs-fixer) >= 3.0

---

## Installation

```bash
composer require --dev phildaiguille/twig-a11y-rules vincentlanglet/twig-cs-fixer
```

This package provides rules only — it does not expose its own binary. Use the `twig-cs-fixer` binary to run linting.

---

## Usage

Create a `.twig-cs-fixer.php` configuration file at the root of your project:

```php
<?php

use TwigCsFixer\Config\Config;
use TwigCsFixer\Ruleset\Ruleset;
use TwigA11y\Rules\Media\ImgAltRule;
use TwigA11y\Rules\Structure\BannedTagsRule;

$ruleset = new Ruleset();
$ruleset->addRule(new ImgAltRule());
$ruleset->addRule(new BannedTagsRule());

$config = new Config();
$config->setRuleset($ruleset);

return $config;
```

### Standards

To make it easier to enable a sensible set of accessibility rules, this package
provides a reusable standard. Rather than adding many rules one-by-one, you can
add the standard to your Ruleset:

```php
use TwigA11y\Standard\A11yStandard;
use TwigCsFixer\Config\Config;
use TwigCsFixer\Ruleset\Ruleset;

$ruleset = new Ruleset();
$ruleset->addStandard(new A11yStandard());

$config = new Config();
$config->setRuleset($ruleset);
$config->allowNonFixableRules(true);

return $config;
```

There are four presets with increasing coverage:

- `A11yBasicStandard`: lowest-noise checks for core HTML issues.
- `A11yRecommendedStandard`: broader structural, media, and form coverage.
- `A11yStandard`: the default balanced preset for most projects.
- `A11yStrict`: every stable rule shipped by this package.

Then run:

```bash
# Check for violations
vendor/bin/twig-cs-fixer lint /path/to/templates

# Auto-fix where possible
vendor/bin/twig-cs-fixer fix /path/to/templates
```

---


## Rules

See [`src/Rules/`](src/Rules/) for the full list.

This ruleset includes automated accessibility checks for common issues in Twig templates.
Rules are grouped by category for easier discovery.

### Media

| Rule            | Description                                                         |
|-----------------|---------------------------------------------------------------------|
| `ImgAltRule`    | `<img>` missing `alt`, or empty `alt` without `role="presentation"` |
| `AutoplayRule`  | `<video>` or `<audio>` with `autoplay` but without `muted`          |
| `ObjectAltRule` | `<object>` without alternative text                                 |
| `VideoTrackRule`| `<video>` without captions track                                    |

### Structure

| Rule                | Description                                        |
|---------------------|----------------------------------------------------|
| `BannedTagsRule`    | Disallows `<marquee>` and `<blink>`                |
| `ButtonContentRule` | `<button>` with no text content or `aria-label`    |
| `AnchorContentRule` | `<a>` with no text, `aria-label`, or `title`       |
| `HeadingOrderRule`  | Heading levels that skip, for example `h1` to `h3` |
| `HeadingEmptyRule`  | Empty heading elements                             |
| `LangAttributeRule` | `<html>` missing `lang` attribute                  |
| `IframeTitleRule`   | `<iframe>` without `title` attribute               |
| `DuplicateIdRule`   | Duplicate `id` values in the same document         |
| `LandmarkRule`      | Missing main landmark                              |
| `MetaViewportRule`  | `<meta name="viewport">` with `user-scalable=no`   |
| `SkipLinkRule`      | Missing skip link to main content                  |
| `TableHeaderRule`   | `<th>` without `scope` attribute                   |

### ARIA

| Rule                   | Description                                       |
|------------------------|---------------------------------------------------|
| `TabIndexRule`         | `tabindex` value greater than `0`                 |
| `AriaRoleRule`         | Invalid ARIA `role` value                         |
| `AriaLabelRule`        | Landmark missing a non-empty `aria-label`         |
| `AriaHiddenFocusRule`  | Focusable element with `aria-hidden="true"`       |
| `AriaRequiredAttrRule` | Missing required attributes for a given ARIA role |

### Forms

| Rule                | Description                                               |
|---------------------|-----------------------------------------------------------|
| `FormLabelRule`     | `<label>` without `for` or without non-empty content      |
| `InputLabelRule`    | `<input>` without an associated `<label>` or `aria-label` |
| `SelectLabelRule`   | `<select>` without an associated `<label>`                |
| `TextareaLabelRule` | `<textarea>` without an associated `<label>`              |
| `InputTypeRule`     | `<input type="email">` without `autocomplete`             |

### UI

| Rule                | Description                                |
|---------------------|--------------------------------------------|
| `ColorContrastRule` | Insufficient inline text/background contrast |

## Contributing

Contributions are welcome — whether it's a new rule, a bug fix, or an improvement to existing ones.

1. Fork the repository and create a branch
2. Follow the TDD workflow described in [`CONTRIBUTING.md`](CONTRIBUTING.md)
3. Open a pull request with a clear description

### Running the test suite locally

```bash
composer install
composer test
```

### Adding a new rule

Each rule lives in `src/Rules/{Category}/` and must have:
- A test class in `tests/Rules/{Category}/`
- Valid and invalid `.html.twig` fixtures in `tests/Rules/{Category}/Fixtures/`

See [`CONTRIBUTING.md`](CONTRIBUTING.md) for the full conventions.

---

## License

MIT — see [`LICENCE`](LICENCE).
