# Roadmap WCAG AA/AAA — Checklist

## Phase 2 — Règles moyenne priorité (1 jour)

 - [x] Valider valeurs `autocomplete` officielles WCAG 1.3.5 (`autocomplete-valid`)
 - [x] `<fieldset>` sans `<legend>` non vide (`fieldset-legend`)
 - [x] `<svg role="img">` sans `<title>` (`role-img-alt`)
 - [x] ARIA attrs non autorisés par rôle (`aria-allowed-attr`)
 - [x] `<ul>`/`<ol>` avec enfants non `<li>` (`list-structure`) + `<dl>` sans `<dt>`/`<dd>`
 - [x] `overflow:scroll`/`auto` inline sans `tabindex` (`scrollable-region-focusable`)

## Phase 3 — Règles complémentaires + AAA (½ jour)

- [ ] Éléments interactifs `<24px` inline (`target-size`, WCAG 2.5.8 AA)
- [ ] `<audio autoplay>` sans `controls` (`no-autoplay-audio`)
- [ ] `landmark-unique`, `avoid-inline-spacing` et autres axe-core manquants

## Phase 4 — Refactorisations techniques (1 jour)

- [ ] Remplacer pattern "scan once" dupliqué (~12 règles) par override `evaluateOncePerFile()` dans `AbstractA11yRule`
- [ ] Extraire `findAssociatedLabel()` et `extractId()` dans `TokenCollectorTrait` (3 règles forms)
- [ ] Cacher `getFullContent()` en propriété `$cachedContent` (évite O(n²))
- [ ] Créer `AbstractFormFieldLabelRule` pour `Input/Select/TextareaLabelRule`
- [ ] Simplifier `StandardRuleSets::classes()` → accepter `class-string[]` directement
- [ ] Utiliser Rector pour auto-refacto patterns répétitifs

## Phase 5 — Corrections bugs (½ jour)

- [ ] `FormLabelRule` : combiner correctement `for` + contenu label
- [ ] `AriaRoleRule` : collecter TOUS les rôles invalides par fichier (pas `return` après le premier)
- [ ] `SkipLinkRule` : clarifier guard `evaluateOncePerFile()`

## Phase 6 — Tests manquants + TDD (1 jour)

- [ ] Tests : input `aria-labelledby`, rôles ARIA multiples, Twig expressions dans attrs, réutilisation instance
- [ ] Tests paramétrés `TemplateClassifier` (extends sans block, etc.)
- [ ] Tests intégration multi-règles + warnings vs erreurs (`AnchorContentRule`)
- [ ] Test régression duplicate landmarks par règle "once per file"
- [ ] `DataProvider` `#[DataProvider('invalidCases')]` → `testInvalid(fixture, expectedErrors)`

## Phase 7 — CI / Workflow (½ jour)

- [ ] Fusionner `ci.yml` / `lint.yml` / `tests.yml` → `qa.yml` avec `workflow_call`
- [ ] Fixer `actions/checkout@v4` (remplacer v6 incorrect)
- [ ] `Rector --dry-run` partout (local + CI)
- [ ] Décider `composer.lock` : ne pas committer pour une librairie
- [ ] Ajouter job mutation testing (Infection) en non-bloquant
- [ ] Makefile : cibles `make test` / `lint` / `fix` / `ci`
