<?php

declare(strict_types=1);

namespace TwigA11y\Standard;

use TwigCsFixer\Rules\RuleInterface;
use TwigCsFixer\Standard\StandardInterface;

/**
 * Strict accessibility standard.
 *
 * This preset enables all stable accessibility rules provided by TwigA11y.
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
