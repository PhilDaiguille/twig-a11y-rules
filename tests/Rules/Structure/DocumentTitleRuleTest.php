<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\DocumentTitleRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class DocumentTitleRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new DocumentTitleRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<array<int, (array<mixed>|string)>>
     */
    public static function provideFixtures(): iterable
    {
        yield 'with title' => [
            __DIR__.'/Fixtures/valid/with_title.html.twig',
            [],
        ];

        yield 'no title' => [
            __DIR__.'/Fixtures/invalid/no_title.html.twig',
            ['DocumentTitle.DocumentTitle.Missing:1:1' => 'Document should include a non-empty <title> element.'],
        ];
    }
}
