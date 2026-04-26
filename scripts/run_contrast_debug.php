<?php
$file = $argv[1] ?? __DIR__.'/../tests/Rules/Ui/Fixtures/invalid/bad_contrast.html.twig';
if (!file_exists($file)) {
    echo "file not found: $file\n";
    exit(1);
}
$content = file_get_contents($file);
echo "CONTENT:\n". $content ."\n\n";

// simple regex test
$pattern = '/style\\s*=\\s*["\\']([^"\\']+)["\\']/i';
echo "PATTERN: $pattern\n";
$ok = preg_match_all($pattern, $content, $m);
echo "preg_match_all result: "; var_export($ok); echo "\n";
var_export($m); echo "\n";

// manual parse
$styles = [];
$pos = 0; $len = strlen($content);
while (false !== ($idx = stripos($content, 'style=', $pos))) {
    $p = $idx + 6;
    while ($p < $len && ctype_space($content[$p])) { $p++; }
    if ($p >= $len) break;
    $quote = $content[$p];
    if ($quote !== '"' && $quote !== "'") { $pos = $p; continue; }
    $p++;
    $start = $p;
    while ($p < $len && $content[$p] !== $quote) { $p++; }
    if ($p >= $len) break;
    $styles[] = substr($content, $start, $p - $start);
    $pos = $p + 1;
}
echo "MANUALLY FOUND STYLES:\n"; var_export($styles); echo "\n";
