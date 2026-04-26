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
Un workflow GitHub Actions est fourni dans `.github/workflows/phpunit.yml`. Il installe PHP (8.2/8.3/8.4), installe les dépendances via Composer et exécute `composer test` sur chaque push et pull request vers `main` / `master`.

Contribuer
-
Les contributions sont bienvenues — ouvrez une issue ou une PR.
