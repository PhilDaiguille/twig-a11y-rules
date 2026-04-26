<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaLabelRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AriaLabelRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaLabelRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid landmark' => [__DIR__.'/Fixtures/valid/landmark_with_label.html.twig', []];

        yield 'invalid landmark' => [__DIR__.'/Fixtures/invalid/landmark_missing_label.html.twig', ['AriaLabel.AriaLabel.MissingOrEmpty:1:7' => 'Landmark elements should have a non-empty aria-label.']];
    }
}
