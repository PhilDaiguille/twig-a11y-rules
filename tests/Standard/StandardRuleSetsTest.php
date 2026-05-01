<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Standard;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use TwigA11y\Rules\Forms\InputLabelRule;
use TwigA11y\Rules\Media\ImgAltRule;
use TwigA11y\Rules\Structure\BannedTagsRule;
use TwigA11y\Rules\Structure\ButtonContentRule;
use TwigA11y\Rules\Structure\LangAttributeRule;
use TwigA11y\Standard\StandardRuleSets;
use TwigCsFixer\Rules\RuleInterface;

/**
 * @internal
 */
#[CoversClass(StandardRuleSets::class)]
final class StandardRuleSetsTest extends TestCase
{
    public function testBasicReturnsRuleInterfaceInstances(): void
    {
        $rules = StandardRuleSets::basic();

        self::assertNotEmpty($rules);
        self::assertContainsOnlyInstancesOf(RuleInterface::class, $rules);
    }

    public function testBasicContainsExpectedClasses(): void
    {
        $classes = array_map(static fn (RuleInterface $r): string => $r::class, StandardRuleSets::basic());

        self::assertSame([
            ImgAltRule::class,
            BannedTagsRule::class,
            ButtonContentRule::class,
            InputLabelRule::class,
            LangAttributeRule::class,
        ], $classes);
    }

    public function testRecommendedReturnsRuleInterfaceInstances(): void
    {
        $rules = StandardRuleSets::recommended();

        self::assertNotEmpty($rules);
        self::assertContainsOnlyInstancesOf(RuleInterface::class, $rules);
    }

    public function testRecommendedIsSupersetOfBasic(): void
    {
        $basicClasses = array_map(static fn (RuleInterface $r): string => $r::class, StandardRuleSets::basic());
        $recommendedClasses = array_map(static fn (RuleInterface $r): string => $r::class, StandardRuleSets::recommended());

        foreach ($basicClasses as $class) {
            self::assertContains($class, $recommendedClasses, \sprintf('Recommended should include basic rule "%s".', $class));
        }
    }

    public function testStandardReturnsRuleInterfaceInstances(): void
    {
        $rules = StandardRuleSets::standard();

        self::assertNotEmpty($rules);
        self::assertContainsOnlyInstancesOf(RuleInterface::class, $rules);
    }

    public function testStandardIsSupersetOfRecommended(): void
    {
        $recommendedClasses = array_map(static fn (RuleInterface $r): string => $r::class, StandardRuleSets::recommended());
        $standardClasses = array_map(static fn (RuleInterface $r): string => $r::class, StandardRuleSets::standard());

        foreach ($recommendedClasses as $class) {
            self::assertContains($class, $standardClasses, \sprintf('Standard should include recommended rule "%s".', $class));
        }
    }

    public function testStrictReturnsRuleInterfaceInstances(): void
    {
        $rules = StandardRuleSets::strict();

        self::assertNotEmpty($rules);
        self::assertContainsOnlyInstancesOf(RuleInterface::class, $rules);
    }

    public function testStrictIsSupersetOfStandard(): void
    {
        $standardClasses = array_map(static fn (RuleInterface $r): string => $r::class, StandardRuleSets::standard());
        $strictClasses = array_map(static fn (RuleInterface $r): string => $r::class, StandardRuleSets::strict());

        foreach ($standardClasses as $class) {
            self::assertContains($class, $strictClasses, \sprintf('Strict should include standard rule "%s".', $class));
        }
    }

    public function testEachRuleSetContainsUniqueClasses(): void
    {
        foreach ([
            'basic' => StandardRuleSets::basic(),
            'recommended' => StandardRuleSets::recommended(),
            'standard' => StandardRuleSets::standard(),
            'strict' => StandardRuleSets::strict(),
        ] as $level => $rules) {
            $classes = array_map(static fn (RuleInterface $r): string => $r::class, $rules);
            self::assertSame(
                array_unique($classes),
                $classes,
                \sprintf('Rule set "%s" should not contain duplicate rule classes.', $level)
            );
        }
    }

    public function testInstantiateProducesNewInstancesEachCall(): void
    {
        $rulesA = StandardRuleSets::basic();
        $rulesB = StandardRuleSets::basic();

        self::assertCount(\count($rulesA), $rulesB);

        foreach ($rulesA as $i => $ruleA) {
            self::assertNotSame($ruleA, $rulesB[$i], 'Each call to basic() should return fresh instances.');
        }
    }
}
