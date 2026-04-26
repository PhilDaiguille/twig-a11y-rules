Contributing to twig-a11y-rules
=================================

Thanks for helping improve the project. This short guide explains how to add new rules and tests.

Write a rule
- Create a new rule class under src/Rules/<Domain>/YourRule.php that extends TwigCsFixer\Rules\AbstractRule.
- Implement a minimal process() method that inspects token stream and calls $this->addError(message, $token, 'RuleNamespace.Code') when appropriate.
- Keep rules small and well-scoped. Add a clear identifier code (e.g. `FormLabel.InvalidLabel`).

Evaluate API (optional, recommended for delegable rules)
- If your rule's logic should be reusable by a meta-rule (like AllInOneRule), implement a public method with the signature:

  public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void

  - This method should perform the same analysis as process() but emit findings via the $emit callable instead of calling $this->addError directly.
  - The $emit callable will be invoked as $emit(string $message, Token $token, ?string $identifier).
  - Keep process() for compatibility; it should call evaluate() and forward emissions to $this->addError / $this->addWarning. Example:

  protected function process(int $tokenIndex, Tokens $tokens): void
  {
      $emit = function (string $message, Token $token, ?string $id = null) {
          $this->addError($message, $token, $id);
      };

      $this->evaluate($tokens, $tokenIndex, $emit);
  }

  This pattern allows a meta-rule to instantiate your rule and call evaluate() to aggregate multiple rules without duplicating logic.

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

Checklist for PRs
- Tests covering valid and invalid examples included.
- phpstan passes locally.
- Add or update README/roadmap if your rule adds new public behavior.

If you need help or want review before opening a big change, open an issue first.
