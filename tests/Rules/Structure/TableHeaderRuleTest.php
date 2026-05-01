<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\TableHeaderRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(TableHeaderRule::class)]
final class TableHeaderRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new TableHeaderRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid table' => [__DIR__.'/Fixtures/valid/table_with_th_scope.html.twig', []];

        yield 'missing th scope' => [
            __DIR__.'/Fixtures/invalid/table_missing_th_scope.html.twig',
            [
                'TableHeader.TableHeader.MissingScope:5:7' => 'Table header <th> elements should include a scope attribute.',
                'TableHeader.TableHeader.MissingScope#2:6:7' => 'Table header <th> elements should include a scope attribute.',
            ],
        ];

        yield 'invalid scope value' => [
            __DIR__.'/Fixtures/invalid/table_th_scope_invalid_value.html.twig',
            [
                'TableHeader.TableHeader.InvalidScope:5:7' => 'Table header <th> has invalid scope value "invalid".',
            ],
        ];
    }
}
