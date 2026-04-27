<?php

declare(strict_types=1);

namespace TwigA11y\Standard;

use TwigCsFixer\Rules\RuleInterface;
use TwigCsFixer\Standard\StandardInterface;

/**
 * A convenience standard that aggregates accessibility rules.
 *
 * Users can add this to their Ruleset via $ruleset->addStandard(new A11yStandard());
 */
final class A11yStandard implements StandardInterface
{
    /**
     * @return list<RuleInterface>
     */
    public function getRules(): array
    {
        return StandardRuleSets::standard();
    }
}
