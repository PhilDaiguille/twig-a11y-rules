# Roadmap — twig-a11y-rules

## Priorité haute — fort impact, faciles à implémenter

- [x] `ImgAltRule` — `<img>` sans attribut `alt` ou `alt=""` sans `role="presentation"`
- [x] `ButtonContentRule` — `<button>` sans contenu textuel ni `aria-label`
- [x] `AnchorContentRule` — `<a>` sans texte ni `aria-label` ni `title`
- [x] `InputLabelRule` — `<input>` sans `<label>` associé ou `aria-label`
- [x] `TabIndexRule` — `tabindex` > 0
- [x] `LangAttributeRule` — `<html>` sans attribut `lang`

## Priorité moyenne — un peu plus complexes

- [ ] `AriaRoleRule` — valeur de `role` invalide (liste ARIA valide)
- [ ] `AriaLabelRule` — `aria-label` vide ou manquant sur les landmarks
- [ ] `FormLabelRule` — `<label>` sans `for` ou sans contenu
- [ ] `HeadingOrderRule` — hiérarchie de headings cassée (h1 → h3 sans h2)
- [ ] `BannedTagsRule` — `<marquee>`, `<blink>`
- [ ] `SelectLabelRule` — `<select>` sans `<label>` associé
- [ ] `TextareaLabelRule` — `<textarea>` sans `<label>` associé

## Priorité basse — complexes à analyser statiquement

- [ ] `AriaHiddenFocusRule` — `aria-hidden="true"` sur un élément focusable
- [ ] `AriaRequiredAttrRule` — attributs ARIA obligatoires manquants selon le role
- [ ] `HeadingEmptyRule` — `<h1>`...`<h6>` vide
- [ ] `IframeTitleRule` — `<iframe>` sans `title`
- [ ] `MetaViewportRule` — `<meta name="viewport">` avec `user-scalable=no`
- [ ] `AutoplayRule` — `<video>` ou `<audio>` avec `autoplay` sans `muted`
- [ ] `ObjectAltRule` — `<object>` sans alternative textuelle
