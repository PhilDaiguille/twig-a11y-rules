<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Integration;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Standard\StandardRuleSets;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * Runs the whole strict rule set over markup that is genuinely accessible.
 *
 * Per-rule fixtures only exercise one rule at a time, which hides false
 * positives coming from another rule reading the token stream differently —
 * the tokenizer splits on whitespace, so a tag can start mid-token ("<li><a")
 * or carry its attributes across several tokens.
 *
 * @internal
 */
#[CoversNothing]
final class NoFalsePositivesTest extends AbstractRuleTestCase
{
    #[DataProvider('provideFixtures')]
    public function testAccessibleMarkupReportsNothing(string $fixture): void
    {
        $this->checkRule(StandardRuleSets::strict(), [], $fixture);
    }

    /**
     * @return iterable<string, array{0:string}>
     */
    public static function provideFixtures(): iterable
    {
        foreach (glob(__DIR__.'/Fixtures/no-false-positives/*.html.twig') ?: [] as $fixture) {
            yield basename($fixture, '.html.twig') => [$fixture];
        }
    }
}
