<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\EmptyTableHeaderRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(EmptyTableHeaderRule::class)]
final class EmptyTableHeaderRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new EmptyTableHeaderRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid th with content' => [__DIR__.'/Fixtures/valid/table_th_with_content.html.twig', []];

        yield 'invalid empty th' => [
            __DIR__.'/Fixtures/invalid/table_th_empty.html.twig',
            ['EmptyTableHeader.EmptyTableHeader.Empty:1:1' => 'Table header <th> must not be empty.'],
        ];
    }
}
