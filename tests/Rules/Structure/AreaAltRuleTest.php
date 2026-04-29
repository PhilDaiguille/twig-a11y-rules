<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\AreaAltRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @covers \TwigA11y\Rules\Structure\AreaAltRule
 */
final class AreaAltRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AreaAltRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'area with alt' => [__DIR__.'/Fixtures/valid/area_with_alt.html.twig', []];

        yield 'area decorative with empty alt and role' => [__DIR__.'/Fixtures/valid/area_empty_alt_decorative.html.twig', []];

        yield 'area without alt' => [__DIR__.'/Fixtures/invalid/area_no_alt.html.twig', [
            'AreaAlt.AreaAlt.MissingAlt:2:1' => 'Missing alt attribute on <area> tag.',
        ]];

        yield 'area empty alt without role' => [__DIR__.'/Fixtures/invalid/area_empty_alt_no_role.html.twig', [
            'AreaAlt.AreaAlt.EmptyAlt:2:1' => 'Empty alt on <area> requires role="presentation" or role="none".',
        ]];
    }
}
