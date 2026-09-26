# Contributing

Thanks for helping! Bug reports about **false positives** are the most useful
contribution: please open an issue with the smallest Twig template that
triggers the wrong error.

## Setup

```bash
composer install
composer lint      # cs-lint + phpstan + tests + rector, read-only
composer lint:fix  # applies rector + php-cs-fixer
```

## Writing a rule

- One rule per file in `src/Rules/<Domain>/<Name>Rule.php`, extending
  `TwigA11y\Rules\AbstractA11yRule`.
- Implement `evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void`
  and report through `$emit($message, $token, 'ShortId')`, never
  `addError()` / `addWarning()`. `$emit` deduplicates and routes to warning or
  error depending on the `emitAsWarning` constructor argument.
- Page-level checks (`<html>`, `<title>`, landmarks…): return `true` from
  `evaluateOncePerFile()`, reset state in `evaluateStart()`, and limit
  `supportedKinds()` to `TemplateKind::FullPage` so partials are not flagged.

## Writing tests

- `tests/Rules/<Domain>/<Name>RuleTest.php` with a `provideFixtures()` data
  provider, fixtures in `Fixtures/valid/` and `Fixtures/invalid/`.
- Every `invalid/` fixture starts with `{# N errors #}`.
- Real-world Symfony markup (`form_row()`, `<twig:Component>`, `include()`)
  belongs in `valid/` when it must not be reported.

## Pull request checklist

- [ ] Valid and invalid fixtures
- [ ] `composer lint` passes
- [ ] Entry under `Unreleased` in `CHANGELOG.md`
- [ ] New rule in a preset? Update `src/Standard/StandardRuleSets.php` **and**
      the rules table in `README.md`
