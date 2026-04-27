<?php

declare(strict_types=1);

// Example configuration file for twig-cs-fixer that enables some accessibility rules.
//
// IMPORTANT: This file must return a TwigCsFixer\Config\Config instance.
// The CLI loader only consumes the file when it returns a Config; returning a
// Ruleset directly will cause twig-cs-fixer to ignore the file and use the
// default configuration. See ConfigResolver::getConfigFromPath for details.
use TwigCsFixer\Config\Config;
use TwigCsFixer\Ruleset\Ruleset;

$ruleset = new Ruleset();

// Add the rules you want enabled by default. This example uses individual
// rule instances to avoid adding the A11yStandard as well, which would
// otherwise duplicate checks when someone copies this file verbatim.
$ruleset->addRule(new \TwigA11y\Rules\Structure\BannedTagsRule());
$ruleset->addRule(new \TwigA11y\Rules\Media\ImgAltRule());
$ruleset->addRule(new \TwigA11y\Rules\Structure\LangAttributeRule());

$config = new Config();
$config->setRuleset($ruleset);
// Include rules that are not "fixable" (they only lint/report). Many a11y
// rules are non-fixable and must be reported rather than auto-fixed.
$config->allowNonFixableRules(true);

return $config;
