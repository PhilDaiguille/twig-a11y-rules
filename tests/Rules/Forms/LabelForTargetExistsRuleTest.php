<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\LabelForTargetExistsRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(LabelForTargetExistsRule::class)]
final class LabelForTargetExistsRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new LabelForTargetExistsRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'label target exists' => [__DIR__.'/Fixtures/valid/label_target_exists.html.twig', []];

        yield 'label target missing' => [__DIR__.'/Fixtures/invalid/label_target_missing.html.twig', [
            'LabelForTargetExists.LabelFor.MissingTarget:2:1' => 'Label for="email" does not reference any existing id in template.',
        ]];
    }
}
