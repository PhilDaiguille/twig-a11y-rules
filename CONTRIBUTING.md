Contributing to twig-a11y-rules
=================================

Thanks for helping improve the project. This short guide explains how to add new rules and tests.

Write a rule
- Create a new rule class under src/Rules/<Domain>/YourRule.php that extends TwigCsFixer\Rules\AbstractRule.
- Implement a minimal process() method that inspects token stream and calls $this->addError(message, $token, 'RuleNamespace.Code') when appropriate.
- Keep rules small and well-scoped. Add a clear identifier code (e.g. `FormLabel.InvalidLabel`).

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
