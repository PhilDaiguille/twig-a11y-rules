<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaAllowedAttrRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
final class AriaAllowedAttrRuleTest extends AbstractRuleTestCase
{
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaAllowedAttrRule(), $expectedErrors, $fixture);
    }

    public static function provideFixtures(): iterable
    {
        yield 'table without row children' => [
            __DIR__.'/Fixtures/invalid/role_table_no_row.html.twig',
            ['AriaAllowedAttr.AriaRequired.ChildrenMissing:1:1' => 'Role "table" should include children with role "row".'],
        ];
    }
}
