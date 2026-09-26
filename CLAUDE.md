@AGENTS.md

## Claude-specific notes

- For twig-cs-fixer, Twig or Symfony API questions, fetch current docs with
  `ctx7` instead of relying on memory; when in doubt, read
  `vendor/vincentlanglet/twig-cs-fixer/src/` directly.
- Legacy: most rules still pass dotted message ids (`'InputLabel.MissingLabel'`).
  They are being migrated to single-segment ids before 1.0; follow the
  convention above for new code.
