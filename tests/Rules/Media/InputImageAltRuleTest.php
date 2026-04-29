<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Media;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Media\InputImageAltRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(InputImageAltRule::class)]
final class InputImageAltRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new InputImageAltRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'no alt' => [
            __DIR__.'/Fixtures/invalid/input_image_no_alt.html.twig',
            ['InputImageAlt.InputImageAlt.Missing:2:1' => '<input type="image"> must have a non-empty alt attribute.'],
        ];

        yield 'with alt' => [__DIR__.'/Fixtures/valid/input_image_with_alt.html.twig', []];
    }
}
