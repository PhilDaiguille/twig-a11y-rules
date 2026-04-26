<?php

declare(strict_types=1);

namespace TwigA11y\Standard;

use TwigA11y\Rules\Aria\AriaHiddenFocusRule;
use TwigA11y\Rules\Aria\AriaLabelRule;
use TwigA11y\Rules\Aria\AriaRequiredAttrRule;
use TwigA11y\Rules\Aria\AriaRoleRule;
use TwigA11y\Rules\Aria\TabIndexRule;
use TwigA11y\Rules\Forms\FormLabelRule;
use TwigA11y\Rules\Forms\InputLabelRule;
use TwigA11y\Rules\Forms\SelectLabelRule;
use TwigA11y\Rules\Forms\TextareaLabelRule;
use TwigA11y\Rules\Media\AutoplayRule;
use TwigA11y\Rules\Media\ImgAltRule;
use TwigA11y\Rules\Media\ObjectAltRule;
use TwigA11y\Rules\Structure\AnchorContentRule;
use TwigA11y\Rules\Structure\BannedTagsRule;
use TwigA11y\Rules\Structure\ButtonContentRule;
use TwigA11y\Rules\Structure\HeadingEmptyRule;
use TwigA11y\Rules\Structure\HeadingOrderRule;
use TwigA11y\Rules\Structure\IframeTitleRule;
use TwigA11y\Rules\Structure\LangAttributeRule;
use TwigA11y\Rules\Structure\MetaViewportRule;
use TwigCsFixer\Rules\RuleInterface;
use TwigCsFixer\Standard\StandardInterface;

/**
 * Strict accessibility standard.
 *
 * This preset enables all stable accessibility rules provided by TwigA11y.
 *
 * Usage:
 *   $ruleset->addStandard(new A11yStrict());
 */
final class A11yStrict implements StandardInterface
{
    /**
     * @return list<RuleInterface>
     */
    public function getRules(): array
    {
        return [
            // Media
            new ImgAltRule(),
            new AutoplayRule(),
            new ObjectAltRule(),

            // Structure
            new BannedTagsRule(),
            new ButtonContentRule(),
            new AnchorContentRule(),
            new HeadingOrderRule(),
            new HeadingEmptyRule(),
            new LangAttributeRule(),
            new IframeTitleRule(),
            new MetaViewportRule(),

            // ARIA
            new TabIndexRule(),
            new AriaRoleRule(),
            new AriaLabelRule(),
            new AriaHiddenFocusRule(),
            new AriaRequiredAttrRule(),

            // Forms
            new FormLabelRule(),
            new InputLabelRule(),
            new SelectLabelRule(),
            new TextareaLabelRule(),
        ];
    }
}
