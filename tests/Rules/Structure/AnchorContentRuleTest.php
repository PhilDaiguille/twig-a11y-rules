<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\AnchorContentRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AnchorContentRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<null|string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AnchorContentRule(), $expectedErrors, $fixture);
    }

    /**
     * @return iterable<string, array{0:string,1:array<null|string>}>
     */
    public static function provideFixtures(): iterable
    {
        yield 'anchor with text' => [
            __DIR__.'/Fixtures/valid/anchor_with_text.html.twig',
            [],
        ];

        yield 'anchor empty without accessible name' => [
            __DIR__.'/Fixtures/invalid/anchor_empty.html.twig',
            ['AnchorContent.AnchorContent.Warning.LinkName:3:1' => 'Anchor element without accessible name (axe-core: link-name) should have an aria-label or title.'],
        ];
    }
}
