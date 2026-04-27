<?php

declare(strict_types=1);


use TwigA11y\Standard\A11yStrict;
use TwigCsFixer\Config\Config;
use TwigCsFixer\Ruleset\Ruleset;

$ruleset = new Ruleset();

// Add the rules you want enabled by default. This example uses individual
// rule instances to avoid adding the A11yStandard as well, which would
// otherwise duplicate checks when someone copies this file verbatim.
$ruleset->addStandard(new A11yStrict());

$config = new Config();
$config->setRuleset($ruleset);
// Include rules that are not "fixable" (they only lint/report). Many a11y
// rules are non-fixable and must be reported rather than auto-fixed.
$config->allowNonFixableRules(true);

return $config;
