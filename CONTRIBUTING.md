Contributing to twig-a11y-rules
=================================

Thanks for helping improve the project. This short guide explains how to add new rules and tests.

Write a rule
- Create a new rule class under src/Rules/<Domain>/YourRule.php that extends TwigCsFixer\Rules\AbstractRule.
- Implement a minimal process() method that inspects token stream and calls $this->addError(message, $token, 'RuleNamespace.Code') when appropriate.
- Keep rules small and well-scoped. Add a clear identifier code (e.g. `FormLabel.InvalidLabel`).

Evaluate API
- Rules should implement `TwigA11y\Rules\EvaluatableRuleInterface` and expose a public method with the signature:

  public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void

  - This method should perform the rule analysis and emit findings via the $emit callable instead of calling `$this->addError()` directly.
  - The $emit callable will be invoked as $emit(string $message, Token $token, ?string $identifier).
  - `AbstractA11yRule` already forwards `process()` to `evaluate()`, so new rules only need to implement `evaluate()`.
  - Override `emitsWarnings()` when the rule should report warnings instead of errors.

Write tests
- Add a test under tests/Rules/<Domain>/YourRuleTest.php following the existing pattern.
  - Use a DataProvider named provideFixtures returning iterable of [fixturePath, expectedErrors].
  - expectedErrors is an array keyed by the violation identifier (identifier includes file/line info added by the test helper). Example:
    ['YourRule.YourRule.Code:1:1' => 'Helpful human message.']
- Place fixtures in tests/Rules/<Domain>/Fixtures/{valid,invalid}/.

Run tests and static checks locally
- composer install
- ./vendor/bin/phpstan analyse -c phpstan.dist.neon
- composer test

Lint and formatting
- `composer lint` runs the full validation sequence (PHPStan, Rector, and PHP CS Fixer).
- WARNING: `composer lint` is **not** a dry-run — it applies Rector refactors and PHP CS Fixer formatting changes directly to the source files. Commit your work before running it, or use the individual commands below for a read-only check:
  - `composer cs-lint` — check formatting without applying changes.
  - `composer phpstan` — static analysis only.
  - `composer rector -- --dry-run` — preview Rector changes without writing them.

Checklist for PRs
- Tests covering valid and invalid examples included.
- phpstan passes locally.
- Add or update README/roadmap if your rule adds new public behavior.

If you need help or want review before opening a big change, open an issue first.
