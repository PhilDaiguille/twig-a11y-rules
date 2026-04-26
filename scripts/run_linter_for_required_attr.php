<?php

require __DIR__.'/../vendor/autoload.php';
use TwigA11y\Rules\Aria\AriaRequiredAttrRule;
use TwigCsFixer\Environment\StubbedEnvironment;
use TwigCsFixer\Ruleset\Ruleset;
use TwigCsFixer\Runner\Linter;
use TwigCsFixer\Token\Tokenizer;

$env = new StubbedEnvironment();
$tokenizer = new Tokenizer($env);
$linter = new Linter($env, $tokenizer);
$ruleset = new Ruleset();
$ruleset->addRule(new AriaRequiredAttrRule());
$file = 'tests/Rules/Aria/Fixtures/invalid/required_attr_missing.html.twig';
$report = $linter->run([new SplFileInfo($file)], $ruleset);
foreach ($report->getFileViolations($file) as $v) {
    echo ($v->getIdentifier() ? $v->getIdentifier()->toString() : '').' => '.$v->getMessage()."\n";
}
