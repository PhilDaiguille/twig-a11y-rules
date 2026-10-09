# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## Unreleased

### Fixed

- `AriaRole` accepts DPUB-ARIA (`doc-*`) and Graphics ARIA (`graphics-*`) roles (#32).

### Changed

- Tooling: [Mago](https://mago.carthage.software/) replaces php-cs-fixer as
  formatter and linter, and runs as a second analyzer next to PHPStan
  (`composer fmt`, `composer mago:lint`, `composer mago:analyze`). `mago guard`
  enforces the layering and rule conventions, and the analyzer baseline fails CI
  when stale. Dev-only, no change for users of the library.

## 1.0.0 - 2026-09-26

### Changed (BC break)

- Violation identifiers are now `<Rule>.<MessageId>`, e.g. `InputLabel.MissingLabel`,
  as the README always documented. They used to be reported as
  `InputLabel.InputLabel.MissingLabel`, which `{# twig-cs-fixer-disable-line … #}`
  comments could not target. Update any disable comment or baseline that used
  the old three-part form. Where the old middle part differed from the rule
  name, the new id uses the rule name: `Anchor.AccessibleName` →
  `AnchorAccessibleName.AccessibleName`, `LinkHref.MissingHref` →
  `LinkHrefValidity.MissingHref`, etc.
- Ids no longer get a `#2`, `#3`… suffix for repeated violations.
- `DuplicateIdRule` reports every repeated occurrence of an id, at its position.
- Removed `AbstractA11yRule::shouldSkipByTokenIndex()` (deprecated no-op).

### Added

- `AbstractA11yRule` implements twig-cs-fixer's `ConfigurableRuleInterface`, so
  `emitAsWarning` invalidates the cache and the same rule can be registered as
  warning and as error.
- `AbstractA11yRule::tokenAtOffset()`; `fakeTokenForLine()` accepts a column.
- README sections for Symfony projects and for silencing a violation.

### Fixed

- Two identical violations in one file (e.g. two `<img>` without `alt`) were
  reported once; each is now reported.
- Page-level scans (`AriaRole`, `AriaAllowedAttr`, `DuplicateId`, `HeadingOrder`,
  `EmptyTableHeader`, `GenericLinkText`, `PAsHeading`, `TableFakeCaption`,
  `TableHeader`, …) reported every violation at line 1; they now point to the
  offending element's line and column.
- `PageHeadingOneRule` no longer requires an `<h1>` in a layout
  (`base.html.twig`) whose content comes from child blocks.

## 0.9.1 - 2026-09-26

### Fixed

- `FormLabelRule` no longer misses an empty or orphan `<label>` that follows a
  valid one, and accepts labels whose text is wrapped in inline elements such as
  `<span>` (#22, thanks @Simbiat).

## 0.9.0 - 2026-08-04

Release candidate for 1.0.0. The rule identifiers and the standards under
`TwigA11y\Standard\` follow the backward-compatibility policy documented in the
README.

### Added

- 87 accessibility rules across six domains: `Anchor` (2), `Aria` (15),
  `Forms` (16), `Media` (9), `Structure` (39), `Ui` (6).
- Four cumulative presets, each a thin wrapper over `StandardRuleSets`:
  `A11yBasicStandard` (5 rules), `A11yRecommendedStandard` (15),
  `A11yStandard` (26), `A11yStrict` (87).
- `TemplateClassifier` and `TemplateKind`: page-level rules
  (`LangAttributeRule`, `LandmarkRule`, `SkipLinkRule`, `MetaViewportRule`, …)
  only run on full pages, so partials and Twig UX components no longer produce
  false positives.
- `AbstractA11yRule` base class with per-file caching, message deduplication
  and error-vs-warning routing via `$emitAsWarning`.
- Versioning and backward-compatibility policy in the README.

### Changed

- Requires PHP >= 8.4 and `vincentlanglet/twig-cs-fixer` ^4.0 (was PHP 8.2 /
  twig-cs-fixer 3.0, which the README still advertised).

### Removed

- `RoleImgAltRule`: it was in no preset and fully subsumed by
  `SvgAccessibilityRule`, which also accepts `aria-label` and
  `aria-labelledby` as accessible names instead of requiring `<title>`.

### Fixed

- False positives on markup where a tag opens in the middle of a text token
  (`<li><a href="/">`, `<div><select id="s">`): the tag collector stopped on the
  `>` of the *previous* tag and read an attribute-less fragment. Affected
  `LinkHrefValidityRule`, `ButtonTypeRule`, `IframeTitleRule`, `FormLabelRule`,
  `InputLabelRule`, `SelectLabelRule`, `TextareaLabelRule`, `OptGroupLabelRule`
  and ten others.
- `AriaLabelRule` no longer reports a landmark whose `aria-label` is written
  *before* its `role` attribute.
- `ListStructureRule` inspected every descendant of a `<ul>` instead of its
  direct children, so any `<ul><li><a>…</a></li></ul>` was reported. It now also
  actually checks `<ol>`, which its message always claimed.
- `ButtonTypeRule` reported buttons outside of any `<form>`, where a missing
  `type` is harmless — its message already said "inside a form".
- `InputLabelRule` no longer requires a `<label>` on `type="submit|reset|button|image"`,
  whose accessible name comes from `value`/`alt` and is checked by
  `InputButtonNameRule` and `InputImageAltRule`.
- Broken CI badge and `LICENCE` link in the README.

## 0.1.0 - 2026-04-26

- First public release: initial rule set and tests.
