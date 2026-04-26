Twig A11y Rules
================

Petit projet fournissant des règles d'accessibilité pour les templates Twig.

Prerequis
-
- PHP >= 8.2
- Composer

Installer les dépendances
-
1. Installer les dépendances :

   composer install

Lancer les tests
-
1. Exécuter la suite de tests :

   composer test

Workflow CI
-
- ![Lint](https://img.shields.io/github/actions/workflow/status/PhilDaiguille/twig-a11y-rules/lint.yml?branch=main)
- ![Tests](https://img.shields.io/github/actions/workflow/status/PhilDaiguille/twig-a11y-rules/tests.yml?branch=main)
- [![Coverage Status](https://codecov.io/gh/PhilDaiguille/twig-a11y-rules/branch/main/graph/badge.svg?token=)](https://codecov.io/gh/PhilDaiguille/twig-a11y-rules)

CI workflows
- Lint workflow: `.github/workflows/lint.yml` — executes PHPStan, php-cs-fixer and rector (dry-run) across PHP 8.2/8.3/8.4.
- Tests workflow: `.github/workflows/tests.yml` — runs PHPUnit with coverage and uploads coverage to Codecov across PHP 8.2/8.3/8.4.

Note: The Codecov badge will work automatically for public repositories. For private repositories you may need to set a CODECOV_TOKEN secret in your repository settings.

Contribuer
-
Les contributions sont bienvenues — ouvrez une issue ou une PR.
