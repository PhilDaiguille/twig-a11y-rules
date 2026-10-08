@AGENTS.md

## Claude-specific notes

- For twig-cs-fixer, Twig or Symfony API questions, fetch current docs with
  `ctx7` instead of relying on memory; when in doubt, read
  `vendor/vincentlanglet/twig-cs-fixer/src/` directly.
- For Mago (formatter / linter / analyzer config, rule names, baseline), fetch
  docs with `ctx7` (`/carthage-software/mago`) or run
  `vendor/bin/mago lint --explain <rule>`; config lives in `mago.toml`.
- After editing PHP, run `composer fmt` then `composer lint` before reporting done.
