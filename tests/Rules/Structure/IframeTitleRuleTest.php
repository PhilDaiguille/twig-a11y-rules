<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\IframeTitleRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class IframeTitleRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new IframeTitleRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'no title' => [__DIR__.'/Fixtures/invalid/iframe_no_title.html.twig', ['IframeTitle.IframeTitle.Missing:1:1' => 'Iframe must have a non-empty title attribute.']];

        yield 'ok' => [__DIR__.'/Fixtures/valid/no_banned.html.twig', []];
    }
}
