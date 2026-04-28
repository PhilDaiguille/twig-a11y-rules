Voici ta todo list structurée et priorisée pour implémenter les 22 règles axe-core manquantes faisables statiquement.

***

## 📋 TODO — Règles axe-core à implémenter (22)

### 🔴 ARIA — 7 règles

- [ ] **`aria-valid-attr`** *(Critical / Facile)* — Whitelist des 46 attributs ARIA officiels, regex `/aria-([a-z-]+)/gi`, comparer à la liste
- [ ] **`aria-valid-attr-value`** *(Critical / Modéré)* — Map enum → valeurs valides, ignorer les expressions `{{ }}` Twig dynamiques
- [ ] **`aria-allowed-attr`** *(Critical / Facile)* — Map `role → [attributs interdits]`, vérifier aria-* présents contre la liste "prohibited" du rôle
- [ ] **`aria-hidden-body`** *(Critical / Facile)* — Règle FullPage uniquement, regex `<body[^>]*aria-hidden\s*=\s*["']true["']`
- [ ] **`aria-required-children`** *(Critical / Modéré)* — Map `role → [rôles enfants requis]`, extraire blocs parent+contenu, vérifier enfants directs
- [ ] **`aria-required-parent`** *(Critical / Modéré)* — Inverse de `aria-required-children`, vérifier contexte parent dans le fichier
- [ ] **`aria-deprecated-role`** *(Minor / Facile)* — Extension de `AriaRoleRule` existante, ajouter liste de rôles dépréciés, émettre un warning

***

### 🟠 Formulaires — 5 règles

- [ ] **`input-button-name`** *(Critical / Facile)* — Extension de `ButtonContentRule`, détecter `<input type="submit|button|reset">` sans `value` ni `aria-label`
- [ ] **`autocomplete-valid`** *(Serious / Facile)* — Whitelist des 53 valeurs WCAG 1.3.5, ignorer valeurs Twig dynamiques, émettre si valeur statique inconnue
- [ ] **`aria-input-field-name`** *(Serious / Modéré)* — Compléter `InputLabelRule` pour couvrir `role="textbox/combobox/searchbox/spinbutton"` sur div/span
- [ ] **`select-name`** *(Critical / Modéré)* — Modifier `SelectLabelRule::evaluate()` pour accepter `aria-label` et `aria-labelledby` comme nom accessible valide
- [ ] **`fieldset-legend`** *(Serious / Modéré)* — Règle once-per-file, regex `<fieldset>...</fieldset>`, vérifier présence de `<legend>` non vide

***

### 🟡 Structure & Navigation — 5 règles

- [ ] **`document-title`** *(Serious / Facile)* — FullPage uniquement, regex `/<title[^>]*>\s*[^<\s]/i` sur le contenu complet
- [ ] **`page-has-heading-one`** *(Moderate / Facile)* — FullPage uniquement, `preg_match('/<h1[^>]*>\s*[^<\s]/i', $full)`
- [ ] **`area-alt`** *(Critical / Facile)* — Clone simplifié de `ImgAltRule`, détecter `<area>` sans `alt` ou `alt=""` sans `role="presentation"`
- [ ] **`list-structure`** *(Serious / Modéré)* — Once-per-file, extraire contenu `<ul>/<ol>`, vérifier que les enfants directs sont bien des `<li>`
- [ ] **`scrollable-region-focusable`** *(Serious / Complexe)* — Détecter `style="overflow:(auto|scroll)"` sans `tabindex` sur le même tag (styles inline uniquement)

***

### 🔵 Structure (suite) & Tables — 4 règles

- [ ] **`definition-list`** *(Serious / Modéré)* — Deux sous-règles : structure `<dl>` + orphelins `<dt>/<dd>`, extraction regex once-per-file
- [ ] **`td-headers-attr`** *(Critical / Modéré)* — Extraire chaque `<table>`, collecter les `id` des `<th>`, valider les `headers="..."` des `<td>`
- [ ] **`scope-attr-valid`** *(Moderate / Facile)* — Modifier `TableHeaderRule` pour vérifier que la valeur de `scope` ∈ `{col, row, colgroup, rowgroup}`
- [ ] **`table-duplicate-name`** *(Minor / Facile)* — Extraire `summary` et contenu `<caption>`, comparer (insensible à la casse, trim), once-per-file

***

### ⚫ Best Practices — 1 règle restante

- [ ] **`landmark-unique`** *(Moderate / Facile)* — Compter les occurrences de chaque landmark (`main`, `nav`, `aside`, `header`, `footer`), warning si count > 1 et labels non distincts

***

### 📝 Documentation — 7 règles non faisables

- [ ] **Documenter dans le README** les 7 règles non faisables statiquement avec lien vers axe-core runtime :
    - `color-contrast-enhanced`, `focus-visible`, `identical-links-same-purpose`
    - `target-size`, `aria-labelledby-valid`, `frame-tested`, `avoid-inline-spacing`

***

## 🗂 Ordre de réalisation suggéré

| Priorité | Règles | Raison |
|---|---|---|
| **1er** | `aria-valid-attr`, `aria-hidden-body`, `input-button-name`, `document-title`, `area-alt`, `page-has-heading-one` | Critical/Serious + Facile — gains rapides |
| **2e** | `aria-valid-attr-value`, `aria-allowed-attr`, `autocomplete-valid`, `scope-attr-valid`, `table-duplicate-name` | Facile, extensions de règles existantes |
| **3e** | `aria-required-children`, `aria-required-parent`, `aria-input-field-name`, `select-name`, `fieldset-legend`, `list-structure`, `definition-list`, `td-headers-attr`, `landmark-unique` | Modéré — logique d'extraction plus poussée |
| **4e** | `scrollable-region-focusable`, `aria-deprecated-role` | Complexe ou priorité basse |
| **5e** | README documentation | Non-code, complétion finale |