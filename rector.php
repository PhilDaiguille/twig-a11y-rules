<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        phpunitCodeQuality: true,
        typeDeclarationDocblocks: true,
        typeDeclarations: true,
        privatization: true,
        rectorPreset: true,
        earlyReturn: true,
    )
    ->withComposerBased(phpunit: true)
;
