# Roadmap WCAG AA/AAA — Checklist

## Phase 4 — Refactorisations techniques (1 jour)

- [ ] Remplacer pattern "scan once" dupliqué (~12 règles) par override `evaluateOncePerFile()` dans `AbstractA11yRule`
- [ ] Extraire `findAssociatedLabel()` et `extractId()` dans `TokenCollectorTrait` (3 règles forms)
- [ ] Cacher `getFullContent()` en propriété `$cachedContent` (évite O(n²))
- [ ] Créer `AbstractFormFieldLabelRule` pour `Input/Select/TextareaLabelRule`
- [ ] Simplifier `StandardRuleSets::classes()` → accepter `class-string[]` directement
- [ ] Utiliser Rector pour auto-refacto patterns répétitifs
 - [x] Remplacer pattern "scan once" dupliqué (~12 règles) par override `evaluateOncePerFile()` dans `AbstractA11yRule`
 - [x] Extraire `findAssociatedLabel()` et `extractId()` dans `TokenCollectorTrait` (3 règles forms)
 - [x] Cacher `getFullContent()` en propriété `$cachedContent` (évite O(n²))
 - [ ] Créer `AbstractFormFieldLabelRule` pour `Input/Select/TextareaLabelRule`
 - [ ] Simplifier `StandardRuleSets::classes()` → accepter `class-string[]` directement
 - [ ] Utiliser Rector pour auto-refacto patterns répétitifs

## Phase 5 — Corrections bugs (½ jour)

- [ ] `FormLabelRule` : combiner correctement `for` + contenu label
- [ ] `AriaRoleRule` : collecter TOUS les rôles invalides par fichier (pas `return` après le premier)
- [ ] `SkipLinkRule` : clarifier guard `evaluateOncePerFile()`
- [ ] `HeadingOrderRule` : corriger faux positif sur `h2` suivi de `h3` (doit être valide)
- [ ] `DuplicateIdRule` : corriger faux positif sur éléments avec `id`
- [ ] `AnchorContentRule` : corriger faux positif sur `<a>` avec `title` mais pas de contenu textuel (doit être valide)
- [ ] `LangAttributeRule` : corriger faux positif sur `<html lang="">` (doit être invalide)
- [ ] `IframeTitleRule` : corriger faux positif sur `<iframe title="">` (doit être invalide)
- [ ] `MetaViewportRule` : corriger faux positif sur `<meta name="viewport" content="width=device-width, initial-scale=1">` (doit être valide)
- [ ] `TableHeaderRule` : corriger faux positif sur `<th scope="col">` (doit être valide)
- [ ] `ButtonContentRule` : corriger faux positif sur `<button aria-label="Label">` sans contenu textuel (doit être valide)

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
