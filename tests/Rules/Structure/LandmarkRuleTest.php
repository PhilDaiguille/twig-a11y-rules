<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\LandmarkRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
final class LandmarkRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new LandmarkRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'has main' => [__DIR__.'/Fixtures/valid/has_main.html.twig', []];

        yield 'has role main' => [__DIR__.'/Fixtures/valid/has_role_main.html.twig', []];

        yield 'missing main' => [__DIR__.'/Fixtures/invalid/no_main.html.twig', [
            'Landmark.Landmark.MissingMain:1:1' => 'Page should include a main landmark',
        ]];
    }
}
