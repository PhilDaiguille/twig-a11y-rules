<?php

declare(strict_types=1);

namespace TwigA11y\Standard;

use TwigCsFixer\Rules\RuleInterface;
use TwigCsFixer\Standard\StandardInterface;

/**
 * Strict accessibility standard — all stable rules enabled.
 *
 * Extends the Standard tier with additional RGAA, keyboard interaction,
 * and UI-level rules (doctype, charset, clickable non-interactives, mouse
 * event equivalents, etc.). Recommended for high-compliance projects or
 * public-sector applications subject to RGAA 4.1 / EN 301 549.
 *
 * Usage:
 *   $ruleset->addStandard(new A11yStrict());
 */
final class A11yStrict implements StandardInterface
{
    /**
     * @return list<RuleInterface>
     */
    public function getRules(): array
    {
        return StandardRuleSets::strict();
    }
}
