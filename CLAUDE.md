@AGENTS.md

## Claude-specific notes

- For twig-cs-fixer, Twig or Symfony API questions, fetch current docs with
  `ctx7` instead of relying on memory; when in doubt, read
  `vendor/vincentlanglet/twig-cs-fixer/src/` directly.
- For Mago (formatter / linter / analyzer config, rule names, baseline), fetch
  docs with `ctx7` (`/carthage-software/mago`) or run
  `vendor/bin/mago lint --explain <rule>`; config lives in `mago.toml`.
- After editing PHP, run `composer fmt` then `composer lint` before reporting done.
- Never relax `[guard]`, raise a metric threshold or grow the analyzer baseline
  in `mago.toml` to make `composer lint` pass, unless the user asks: fix the code.
