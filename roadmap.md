# Roadmap WCAG AA/AAA — Checklist

## Phase 7 — CI / Workflow (½ jour)

- [ ] Fusionner `ci.yml` / `lint.yml` / `tests.yml` → `qa.yml` avec `workflow_call`
- [ ] `Rector --dry-run` partout (local + CI)
- [ ] Décider `composer.lock` : ne pas committer pour une librairie
- [ ] Ajouter job mutation testing (Infection) en non-bloquant
- [ ] Makefile : cibles `make test` / `lint` / `fix` / `ci`
