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

Then run:

```bash
# Check for violations
vendor/bin/twig-cs-fixer lint /path/to/templates

# Auto-fix where possible
vendor/bin/twig-cs-fixer fix /path/to/templates
```

---

## Available rules

See [`src/Rules/`](src/Rules/) for the full list. The [roadmap](ROADMAP.md) tracks implementation status and priorities.

| Rule | Category | Description |
|---|---|---|
| `ImgAltRule` | Media | `<img>` missing `alt` or empty `alt` without `role="presentation"` |
| `AutoplayRule` | Media | `<video>`/`<audio>` with `autoplay` but without `muted` |
| `ObjectAltRule` | Media | `<object>` without alternative text |
| `BannedTagsRule` | Structure | Disallows `<marquee>` and `<blink>` |
| `ButtonContentRule` | Structure | `<button>` with no text content or `aria-label` |
| `AnchorContentRule` | Structure | `<a>` with no text, `aria-label`, or `title` |
| `HeadingOrderRule` | Structure | Heading levels that skip (e.g. h1 → h3) |
| `HeadingEmptyRule` | Structure | Empty heading elements |
| `LangAttributeRule` | Structure | `<html>` missing `lang` attribute |
| `IframeTitleRule` | Structure | `<iframe>` without `title` attribute |
| `MetaViewportRule` | Structure | `<meta viewport>` with `user-scalable=no` |
| `TabIndexRule` | Aria | `tabindex` value greater than 0 |
| `AriaRoleRule` | Aria | Invalid ARIA `role` value |
| `AriaLabelRule` | Aria | Landmark missing non-empty `aria-label` |
| `AriaHiddenFocusRule` | Aria | Focusable element with `aria-hidden="true"` |
| `AriaRequiredAttrRule` | Aria | Missing required attributes for a given ARIA role |
| `FormLabelRule` | Forms | `<label>` without `for` or non-empty content |
| `InputLabelRule` | Forms | `<input>` without associated `<label>` or `aria-label` |
| `SelectLabelRule` | Forms | `<select>` without associated `<label>` |
| `TextareaLabelRule` | Forms | `<textarea>` without associated `<label>` |
---

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