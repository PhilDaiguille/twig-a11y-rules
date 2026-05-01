<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\GenericLinkTextRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(GenericLinkTextRule::class)]
final class GenericLinkTextRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new GenericLinkTextRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid descriptive link' => [__DIR__.'/Fixtures/valid/link_descriptive_text.html.twig', []];

        yield 'invalid generic link text' => [
            __DIR__.'/Fixtures/invalid/link_generic_text.html.twig',
            ['GenericLinkText.GenericLinkText.Generic:1:1' => 'Avoid generic link text "click here"; use descriptive text that explains the link destination.'],
        ];
    }
}
