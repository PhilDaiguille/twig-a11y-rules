# twig-a11y-rules

> Accessibility linting rules for Twig templates, built on top of [`vincentlanglet/twig-cs-fixer`](https://github.com/VincentLanglet/Twig-CS-Fixer).

[![Latest Stable Version](http://poser.pugx.org/phildaiguille/twig-a11y-rules/v)](https://packagist.org/packages/phildaiguille/twig-a11y-rules) [![Total Downloads](http://poser.pugx.org/phildaiguille/twig-a11y-rules/downloads)](https://packagist.org/packages/phildaiguille/twig-a11y-rules) [![Latest Unstable Version](http://poser.pugx.org/phildaiguille/twig-a11y-rules/v/unstable)](https://packagist.org/packages/phildaiguille/twig-a11y-rules) [![License](http://poser.pugx.org/phildaiguille/twig-a11y-rules/license)](https://packagist.org/packages/phildaiguille/twig-a11y-rules) [![PHP Version Require](http://poser.pugx.org/phildaiguille/twig-a11y-rules/require/php)](https://packagist.org/packages/phildaiguille/twig-a11y-rules)
[![QA](https://github.com/PhilDaiguille/twig-a11y-rules/actions/workflows/qa.yml/badge.svg)](https://github.com/PhilDaiguille/twig-a11y-rules/actions/workflows/qa.yml)
[![codecov](https://codecov.io/gh/PhilDaiguille/twig-a11y-rules/branch/main/graph/badge.svg?token=CWK2T8325J)](https://codecov.io/gh/PhilDaiguille/twig-a11y-rules)
---

## What is this?

`twig-a11y-rules` is a standalone package of accessibility rules for Twig templates. It integrates with `twig-cs-fixer` and statically checks your templates for known accessibility issues — missing `alt` attributes, empty buttons, invalid ARIA roles, and more.

> **Note:** Static analysis cannot guarantee full accessibility. Manual testing remains essential.

Inspired by [Deque's Axe Linter](https://axe-linter.deque.com/) and built as a modern successor to the unmaintained [`nielsdeblaauw/twigcs-a11y`](https://github.com/nielsdeblaauw/twigcs-a11y).

---

## Requirements

- PHP >= 8.4
- [`vincentlanglet/twig-cs-fixer`](https://packagist.org/packages/vincentlanglet/twig-cs-fixer) ^4.0

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
use TwigA11y\Rules\Media\ImgAltRule;
use TwigA11y\Rules\Structure\BannedTagsRule;

$config = new Config();

// getRuleset() keeps the default twig-cs-fixer standard in place.
$config->getRuleset()
    ->addRule(new ImgAltRule())
    ->addRule(new BannedTagsRule());

return $config;
```

> Use `$config->getRuleset()` rather than `$config->setRuleset(new Ruleset())`:
> `Config` already loads the `TwigCsFixer` standard into its ruleset, and replacing
> it with an empty `Ruleset` disables all Twig formatting lint. Only pass a fresh
> ruleset to `setRuleset()` if you explicitly want the a11y rules *only*.

### Standards

To make it easier to enable a sensible set of accessibility rules, this package
provides a reusable standard. Rather than adding many rules one-by-one, you can
add the standard to your Ruleset:

```php
use TwigA11y\Standard\A11yStandard;
use TwigCsFixer\Config\Config;

$config = new Config();
$config->getRuleset()->addStandard(new A11yStandard());
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
| `SvgAccessibilityRule` | `<svg>` without accessible name, or `<svg role="img">` without `<title>`, `aria-label`, or `aria-labelledby` | Strict |
| `AudioControlsRule` | `<audio>` without `controls` attribute (WCAG 1.2.1) | Strict |
| `VideoDescriptionTrackRule` | `<video>` without an audio description track (`<track kind="descriptions">`) (WCAG 1.2.5) | Strict |

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
| `DetailsSummaryRule` | `<details>` without a non-empty `<summary>` | Strict |
| `DialogAccessibleNameRule` | `<dialog>` or `role="dialog"/"alertdialog"` without `aria-label` or `aria-labelledby` | Strict |
| `PageHeadingOneRule` | Full-page document without at least one non-empty `<h1>` | Strict |
| `PAsHeadingRule` | `<p>` with `font-weight:bold` or large `font-size` mimicking a heading (WCAG 1.3.1) | Strict |
| `TableCaptionMissingRule` | Data table without a non-empty `<caption>` | Strict |
| `TableDuplicateNameRule` | Table `caption` and `summary` with identical text | Strict |
| `TdHeadersAttrRule` | `<td headers="...">` referencing a non-existent `id` | Strict |
| `AbbrTitleRule` | `<abbr>` without a non-empty `title` providing the expansion (RGAA 9.4) | Strict |
| `DocTypeRule` | Full-page document missing `<!DOCTYPE html>` (RGAA 8.1) | Strict |
| `MetaCharsetRule` | Full-page document missing `<meta charset>` declaration (RGAA 8.8, WCAG 4.1.1) | Strict |
| `TableLayoutRoleRule` | `<table>` without `<th>` that should declare `role="presentation"` or `role="none"` (RGAA 5.3, WCAG 1.3.1) | Strict |
| `SummaryAttributeObsoleteRule` | `<table summary="...">` — obsolete HTML5 attribute, use `<caption>` instead (WCAG 4.1.1) | Strict |

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
| `ButtonTypeRule` | `<button>` without explicit `type` attribute | Strict |
| `InvalidFieldErrorMessageRule` | Field with `aria-invalid` but without `aria-describedby` or `aria-errormessage` | Strict |
| `AriaInputFieldNameRule` | Custom input-role widget without accessible name | Strict |
| `LabelForTargetExistsRule` | `<label for="...">` referencing a non-existent `id` | Strict |
| `PlaceholderOnlyLabelRule` | Form field relying on `placeholder` text without a proper label | Strict |
| `RadioGroupAccessibleNameRule` | Radio groups missing an accessible group label via `<legend>`, `aria-label`, or `aria-labelledby` | Strict |
| `RadioGroupStructureRule` | Multiple radio inputs sharing the same `name` not grouped in `<fieldset>` or `role="radiogroup"` | Strict |
| `OptGroupLabelRule` | `<optgroup>` without a non-empty `label` attribute (WCAG 1.3.1) | Strict |
| `CheckboxGroupStructureRule` | Multiple checkboxes sharing the same `name` not grouped in `<fieldset>` or `role="group"` (WCAG 1.3.1, RGAA 11.7) | Strict |

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
| `AriaControlsIdExistsRule` | `aria-controls` references a missing `id` | Strict |
| `AriaErrorMessageIdExistsRule` | `aria-errormessage` references a missing `id` | Strict |
| `AriaAllowedAttrRule` | `aria-*` attribute not allowed for the given role | Strict |
| `AriaHiddenBodyRule` | `<body aria-hidden="true">` | Strict |
| `RoleButtonTabindexRule` | Non-native element with interactive role (`button`, `link`, `checkbox`, `tab`, …) missing `tabindex="0"` (WCAG 4.1.2, 2.1.1) | Strict |

### Anchor

| Rule | Description | Preset |
|---|---|---|
| `AnchorAccessibleNameRule` | `<a>` without any accessible name (`aria-label`, `aria-labelledby`, inner text, or img alt) — supersedes `AnchorContentRule` in the strict preset | Strict |
| `LinkHrefValidityRule` | `<a>` without `href`, with empty `href`, or using placeholder href values like `#` or `javascript:void(0)` | Strict |

### UI

| Rule | Description | Preset |
|---|---|---|
| `ColorContrastRule` | Insufficient inline text/background contrast (inline `style` only) — **best-effort, inline styles only** | Strict |
| `ScrollableRegionFocusableRule` | Scrollable region not keyboard-focusable | Strict |
| `OutlineNoneWithoutFocusVisibleRule` | `outline:none` or `outline:0` without a `focus-visible` class compensation | Strict |
| `TargetSizeRule` | Interactive element smaller than 24×24 px (inline `style` only) — **best-effort, inline styles only** | Strict |
| `ClickableNonInteractiveRule` | Non-interactive element (`<div>`, `<span>`, …) with `onclick` but without `tabindex` (WCAG 4.1.2, 2.1.1) | Strict |
| `MouseEventKeyboardEquivalentRule` | `onmouseover`/`onmouseout`/`onmousedown` without keyboard equivalent (`onfocus`/`onblur`/`onkeydown`) (WCAG 2.1.1) | Strict |

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

## Versioning & backward compatibility

This package follows [Semantic Versioning](https://semver.org/).

**Public API** (breaking changes only in a major release):

- Rule class names and namespaces under `TwigA11y\Rules\`
- Rule identifiers (`Domain.ShortId`, e.g. `InputLabel.MissingLabel`) — used in
  `// twig-cs-fixer-disable` comments and in your own baselines
- The four standards under `TwigA11y\Standard\` and their constructors

**Not covered** (may change in any release):

- Rule internals, `AbstractA11yRule` protected members, `TokenCollectorTrait`
- The exact wording of error messages
- The `TwigA11y\Template\` classifier internals

**New rules in an existing preset are a minor release.** They may surface new
errors in templates that previously passed. Pin an exact version if your CI
cannot tolerate that.

---

## License

MIT — see [`LICENSE`](LICENSE).
