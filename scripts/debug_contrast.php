<?php
$f = $argv[1] ?? 'tests/Rules/Ui/Fixtures/invalid/bad_contrast.html.twig';
$c = file_get_contents($f);
echo "FILE: $f\n";
echo $c . "\n";
$pattern = '/style\\s*=\\s*["\\']([^"\\']+)["\\']/i';
echo "PATTERN: $pattern\n";
$ok = preg_match_all($pattern, $c, $m);
var_dump($ok);
var_dump($m);
