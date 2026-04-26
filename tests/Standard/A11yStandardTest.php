<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Standard;

use PHPUnit\Framework\TestCase;
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
use TwigA11y\Standard\A11yBasicStandard;
use TwigA11y\Standard\A11yRecommendedStandard;
use TwigA11y\Standard\A11yStandard;
use TwigA11y\Standard\A11yStrict;
use TwigCsFixer\Rules\RuleInterface;

#[\PHPUnit\Framework\Attributes\CoversNothing]
/** @internal */
final class A11yStandardTest extends TestCase
{
    public function testBasicStandardProvidesRules(): void
    {
        $standard = new A11yBasicStandard();
        $rules = $standard->getRules();
        $this->assertNotEmpty($rules);
        foreach ($rules as $rule) {
            $this->assertInstanceOf(RuleInterface::class, $rule);
        }
        $classes = array_map(fn ($r): string => $r::class, $rules);
        $this->assertContains(ImgAltRule::class, $classes);
        $this->assertContains(BannedTagsRule::class, $classes);
    }

    public function testRecommendedStandardProvidesRules(): void
    {
        $standard = new A11yRecommendedStandard();
        $rules = $standard->getRules();
        $this->assertNotEmpty($rules);
        $classes = array_map(fn ($r): string => $r::class, $rules);
        $this->assertContains(ImgAltRule::class, $classes);
        $this->assertContains(BannedTagsRule::class, $classes);
        $this->assertContains(HeadingOrderRule::class, $classes);
    }

    public function testA11yStandardProvidesRules(): void
    {
        $standard = new A11yStandard();
        $rules = $standard->getRules();
        $this->assertNotEmpty($rules);
        $classes = array_map(fn ($r): string => $r::class, $rules);
        $this->assertContains(ImgAltRule::class, $classes);
        $this->assertContains(BannedTagsRule::class, $classes);
        $this->assertContains(HeadingOrderRule::class, $classes);
    }

    public function testA11yStrictProvidesRules(): void
    {
        $standard = new A11yStrict();
        $rules = $standard->getRules();

        $this->assertNotEmpty($rules);

        $classes = array_map(static fn (RuleInterface $rule): string => $rule::class, $rules);

        $this->assertContains(ImgAltRule::class, $classes);
        $this->assertContains(AutoplayRule::class, $classes);
        $this->assertContains(ObjectAltRule::class, $classes);
        $this->assertContains(BannedTagsRule::class, $classes);
        $this->assertContains(ButtonContentRule::class, $classes);
        $this->assertContains(AnchorContentRule::class, $classes);
        $this->assertContains(HeadingOrderRule::class, $classes);
        $this->assertContains(HeadingEmptyRule::class, $classes);
        $this->assertContains(LangAttributeRule::class, $classes);
        $this->assertContains(IframeTitleRule::class, $classes);
        $this->assertContains(MetaViewportRule::class, $classes);
        $this->assertContains(TabIndexRule::class, $classes);
        $this->assertContains(AriaRoleRule::class, $classes);
        $this->assertContains(AriaLabelRule::class, $classes);
        $this->assertContains(AriaHiddenFocusRule::class, $classes);
        $this->assertContains(AriaRequiredAttrRule::class, $classes);
        $this->assertContains(FormLabelRule::class, $classes);
        $this->assertContains(InputLabelRule::class, $classes);
        $this->assertContains(SelectLabelRule::class, $classes);
        $this->assertContains(TextareaLabelRule::class, $classes);
    }
}
