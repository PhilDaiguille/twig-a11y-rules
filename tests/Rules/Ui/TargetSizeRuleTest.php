<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Ui;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Ui\TargetSizeRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class TargetSizeRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new TargetSizeRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'small interactive' => [__DIR__.'/Fixtures/invalid/button_small_inline.html.twig', [
            'TargetSize.TargetSize.Small:1:1' => 'Interactive element has inline size < 24px; this may fail target-size (WCAG 2.5.8).',
        ]];
    }
}
