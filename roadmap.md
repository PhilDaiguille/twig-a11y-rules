# Roadmap

## Serie livree

Le repo a maintenant 9 nouvelles regles ajoutees dans cette serie :

1. `SvgAccessibilityRule`
2. `DetailsSummaryRule`
3. `DialogAccessibleNameRule`
4. `LabelForTargetExistsRule`
5. `AriaControlsIdExistsRule`
6. `ButtonTypeRule`
7. `PlaceholderOnlyLabelRule`
8. `TableCaptionMissingRule`
9. `LinkHrefValidityRule`

## Ce qui a ete fait

- integration dans les presets
- fixtures valides / invalides
- tests unitaires
- mise a jour des tests de standards
- mise a jour de la documentation README

## Suite logique

La suite la plus logique, si on continue, c'est soit :

- une passe `tableaux avances`
- soit une passe `forms modernes` avec des regles sur `required`, erreurs, `aria-invalid`, `fieldset` / `radiogroup`

## Pistes suivantes

### Option 1. Tableaux avances

- renforcer les controles autour des tableaux de donnees complexes
- mieux couvrir les cas `th/id/headers`
- verifier certains patterns de tableaux mal structures

### Option 2. Forms modernes

- validation de `required` sur widgets custom quand pertinent
- coherence entre etats d'erreur et attributs ARIA
- controles autour de `aria-invalid`
- couverture plus fine des groupes radio / `fieldset` / `radiogroup`
