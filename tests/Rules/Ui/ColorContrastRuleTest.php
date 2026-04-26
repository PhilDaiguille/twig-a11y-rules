<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Ui;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Ui\ColorContrastRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
final class ColorContrastRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new ColorContrastRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'good contrast' => [__DIR__.'/Fixtures/valid/good_contrast.html.twig', []];

        yield 'bad contrast' => [__DIR__.'/Fixtures/invalid/bad_contrast.html.twig', [
            'ColorContrast.ColorContrast.Insufficient:1:1' => 'Insufficient color contrast',
        ]];
    }
}
