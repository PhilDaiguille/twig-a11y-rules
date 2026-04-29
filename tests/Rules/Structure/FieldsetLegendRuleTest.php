<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\FieldsetLegendRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(FieldsetLegendRule::class)]
final class FieldsetLegendRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new FieldsetLegendRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<(array<int, array<string, string>>|array<int, string>)>
     */
    public static function provideFixtures(): iterable
    {
        yield 'fieldset without legend' => [__DIR__.'/Fixtures/invalid/fieldset_no_legend.html.twig', [
            'FieldsetLegend.Fieldset.LegendMissing:1:1' => 'Fieldset must contain a non-empty <legend>.',
        ]];
    }
}
