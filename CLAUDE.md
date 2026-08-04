# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

`AGENTS.md` holds the fuller repository guidelines (structure, rule/test conventions, commit style) — read it too. Note it is slightly stale on requirements: `composer.json` now requires **PHP >= 8.4**, `vincentlanglet/twig-cs-fixer ^4.0`, PHPUnit ^13.

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

This is a rule library for `twig-cs-fixer`; it ships no binary. Three layers:

1. **`src/Rules/<Domain>/`** — one rule per file (`Anchor`, `Aria`, `Forms`, `Media`, `Structure`, `Ui`). Rules extend `AbstractA11yRule`, whose `process()` is `final` and delegates to `evaluate(Tokens $tokens, int $tokenIndex, callable $emit)`. The base class handles template-kind filtering, per-file caching, dedup of emitted messages (bounded maps), and error-vs-warning routing via the constructor's `$emitAsWarning`. Always report through `$emit`, never `addError`/`addWarning`.
   - `evaluateOncePerFile(): true` for page-level scans; reset per-file state in `evaluateStart()`, not via `0 === $tokenIndex` guards (`shouldSkipByTokenIndex()` is deprecated and a no-op).
   - `TokenCollectorTrait` (`collectTag`, `collectUntil`, `safePregMatch`) is how rules reassemble HTML from the Twig token stream — these rules parse HTML out of tokens with regex, so work at that level rather than reaching for a DOM parser.
   - `fakeTokenForLine()` when the line comes from a regex offset instead of a real token.

2. **`src/Template/`** — `TemplateClassifier` labels each file with a `TemplateKind` (`FullPage`, `ChildTemplate`, `ParentTemplate`, `Partial`, `MixedTemplate`, `TwigUxComponent`). Page-level rules (`LangAttributeRule`, `LandmarkRule`, `SkipLinkRule`, `MetaViewportRule`, …) override `supportedKinds()` to `FullPage` only, so partials don't get false positives. New page-level rules must ship a partial fixture under `valid/` to lock that in.

3. **`src/Standard/`** — `StandardRuleSets` is the single source of truth listing rule instances per tier; `A11yBasicStandard` / `A11yRecommendedStandard` / `A11yStandard` / `A11yStrict` are thin wrappers over it. Adding a rule to a preset means editing `StandardRuleSets` **and** the README rules table (which carries the preset column).

## Tests

`tests/Rules/` mirrors `src/Rules/`. Each rule test uses a `provideFixtures()` data provider over `Fixtures/valid/` and `Fixtures/invalid/`. Every `invalid/` fixture must declare `{# N errors #}` on line 1. Cross-cutting suites: `tests/Rules/EvaluateOncePerFileConsistencyTest.php`, `tests/Standard/StandardRuleSetsTest.php`, `tests/ConfigExampleTest.php` (verifies the README config snippets still work).

Rule identifiers are `Domain.ShortId` (e.g. `InputLabel.MissingLabel`), but the `messageId` passed at the call site is the short unprefixed part only.
