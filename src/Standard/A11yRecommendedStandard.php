<?php

declare(strict_types=1);

namespace TwigA11y\Standard;

use TwigA11y\Rules\Media\ImgAltRule;
use TwigA11y\Rules\Structure\BannedTagsRule;
use TwigA11y\Rules\Structure\HeadingOrderRule;
use TwigCsFixer\Rules\RuleInterface;
use TwigCsFixer\Standard\StandardInterface;

final class A11yRecommendedStandard implements StandardInterface
{
    /**
     * @return list<RuleInterface>
     */
    public function getRules(): array
    {
        // Recommended includes basic rules plus heading order.
        return [
            new ImgAltRule(),
            new BannedTagsRule(),
            new HeadingOrderRule(),
        ];
    }
}
