Twig A11y Rules
================

Accessibilité pour les templates Twig — ensemble de règles (rules) pour être utilisées avec `vincentlanglet/twig-cs-fixer`.

[![PHP Version](https://poser.pugx.org/phildaiguille/twig-a11y-rules/require/php)](https://packagist.org/packages/phildaiguille/twig-a11y-rules)
[![Latest Stable Version](https://poser.pugx.org/phildaiguille/twig-a11y-rules/v)](https://github.com/PhilDaiguille/twig-a11y-rules/releases/latest)
[![License](https://poser.pugx.org/phildaiguille/twig-a11y-rules/license)](https://github.com/PhilDaiguille/twig-a11y-rules/blob/main/LICENCE)
[![Actions Status](https://github.com/PhilDaiguille/twig-a11y-rules/workflows/Test/badge.svg)](https://github.com/PhilDaiguille/twig-a11y-rules/actions?query=workflow%3ATest)

Installation
------------

### Depuis Composer (recommandé)

Ajouter la librairie et `twig-cs-fixer` en tant que dépendances de développement :

```bash
composer require --dev phildaiguille/twig-a11y-rules vincentlanglet/twig-cs-fixer
```

Ensuite vous pouvez réutiliser le binaire fourni par `twig-cs-fixer` :

```bash
vendor/bin/twig-cs-fixer lint /chemin/vers/templates
vendor/bin/twig-cs-fixer fix  /chemin/vers/templates
```

Note : ce paquet n'expose pas de binaire propre (`bin`) — il fournit des règles pour `twig-cs-fixer`. Installez `vincentlanglet/twig-cs-fixer` et activez les règles via une configuration (voir ci-dessous).

### En PHAR

Il est possible d'utiliser `twig-cs-fixer` en PHAR (voir la documentation du projet `vincentlanglet/twig-cs-fixer`). Ces règles étant un package PHP, l'approche PHAR demandera d'autoloader ce package ou d'inclure son autoload.

Usage
-----

Deux façons courantes d'utiliser ces règles :

1) Intégrer les règles dans la configuration de `twig-cs-fixer` (recommandé)

Créez un fichier de configuration PHP pour `twig-cs-fixer` qui retourne un `Ruleset` et y enregistrez les règles que vous voulez activer. Exemple minimal :

```php
<?php
use TwigCsFixer\Ruleset\Ruleset;
use TwigA11y\Rules\Structure\BannedTagsRule;
use TwigA11y\Rules\Media\ImgAltRule;

$ruleset = new Ruleset();
$ruleset->addRule(new BannedTagsRule());
$ruleset->addRule(new ImgAltRule());

return $ruleset;
```

Après cela, lancez `vendor/bin/twig-cs-fixer lint` ou `fix` et les règles d'accessibilité seront prises en compte.

2) Utiliser le linter programmatique

Si vous préférez exécuter la vérification depuis un script PHP (ou intégrer dans un outil), vous pouvez créer un script qui assemble un `Ruleset` et utilise le `Linter` de `twig-cs-fixer`. Les tests du projet montrent des exemples (voir `tests/Rules/*/*RuleTest.php`).

Exemples de règles disponibles
-----------------------------

Les règles couvrent plusieurs domaines (Structure, Forms, Aria, Media, ...). Voir `src/Rules/` pour la liste complète. Le roadmap (`roadmap.md`) donne un aperçu de l'état d'implémentation et de priorité.

Tests et développement
----------------------

Pré-requis : PHP >= 8.2 et Composer

Installer les dépendances :

```bash
composer install
```

Lancer les tests :

```bash
composer test
```

Checks & lint:

```bash
composer lint
```

Contribuer
---------

Les contributions sont les bienvenues : ouvrez une issue ou une PR. Voir `CONTRIBUTING.md` pour les conventions d'ajout d'une règle et des tests.
