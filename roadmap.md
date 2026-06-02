# Roadmap

## Serie livree (v1.0 — Mai 2026)

83 regles deployeees dans 6 domaines :

| Domaine | Regles | Exemples |
|---------|--------|----------|
| **Aria** (15) | 14 + 1 catalogue | roles, attributs, etats, relations |
| **Forms** (15) | labels, boutons, autocomplete, erreurs | `InputLabel`, `ButtonType`, `RadioGroup*` |
| **Media** (8) | images, video, audio, SVG | `ImgAlt`, `VideoTrack`, `SvgAccessibility` |
| **Structure** (27) | tableaux, iframes, listes, landmarks, titres | `HeadingOrder`, `TableHeader`, `SkipLink` |
| **Ui** (4) | contraste, outline, scroll, cibles | `ColorContrast`, `TargetSize` |
| **Anchor** (2) | nom accessible, href valide | `AnchorAccessibleName`, `LinkHrefValidity` |

### Nouveautes de la serie (9 regles)

1. `SvgAccessibilityRule` — decoration vs information
2. `DetailsSummaryRule` — `<details>` exige `<summary>`
3. `DialogAccessibleNameRule` — `<dialog>` / `role="dialog"` nomme
4. `LabelForTargetExistsRule` — chaque `label[for]` doit referencer un `id`
5. `AriaControlsIdExistsRule` — chaque `aria-controls` doit referencer un `id`
6. `ButtonTypeRule` — `<button>` sans `type` explicite
7. `PlaceholderOnlyLabelRule` — `placeholder` ne remplace pas un label
8. `TableCaptionMissingRule` — tableaux de donnees sans `<caption>`
9. `LinkHrefValidityRule` — liens sans href, href="#", href="javascript:void(0)"

### Ce qui a ete fait

- integration dans les 4 presets (Basic / Recommended / Standard / Strict)
- fixtures valides / invalides pour chaque regle
- tests unitaires avec PHPUnit 11 et `{# N errors #}`
- mise a jour des tests de standards
- mise a jour de la documentation README
- pipeline CI (PHPStan, PHP CS Fixer, Rector, Infection)
- classification des templates (FullPage / Partial / etc.) pour reduire les faux positifs
- deduplication inter-tokens et inter-fichiers avec cache borne

## Architecture technique

```
src/Rules/
  AbstractA11yRule.php    # Classe de base : kind-guard, dedup, caching
  TokenCollectorTrait.php # Helpers : collectTag, safePregMatch, IDs, etc.
  EvaluatableRuleInterface.php  # Interface evaluate(tokens, index, emit)

src/Standard/
  StandardRuleSets.php    # Mapping centralise regle -> preset
  A11y{Basic,Recommended,Standard,Strict}.php  # Presets cumulatifs
```

### Principe de fonctionnement

1. `AbstractA11yRule::process()` est appelee par Twig-CS-Fixer pour chaque token
2. Des le premier token (index 0), la template est classifiee via `TemplateClassifier` et mise en cache
3. Si le `TemplateKind` n'est pas supporte par la regle, tous les tokens sont ignores
4. Les regles "full-page" (`evaluateOncePerFile()`) ne traitent que le token 0
5. Les regles "per-token" sont evaluees pour chaque token texte
6. L'emetteur deduplique les messages identiques pour un meme fichier

## Pistes futures (retard technique)

### Court terme

- **Extraction de `fakeTokenForLine()`** — actuellement duplique dans 9 fichiers. Deplacer dans `AbstractA11yRule`.
- **Hook `evaluateStart()`** — remplacer le pattern `if (0 === $tokenIndex)` repandu dans 8 regles par une methode dediee dans la classe de base.
- **Uniformisation des message IDs** — convention `{RuleName}.{SpecificIssue}` (actuellement `ButtonContent.MissingContent`, `AnchorContent.Warning.LinkName`, `ImgAlt.MissingAlt` — pas toujours coherent).
- **`openingProvidesLabel()` duplique** — `PlaceholderOnlyLabelRule` recopie la logique de `AbstractFormFieldLabelRule`. Mutualiser dans le trait.

### Moyen terme

- **Passe tableaux avances** — renforcer les controles : coherence `th`/`id`/`headers`, patterns mal structurés (colspan/rowspan mal alignés, `th` sans `scope`, tableaux de donnees sans `<thead>`/`<tbody>`).
- **Passe forms modernes** — validation `required` sur widgets custom, coherence etats d'erreur + ARIA, couverture plus fine des groupes `fieldset`/`radiogroup`.
- **Extraction `RoleCatalog`** — scinder les donnees de roles et attributs autorises dans un fichier YAML/JSON pour faciliter la maintenance.
- **Regles inline-style** — `ColorContrastRule`, `TargetSizeRule` ne lisent que les styles inline. Enrichir avec une analyse CSS compilee (post-processeur optionnel).

### Long terme

- Support d'analyse cross-fichier (detection de `id` manquants dans un projet entier)
- Plugin IDE / VS Code base sur le package
- Traduction des messages d'erreur (i18n)
- Benchmark et profiling pour les gros projets

## Evolution du codebase

- PHP 8.2 minimum, dependance unique `vincentlanglet/twig-cs-fixer ^3.14`
- PSR-4 : `TwigA11y\` → `src/`, `TwigA11y\Tests\` → `tests/`
- Tests : PHPUnit 11 avec `--testdox`, fixtures `valid/` + `invalid/`
- Qualite : PHPStan niveau max, PHP CS Fixer, Rector, Infection (mutation testing)
