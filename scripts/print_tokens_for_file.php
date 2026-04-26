<?php
require __DIR__.'/../vendor/autoload.php';
use TwigCsFixer\Environment\StubbedEnvironment;
use TwigCsFixer\Token\Tokenizer;
use TwigCsFixer\Runner\Linter;
use TwigCsFixer\Ruleset\Ruleset;
use TwigA11y\Rules\Aria\AriaHiddenFocusRule;

$env=new StubbedEnvironment();
$tokenizer=new Tokenizer($env);
$linter=new Linter($env,$tokenizer);
$ruleset=new Ruleset();
$ruleset->addRule(new AriaHiddenFocusRule());
$file='tests/Rules/Aria/Fixtures/invalid/aria_hidden_focus.html.twig';
$tokens = $tokenizer->tokenizeFile(new SplFileInfo($file));
$s='';
foreach($tokens->toArray() as $t) {
    $s .= $t->getValue();
}
echo $s . PHP_EOL;
