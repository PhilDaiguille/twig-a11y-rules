<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\ListStructureRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(ListStructureRule::class)]
final class ListStructureRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new ListStructureRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<(array<int, array<string, string>>|array<int, string>)>
     */
    public static function provideFixtures(): iterable
    {
        yield 'ul with non-li child' => [__DIR__.'/Fixtures/invalid/list_non_li.html.twig', [
            'ListStructure.ListStructure.InvalidChild:1:1' => 'List (<ul>/<ol>) contains non-<li> child.',
        ]];

        yield 'orphan dt dd' => [__DIR__.'/Fixtures/invalid/orphan_dt.html.twig', [
            'ListStructure.ListStructure.OrphanDtDd:1:1' => 'Orphan <dt> or <dd> found outside of a <dl>.',
        ]];
    }
}
