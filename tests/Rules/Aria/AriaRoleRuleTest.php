<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaRoleRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
final class AriaRoleRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaRoleRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid role' => [__DIR__.'/Fixtures/valid/role_valid.html.twig', []];

        yield 'invalid role' => [__DIR__.'/Fixtures/invalid/role_invalid.html.twig', ['AriaRole.AriaRole.InvalidRole:1:1' => 'Invalid ARIA role "marquee".']];
    }
}
