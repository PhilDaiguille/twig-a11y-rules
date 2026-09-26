# AGENTS.md

Guide for humans and coding agents working on `phildaiguille/twig-a11y-rules`.

## What this is

Accessibility (WCAG) rules for [`vincentlanglet/twig-cs-fixer`](https://github.com/VincentLanglet/Twig-CS-Fixer) ^4.
A rule library only: no binary, no runtime dependency besides twig-cs-fixer.
Primary audience: Symfony / Twig / Twig UX projects. PHP >= 8.4.

## Philosophy

Follow the conventions of the ecosystem we plug into:

- **Twig-CS-Fixer**: one rule = one responsibility = one file. The reported id
  is `<RuleShortName>.<messageId>` (short name = class name without `Rule`), and
  users silence it with `{# twig-cs-fixer-disable-line InputLabel.MissingLabel #}`.
  twig-cs-fixer only understands **two** dot-separated segments, so the
  `messageId` passed to `$emit` must be a single segment (`'MissingLabel'`).
  Rule options must be exposed through `ConfigurableRuleInterface` so the
  cache and `Ruleset` see them. A11y rules are non-fixable
  (`allowNonFixableRules(true)`).
- **No false positives over coverage.** A noisy rule gets disabled and helps
  nobody. When unsure whether markup is wrong, don't report it, or report a
  warning.
- **Symfony**: SemVer with the BC promise in the README ("Versioning & backward
  compatibility"). Deprecate in a minor (`#[\Deprecated]`), remove in a major.
  Keep-a-Changelog `CHANGELOG.md`. No new runtime dependency.
- **Twig**: never assume a template is a full page. `{% extends %}`,
  `{% block %}`, `include()`, `form_row()`/`form_widget()` and
  `<twig:Component>` are normal inputs, not errors.

## Commands

| Command | What it does |
|---|---|
| `composer test` | PHPUnit with `--testdox` |
| `composer phpstan` / `composer cs-lint` / `composer rector` | static analysis / CS dry-run / Rector dry-run |
| `composer lint` | cs-lint + phpstan + test + rector (all read-only) |
| `composer lint:fix` | `rector:apply && cs-fix` (writes files) |
| `composer infection` | mutation testing |
| `make ci` | `composer lint && composer test` |

Single test: `vendor/bin/phpunit tests/Rules/Structure/HeadingOrderRuleTest.php --testdox`

## Architecture

1. **`src/Rules/<Domain>/`** (`Anchor`, `Aria`, `Forms`, `Media`, `Structure`, `Ui`):
   rules extend `AbstractA11yRule`, whose `process()` is `final` and delegates to
   `evaluate(Tokens $tokens, int $tokenIndex, callable $emit)`. The base class
   handles template-kind filtering, per-file caching, message dedup (bounded
   maps) and warning-vs-error routing (`$emitAsWarning`). Always report
   through `$emit`, never `addError`/`addWarning`.
   - Page-level scans: `evaluateOncePerFile(): true`, reset per-file state in
     `evaluateStart()`.
   - `TokenCollectorTrait` (`collectTag`, `collectUntil`, `safePregMatch`)
     rebuilds HTML from the Twig token stream. Work at that regex level, don't
     add a DOM parser.
   - Report each violation where it is: `tokenAtOffset()` for a regex offset
     (`PREG_OFFSET_CAPTURE`) on `getFullContent()`, not `$tokens->get(0)`.
     Never make ids unique with suffixes (`Invalid#2`): `$emit` dedups by
     message + id + position, so distinct positions are enough.
2. **`src/Template/`**: `TemplateClassifier` labels each file with a
   `TemplateKind` (`FullPage`, `ChildTemplate`, `ParentTemplate`, `Partial`,
   `MixedTemplate`, `TwigUxComponent`). Page-level rules override
   `supportedKinds()` to `FullPage` and must ship a partial fixture in `valid/`.
3. **`src/Standard/`**: `StandardRuleSets` is the single source of truth for
   the presets (`A11yBasicStandard` ⊂ `A11yRecommendedStandard` ⊂ `A11yStandard`
   ⊂ `A11yStrict`). Changing a preset means editing `StandardRuleSets` **and**
   the README rules table.

## Tests

- `tests/Rules/` mirrors `src/Rules/`; each rule test has a `provideFixtures()`
  provider over `Fixtures/valid/` and `Fixtures/invalid/`.
- Every `invalid/` fixture declares `{# N errors #}` on line 1.
- Any real Symfony markup that must stay silent goes into `valid/`.
- Cross-cutting: `tests/Rules/EvaluateOncePerFileConsistencyTest.php`,
  `tests/Standard/StandardRuleSetsTest.php`, `tests/ConfigExampleTest.php`
  (README config snippets).

## Before a PR / release

- `composer lint` green.
- `CHANGELOG.md` entry under `Unreleased` (credit external contributors).
- Preset changed: `StandardRuleSets` + README table.
- Never create or push tags by hand: releases go through release-drafter
  (`vX.Y.Z`). The current line is `0.9.x`; `1.0.0` is not released yet.
- Commits: conventional, imperative (`feat(forms): ...`, `fix(aria): ...`).

## References

- Twig-CS-Fixer custom rules: https://github.com/VincentLanglet/Twig-CS-Fixer/blob/main/docs/custom_rules.md
- WCAG 2.2 quick reference: https://www.w3.org/WAI/WCAG22/quickref/
