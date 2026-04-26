<?php
// Example configuration file for twig-cs-fixer that enables some accessibility rules.
//
// IMPORTANT: This file must return a TwigCsFixer\Config\Config instance.
// The CLI loader only consumes the file when it returns a Config; returning a
// Ruleset directly will cause twig-cs-fixer to ignore the file and use the
// default configuration. See ConfigResolver::getConfigFromPath for details.
use TwigCsFixer\Config\Config;
use TwigCsFixer\Ruleset\Ruleset;
use TwigA11y\Rules\Structure\BannedTagsRule;
use TwigA11y\Rules\Media\ImgAltRule;
use TwigA11y\Rules\Structure\LangAttributeRule;

$ruleset = new Ruleset();

// Add the rules you want enabled by default
$ruleset->addRule(new BannedTagsRule());
$ruleset->addRule(new ImgAltRule());
$ruleset->addRule(new LangAttributeRule());
// All-in-one convenience rule
use TwigA11y\Standard\A11yStandard;
$ruleset->addStandard(new A11yStandard());

$config = new Config();
$config->setRuleset($ruleset);
// Include rules that are not "fixable" (they only lint/report). Many a11y
// rules are non-fixable and must be reported rather than auto-fixed.
$config->allowNonFixableRules(true);

return $config;
