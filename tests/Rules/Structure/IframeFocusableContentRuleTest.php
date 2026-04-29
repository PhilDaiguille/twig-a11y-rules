<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\IframeFocusableContentRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @covers \TwigA11y\Rules\Structure\IframeFocusableContentRule
 */
final class IframeFocusableContentRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new IframeFocusableContentRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<(array<int, array<string, string>>|array<int, string>)>
     */
    public static function provideFixtures(): iterable
    {
        yield 'iframe tabindex -1 with focusable content' => [__DIR__.'/Fixtures/invalid/iframe_tabindex_focusable.html.twig', [
            'IframeFocusableContent.Iframe.FocusableContent:2:1' => 'Iframe has tabindex="-1" but contains focusable content.',
        ]];
    }
}
