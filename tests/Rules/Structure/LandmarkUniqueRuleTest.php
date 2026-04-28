<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\LandmarkUniqueRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversNothing]
final class LandmarkUniqueRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new LandmarkUniqueRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'duplicate main landmarks' => [__DIR__.'/Fixtures/invalid/duplicate_main.html.twig', [
            'LandmarkUnique.Landmark.Unique:1:1' => 'Landmark role="main" appears 2 times; landmarks should be unique.',
        ]];
    }
}
