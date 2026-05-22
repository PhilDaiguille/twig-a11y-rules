<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\InvalidFieldErrorMessageRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(InvalidFieldErrorMessageRule::class)]
final class InvalidFieldErrorMessageRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new InvalidFieldErrorMessageRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'invalid field with describedby' => [__DIR__.'/Fixtures/valid/invalid_field_with_describedby.html.twig', []];

        yield 'invalid field with errormessage' => [__DIR__.'/Fixtures/valid/invalid_field_with_errormessage.html.twig', []];

        yield 'invalid field missing message reference' => [__DIR__.'/Fixtures/invalid/invalid_field_missing_message_reference.html.twig', [
            'InvalidFieldErrorMessage.InvalidFieldErrorMessage.MissingReference:2:1' => 'Invalid form fields should reference help or error text via aria-describedby or aria-errormessage.',
        ]];
    }
}
