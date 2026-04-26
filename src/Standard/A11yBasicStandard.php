<?php

declare(strict_types=1);

namespace TwigA11y\Standard;

use TwigA11y\Rules\Media\ImgAltRule;
use TwigA11y\Rules\Structure\BannedTagsRule;
use TwigCsFixer\Rules\RuleInterface;
use TwigCsFixer\Standard\StandardInterface;

final class A11yBasicStandard implements StandardInterface
{
    /**
     * @return list<RuleInterface>
     */
    public function getRules(): array
    {
        return [
            new ImgAltRule(),
            new BannedTagsRule(),
        ];
    }
}
