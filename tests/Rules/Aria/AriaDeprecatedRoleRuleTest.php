<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaDeprecatedRoleRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AriaDeprecatedRoleRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaDeprecatedRoleRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<array<int, (array<mixed>|string)>>
     */
    public static function provideFixtures(): iterable
    {
        yield 'valid role' => [__DIR__.'/Fixtures/valid/role_valid.html.twig', []];

        yield 'deprecated role' => [
            __DIR__.'/Fixtures/invalid/role_deprecated.html.twig',
            [
                // Warning keys are still produced via the test harness; use the
                // expected key format combining rule shortname + provided id
                'AriaDeprecatedRole.AriaDeprecated.Deprecated:1:1' => 'ARIA role "directory" is deprecated.',
            ],
        ];
    }
}
