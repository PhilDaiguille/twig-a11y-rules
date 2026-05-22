<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaErrorMessageIdExistsRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(AriaErrorMessageIdExistsRule::class)]
final class AriaErrorMessageIdExistsRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaErrorMessageIdExistsRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'aria errormessage existing id' => [__DIR__.'/Fixtures/valid/aria_errormessage_existing_id.html.twig', []];

        yield 'aria errormessage missing id' => [__DIR__.'/Fixtures/invalid/aria_errormessage_missing_id.html.twig', [
            'AriaErrorMessageIdExists.AriaErrorMessage.MissingId:2:1' => 'Referenced id "email-error" in aria-errormessage does not exist in template.',
        ]];
    }
}
