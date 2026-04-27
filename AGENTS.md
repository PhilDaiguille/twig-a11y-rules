Purpose
All repository-specific guidance an OpenCode session is likely to miss. Keep this short — every line should answer: "Would an agent likely miss this without help?"

Quick facts
- Language: PHP library (PSR-4). Autoload: "TwigA11y\\" => "src/". Tests autoload: "TwigA11y\\Tests\\" => "tests/".
- PHP minimum: 8.2 (CI tests matrix: 8.2, 8.3, 8.4).
- This package ships rules only — it does NOT provide a runnable binary. Use vendor/bin/twig-cs-fixer (from vincentlanglet/twig-cs-fixer).

Setup (safe, repeatable)
- Install deps: `composer install` (CI uses `composer install --prefer-dist --no-progress --no-suggest --optimize-autoloader`). Ensure your PHP version >= 8.2 and required extensions (mbstring, dom when running tests).

Run tests
- Run full test suite: `composer test` (runs phpunit tests --testdox).
- Run with coverage (as CI does): `./vendor/bin/phpunit --configuration phpunit.xml --coverage-text --coverage-clover=coverage.xml`.
- Run a single test file: `./vendor/bin/phpunit path/to/TestFile.php` or use `--filter` to run by test name.

Linting / static checks — exact commands and gotchas
- Composer scripts available (see composer.json):
  - `composer cs-lint` -> `php-cs-fixer fix --dry-run --diff --verbose`
  - `composer cs-fix`  -> `php-cs-fixer fix`
  - `composer phpstan` -> `phpstan analyse`
  - `composer rector`  -> `rector`
  - `composer lint`    -> runs `cs-lint && phpstan && test && rector` (IMPORTANT: this runs `rector` without `--dry-run`, so it can modify files locally)
  - `composer lint:fix` -> runs `cs-fix && rector` (also modifies files)

- Safe CI-like full check (no local modifications):
  composer cs-lint && composer phpstan && composer test && composer rector -- --dry-run
  (CI uses this pattern for rector; prefer adding `--dry-run` locally when you only want to verify.)

Twig lint (rules usage)
- This package provides rules for twig-cs-fixer; create a `.twig-cs-fixer.php` at your repo root to enable them (see README example).
- Run the installed linter against templates (CI command):
  `vendor/bin/twig-cs-fixer lint . --config=.twig-cs-fixer.php --no-cache`
  Note: CI runs the above with `|| true` so lint errors don't fail the job. Locally treat its exit status as you prefer.

Adding / testing rules (required structure)
- Rule code: `src/Rules/{Category}/`
- Tests: `tests/Rules/{Category}/` with PHPUnit test classes.
- Fixtures: valid and invalid `.html.twig` files under `tests/Rules/{Category}/Fixtures/` (tests rely on these fixtures).

CI differences to notice
- CI runs `composer rector -- --dry-run` (lint.yml/tests.yml) while the `composer lint` script will run rector without `--dry-run`. Do NOT assume `composer lint` is read-only.
- CI sets up xdebug for coverage. If you need local coverage run with Xdebug enabled (or the coverage step will fail).

Other small but important facts
- The package requires `vincentlanglet/twig-cs-fixer` (the rules don't include the binary). If you run twig lint locally, install that dependency.
- PHPUnit bootstrap is `vendor/autoload.php` (phpunit.xml). Running tests requires vendor autoload to exist (i.e. run composer install first).

If something is unclear
- Check README.md, composer.json (scripts), phpunit.xml and .github/workflows/* for the authoritative behavior used by CI.
