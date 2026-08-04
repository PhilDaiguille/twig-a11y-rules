<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Standard;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Standard\A11yBasicStandard;
use TwigA11y\Standard\A11yRecommendedStandard;
use TwigA11y\Standard\A11yStandard;
use TwigA11y\Standard\A11yStrict;
use TwigA11y\Standard\StandardRuleSets;
use TwigCsFixer\Rules\Node\NodeRuleInterface;
use TwigCsFixer\Rules\RuleInterface;
use TwigCsFixer\Standard\StandardInterface;

/**
 * @internal
 */
#[CoversClass(A11yBasicStandard::class)]
#[CoversClass(A11yRecommendedStandard::class)]
#[CoversClass(A11yStandard::class)]
#[CoversClass(A11yStrict::class)]
#[CoversClass(StandardRuleSets::class)]
final class A11yStandardTest extends TestCase
{
    /**
     * Each tier must return a non-empty list of AbstractA11yRule instances,
     * with no duplicates, and at least the expected minimum count.
     *
     * @param int $minCount lower bound — adding new rules never breaks this test
     */
    #[DataProvider('provideStandards')]
    public function testTierIsValid(StandardInterface $standard, int $minCount): void
    {
        $rules = $standard->getRules();

        $this->assertNotEmpty($rules, 'Standard must provide at least one rule.');
        $this->assertContainsOnlyInstancesOf(RuleInterface::class, $rules);

        foreach ($rules as $rule) {
            $this->assertInstanceOf(
                AbstractA11yRule::class,
                $rule,
                sprintf('Expected %s to extend AbstractA11yRule.', $rule::class)
            );
        }

        $classes = $this->classNames($rules);
        $this->assertSame(
            $classes,
            array_values(array_unique($classes)),
            'Standard must not contain duplicate rules.'
        );

        $this->assertGreaterThanOrEqual(
            $minCount,
            count($rules),
            sprintf('Expected at least %d rules, got %d.', $minCount, count($rules))
        );
    }

    /**
     * @return iterable<string, array{StandardInterface, int}>
     */
    public static function provideStandards(): iterable
    {
        yield 'basic' => [new A11yBasicStandard(), 5];

        yield 'recommended' => [new A11yRecommendedStandard(), 15];

        yield 'standard' => [new A11yStandard(), 26];

        yield 'strict' => [new A11yStrict(), 73];
    }

    /**
     * Each tier must be a prefix-subset of the next: every rule in a lighter
     * tier must appear — in the same order — at the start of the heavier tier.
     */
    public function testStandardsAreMonotonic(): void
    {
        $basic = $this->classNames(new A11yBasicStandard()->getRules());
        $recommended = $this->classNames(new A11yRecommendedStandard()->getRules());
        $standard = $this->classNames(new A11yStandard()->getRules());
        $strict = $this->classNames(new A11yStrict()->getRules());

        $this->assertSame($basic, array_values(array_intersect($recommended, $basic)));
        $this->assertSame($recommended, array_values(array_intersect($standard, $recommended)));
        $this->assertSame($standard, array_values(array_intersect($strict, $standard)));
    }

    /**
     * Every rule in the strict tier must also appear in the strict tier of
     * StandardRuleSets — i.e. the Standard classes are faithful proxies.
     */
    public function testStandardClassesProxyStandardRuleSets(): void
    {
        $this->assertSame(
            $this->classNames(StandardRuleSets::basic()),
            $this->classNames(new A11yBasicStandard()->getRules())
        );

        $this->assertSame(
            $this->classNames(StandardRuleSets::recommended()),
            $this->classNames(new A11yRecommendedStandard()->getRules())
        );

        $this->assertSame(
            $this->classNames(StandardRuleSets::standard()),
            $this->classNames(new A11yStandard()->getRules())
        );

        $this->assertSame(
            $this->classNames(StandardRuleSets::strict()),
            $this->classNames(new A11yStrict()->getRules())
        );
    }

    /**
     * @param list<RuleInterface> $rules
     *
     * @return list<string>
     */
    private function classNames(array $rules): array
    {
        return array_map(
            static fn (NodeRuleInterface|RuleInterface $rule): string => $rule::class,
            $rules
        );
    }
}
