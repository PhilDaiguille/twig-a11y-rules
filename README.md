# twig-a11y-rules

> Accessibility linting rules for Twig templates, built on top of [`vincentlanglet/twig-cs-fixer`](https://github.com/VincentLanglet/Twig-CS-Fixer).

[![Latest Stable Version](http://poser.pugx.org/phildaiguille/twig-a11y-rules/v)](https://packagist.org/packages/phildaiguille/twig-a11y-rules) [![Total Downloads](http://poser.pugx.org/phildaiguille/twig-a11y-rules/downloads)](https://packagist.org/packages/phildaiguille/twig-a11y-rules) [![Latest Unstable Version](http://poser.pugx.org/phildaiguille/twig-a11y-rules/v/unstable)](https://packagist.org/packages/phildaiguille/twig-a11y-rules) [![License](http://poser.pugx.org/phildaiguille/twig-a11y-rules/license)](https://packagist.org/packages/phildaiguille/twig-a11y-rules) [![PHP Version Require](http://poser.pugx.org/phildaiguille/twig-a11y-rules/require/php)](https://packagist.org/packages/phildaiguille/twig-a11y-rules)
[![CI](https://github.com/PhilDaiguille/twig-a11y-rules/workflows/Tests/badge.svg)](https://github.com/PhilDaiguille/twig-a11y-rules/actions?query=workflow%3ATests)
[![codecov](https://codecov.io/gh/PhilDaiguille/twig-a11y-rules/branch/main/graph/badge.svg?token=CWK2T8325J)](https://codecov.io/gh/PhilDaiguille/twig-a11y-rules)
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
Rules are grouped by category for easier discovery. The **Preset** column indicates the earliest preset that activates the rule.

### Media

| Rule | Description | Preset |
|---|---|---|
| `ImgAltRule` | `<img>` missing `alt`, or empty `alt` without `role="presentation"` | Basic |
| `ObjectAltRule` | `<object>` without alternative text | Recommended |
| `VideoTrackRule` | `<video>` without captions track | Recommended |
| `AutoplayRule` | `<video>` or `<audio>` with `autoplay` but without `muted` | Standard |
| `InputImageAltRule` | `<input type="image">` without a non-empty `alt` (axe: input-image-alt) | Strict |
| `NoAutoplayAudioRule` | `<audio autoplay>` without controls (axe: audio-caption) | Strict |
| `RoleImgAltRule` | Element with `role="img"` without a non-empty `title` | Strict |

### Structure

| Rule | Description | Preset |
|---|---|---|
| `BannedTagsRule` | Disallows `<marquee>` and `<blink>` | Basic |
| `ButtonContentRule` | `<button>` with no text content or `aria-label` | Basic |
| `LangAttributeRule` | `<html>` missing `lang` attribute | Basic |
| `HeadingOrderRule` | Heading levels that skip, for example `h1` to `h3` | Recommended |
| `IframeTitleRule` | `<iframe>` without `title` attribute | Recommended |
| `DuplicateIdRule` | Duplicate `id` values in the same document | Recommended |
| `LandmarkRule` | Missing main landmark (`<main>` or `role="main"`) | Recommended |
| `TableFakeCaptionRule` | First `<td>` used as a visual table caption instead of `<caption>` | Recommended |
| `AnchorContentRule` | `<a>` with no text, `aria-label`, or `title` — warning; superseded by `AnchorAccessibleNameRule` in the strict preset | Standard |
| `HeadingEmptyRule` | Empty heading elements | Standard |
| `MetaViewportRule` | `<meta name="viewport">` with `user-scalable=no` or `maximum-scale` below 2 (WCAG 1.4.4) | Standard |
| `SkipLinkRule` | Missing skip link to main content | Standard |
| `TableHeaderRule` | `<th>` without `scope` attribute, or invalid `scope` value | Standard |
| `EmptyTableHeaderRule` | `<th>` with no text content | Standard |
| `GenericLinkTextRule` | Link text is a known generic phrase such as "click here" or "read more" — warning (WCAG 2.4.4) | Standard |
| `AreaAltRule` | `<area>` without `alt`, or empty `alt` without `role="presentation"` | Strict |
| `DocumentTitleRule` | `<head>` missing a non-empty `<title>` element | Strict |
| `DuplicateAccessKeyRule` | Duplicate `accesskey` values in the same document (WCAG 4.1.1, axe: accesskeys) | Strict |
| `FieldsetLegendRule` | `<fieldset>` without a non-empty `<legend>` | Strict |
| `FrameTitleRule` | `<frame>` without a non-empty `title` (axe: frame-title) | Strict |
| `IframeFocusableContentRule` | `<iframe tabindex="-1">` that contains focusable content | Strict |
| `LangAttributeValueRule` | `lang` attribute with an invalid BCP 47 primary language subtag (WCAG 3.1.1, axe: html-lang-valid) | Strict |
| `LandmarkUniqueRule` | Multiple landmarks of the same type without distinct labels | Strict |
| `ListStructureRule` | `<ul>`/`<ol>` with non-`<li>` children, or `<dl>` missing `<dt>`/`<dd>` | Strict |
| `MetaRefreshRule` | `<meta http-equiv="refresh">` with non-zero timeout (WCAG 2.2.1, axe: meta-refresh) | Strict |
| `NestedInteractiveRule` | `<button>`, `<input>` or `<select>` nested inside `<a>`, or `<a>` inside `<button>` (WCAG 4.1.1, axe: nested-interactive) | Strict |
| `PageHeadingOneRule` | Full-page document without at least one non-empty `<h1>` | Strict |
| `PAsHeadingRule` | `<p>` with `font-weight:bold` or large `font-size` mimicking a heading (WCAG 1.3.1) | Strict |
| `TableDuplicateNameRule` | Table `caption` and `summary` with identical text | Strict |
| `TdHeadersAttrRule` | `<td headers="...">` referencing a non-existent `id` | Strict |

### Forms

| Rule | Description | Preset |
|---|---|---|
| `InputLabelRule` | `<input>` without an associated `<label>` or `aria-label` | Basic |
| `FormLabelRule` | `<label>` without `for` or without non-empty content | Recommended |
| `SelectLabelRule` | `<select>` without an associated `<label>`, `aria-label`, or `aria-labelledby` | Recommended |
| `TextareaLabelRule` | `<textarea>` without an associated `<label>` | Recommended |
| `InputTypeRule` | `<input>` with personal-data type (`email`, `tel`, `name`, `username`, `new-password`, `current-password`) without `autocomplete` (WCAG 1.3.5) | Standard |
| `InputButtonNameRule` | `<input type="submit\|button">` without `value` or `aria-label` | Standard |
| `AutocompleteValidRule` | Invalid `autocomplete` attribute value | Strict |
| `AriaInputFieldNameRule` | Custom input-role widget without accessible name | Strict |

### ARIA

| Rule | Description | Preset |
|---|---|---|
| `TabIndexRule` | `tabindex` value greater than `0` | Standard |
| `AriaRoleRule` | Invalid WAI-ARIA 1.2 `role` value (source: `RoleCatalog`) | Strict |
| `AriaLabelRule` | Landmark missing a non-empty `aria-label` | Strict |
| `AriaHiddenFocusRule` | Focusable element with `aria-hidden="true"` | Strict |
| `AriaRequiredAttrRule` | Missing required attributes for a given ARIA role | Strict |
| `AriaValidAttrRule` | Unknown `aria-*` attribute (checks all 46 WAI-ARIA 1.2 attrs) | Strict |
| `AriaValidAttrValueRule` | Invalid enum value for `aria-*` attributes (covers 21 WAI-ARIA 1.2 enum attrs including `aria-sort`, `aria-live`, `aria-orientation`, `aria-haspopup`, `aria-current`) | Strict |
| `AriaDeprecatedRoleRule` | Deprecated ARIA role used (e.g. `directory`) | Strict |
| `AriaRequiredChildrenRule` | Composite role missing required child roles | Strict |
| `AriaRequiredParentRule` | Child role not wrapped in appropriate parent role | Strict |
| `AriaReferencedIdExistsRule` | `aria-labelledby`/`aria-describedby` references a missing `id` | Strict |
| `AriaAllowedAttrRule` | `aria-*` attribute not allowed for the given role | Strict |
| `AriaHiddenBodyRule` | `<body aria-hidden="true">` | Strict |

### Anchor

| Rule | Description | Preset |
|---|---|---|
| `AnchorAccessibleNameRule` | `<a>` without any accessible name (`aria-label`, `aria-labelledby`, inner text, or img alt) — supersedes `AnchorContentRule` in the strict preset | Strict |

### UI

| Rule | Description | Preset |
|---|---|---|
| `ColorContrastRule` | Insufficient inline text/background contrast (inline `style` only) — **best-effort, inline styles only** | Strict |
| `ScrollableRegionFocusableRule` | Scrollable region not keyboard-focusable | Strict |
| `OutlineNoneWithoutFocusVisibleRule` | `outline:none` or `outline:0` without a `focus-visible` class compensation | Strict |
| `TargetSizeRule` | Interactive element smaller than 24×24 px (inline `style` only) — **best-effort, inline styles only** | Strict |

> **Note on static analysis limits:** some accessibility checks cannot be evaluated statically from template source alone.
> Rules such as `color-contrast-enhanced`, `focus-visible`, `identical-links-same-purpose`, CSS-based `target-size`,
> `aria-labelledby-valid`, `frame-tested`, and `avoid-inline-spacing` require runtime context.
> Use a browser-based tool such as [axe DevTools](https://www.deque.com/axe/) or
> [Lighthouse](https://developer.chrome.com/docs/lighthouse/) alongside this linter for complete coverage.
>
> `ColorContrastRule` and `TargetSizeRule` are **best-effort, inline-only** checks: they only inspect
> `style="..."` attributes present directly in the template source. Contrast ratios and target sizes
> driven by external CSS, CSS variables, or computed styles are **not** detected. These rules reduce
> the chance of obvious mistakes in quick-markup situations; they are not a substitute for a full
> browser-based audit.

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

## Template classification and rule scoping

Some rules are "page-level" and must only run on full HTML pages (to avoid
flagging partials/components). To make this reliable we introduce a simple
TemplateKind classifier used by the rules engine:

- TemplateKind::FullPage: contains both `<html>` and `<body>` and isn't an
  extending child.
- TemplateKind::ChildTemplate: contains `{% extends %}`.
- TemplateKind::ParentTemplate: contains `{% block %}` but no `<html>`.
- TemplateKind::Partial: no `<html>`/`<body>`, typical component fragment.
- TemplateKind::MixedTemplate: `{% extends %}` + own `{% block %}`.
- TemplateKind::TwigUxComponent: uses `{% props %}` (Twig UX style components).

Rules may declare which kinds they support. Page-level rules such as
LangAttributeRule, LandmarkRule, SkipLinkRule and MetaViewportRule are scoped
to FullPage only — this prevents false positives on components and partials.

If you add a new page-level rule, include a partial fixture in the valid
fixtures to document this decision and prevent regressions.

---

## License

MIT — see [`LICENCE`](LICENCE).
