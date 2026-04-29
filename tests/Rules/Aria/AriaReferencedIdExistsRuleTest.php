<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaReferencedIdExistsRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @covers \TwigA11y\Rules\Aria\AriaReferencedIdExistsRule
 */
final class AriaReferencedIdExistsRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaReferencedIdExistsRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<(array<int, array<string, string>>|array<int, string>)>
     */
    public static function provideFixtures(): iterable
    {
        yield 'missing referenced id' => [__DIR__.'/Fixtures/invalid/aria_labelledby_missing_id.html.twig', [
            'AriaReferencedIdExists.AriaRef.MissingId:2:1' => 'Referenced id "missing-id" in aria attribute does not exist in template.',
        ]];
    }
}
