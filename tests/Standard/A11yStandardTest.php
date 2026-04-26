<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Standard;

use PHPUnit\Framework\TestCase;
use TwigA11y\Rules\Media\ImgAltRule;
use TwigA11y\Rules\Structure\BannedTagsRule;
use TwigA11y\Rules\Structure\HeadingOrderRule;
use TwigA11y\Standard\A11yBasicStandard;
use TwigA11y\Standard\A11yRecommendedStandard;
use TwigA11y\Standard\A11yStandard;

/**
 * @internal
 *
 * @coversNothing
 */
final class A11yStandardTest extends TestCase
{
    public function testBasicStandardProvidesRules(): void
    {
        $standard = new A11yBasicStandard();
        $rules = $standard->getRules();
        $this->assertNotEmpty($rules);
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
}
