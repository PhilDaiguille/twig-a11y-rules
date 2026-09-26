# Roadmap

Where `twig-a11y-rules` is heading after 1.0. The two goals: cover everything
**WCAG 2.2 AA** allows a static linter to check, and keep **false positives as
close to zero as possible**: a noisy rule gets disabled and helps nobody.

Each item has a GitHub issue in the matching milestone. Pick one, comment on it,
and see [`CONTRIBUTING.md`](CONTRIBUTING.md). Releases follow SemVer: fixes are
patches, new rules and deprecations are minors, removals wait for 2.0.

## 1.0.1: false positives

Confirmed on real Symfony-style templates:

- [ ] HTML inside Twig comments, `<script>` and `<style>` is analysed
      (`HeadingOrder`, `AriaRole`, …)
- [ ] Same `id` in `{% if %}` / `{% else %}` branches reported as duplicate
- [ ] `<label for>` / `aria-*` id references reported missing when the target
      lives in an included template or the layout
- [ ] `AriaAllowedAttr` rejects global ARIA 1.2 attributes (`aria-controls`, …)
- [ ] `AnchorContent` misses `<img alt>` names when the image has boolean
      attributes (`ismap`)
- [ ] `AriaRole`: accept DPUB (`doc-*`) and Graphics (`graphics-*`) roles

## 1.1: one defect, one error

- [ ] Audit overlapping rules; deprecate `AnchorContentRule` in favour of
      `AnchorAccessibleNameRule`; `PlaceholderOnlyLabel` stays silent when
      `InputLabel` already reports
- [ ] Shared attribute parser in `TokenCollectorTrait` instead of per-rule regexes
- [ ] Every rule declares its WCAG 2.2 success criteria (README column);
      drop references to 4.1.1 Parsing, removed in WCAG 2.2
- [ ] README: what a linter cannot check (2.4.11, 2.5.7, 3.2.6, 3.3.7, real
      contrast, focus order)

## 1.2: new WCAG 2.2 / ARIA 1.2 rules

New rules start as warnings in `A11yStrict` and are promoted after a release
without false-positive reports.

| Rule | WCAG | Checks |
|---|---|---|
| `LabelInName` | 2.5.3 A | `aria-label` contains the visible text |
| `AccessibleAuthentication` | 3.3.8 AA | password fields that block paste or disable autocomplete |
| `AriaProhibitedAttr` | 4.1.2 A | `aria-label` on `div`/`span` without a role |
| `LangAttributeValue` (extended) | 3.1.2 AA | valid BCP 47 `lang` on any element |
| `ImgAltRedundant` | 1.1.1 A | alt such as "image of…" or a file name |
| `ServerSideImageMap` | 2.1.1 A | `<img ismap>` |
| `AriaWidgetName` | 4.1.2 A | named meter, progressbar, tooltip, switch… |

## 1.3: Twig-aware checks

- [ ] `form_widget(form.x)` rendered without `form_label` / `form_row`
- [ ] Configurable rules: localised generic link phrases (fr, de, es…),
      target-size threshold, `<twig:Component>` → native element mapping
- [ ] Structure rules understand `{% if %}` branches

## Always

- Real-world corpus in CI (`symfony/demo` templates) with a committed baseline:
  any new violation shows up in the PR diff
- Mutation testing on changed files
- A confirmed false positive ships as a patch release
- Watching ARIA 1.3, new HTML (`popover`, `<search>`, `inert`) and the WCAG 3.0
  draft (nothing implemented before it becomes a Recommendation)
