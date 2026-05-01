<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\TableFakeCaptionRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(TableFakeCaptionRule::class)]
final class TableFakeCaptionRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new TableFakeCaptionRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid table with caption' => [__DIR__.'/Fixtures/valid/table_with_caption.html.twig', []];

        yield 'invalid table fake caption' => [
            __DIR__.'/Fixtures/invalid/table_fake_caption.html.twig',
            ['TableFakeCaption.TableFakeCaption.FakeCaption:1:1' => 'Avoid using a <td> as a table caption; use the <caption> element instead.'],
        ];
    }
}
