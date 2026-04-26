<?php
// Example configuration file for twig-cs-fixer that enables some accessibility rules
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
use TwigA11y\Rules\Structure\AllInOneRule;
$ruleset->addRule(new AllInOneRule());

return $ruleset;
