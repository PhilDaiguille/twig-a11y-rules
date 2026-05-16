<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaControlsIdExistsRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(AriaControlsIdExistsRule::class)]
final class AriaControlsIdExistsRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaControlsIdExistsRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'aria controls existing id' => [__DIR__.'/Fixtures/valid/aria_controls_existing_id.html.twig', []];

        yield 'aria controls missing id' => [__DIR__.'/Fixtures/invalid/aria_controls_missing_id.html.twig', [
            'AriaControlsIdExists.AriaControls.MissingId:2:1' => 'Referenced id "panel-1" in aria-controls does not exist in template.',
        ]];
    }
}
