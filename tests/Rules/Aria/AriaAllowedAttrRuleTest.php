<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaAllowedAttrRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AriaAllowedAttrRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaAllowedAttrRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<(array<int, array<string, string>>|array<int, string>)>
     */
    public static function provideFixtures(): iterable
    {
        yield 'aria attr not allowed' => [__DIR__.'/Fixtures/invalid/aria_attr_not_allowed.html.twig', [
            'AriaAllowedAttr.AriaAllowed.Invalid:1:1' => 'Attribute aria-checked is not allowed on role button.',
        ]];
    }
}
