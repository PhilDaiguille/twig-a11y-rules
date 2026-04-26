<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\AllInOneRule;
use TwigCsFixer\Environment\StubbedEnvironment;
use TwigCsFixer\Ruleset\Ruleset;
use TwigCsFixer\Runner\Linter;
use TwigCsFixer\Test\AbstractRuleTestCase;
use TwigCsFixer\Token\Tokenizer;

#[\PHPUnit\Framework\Attributes\CoversNothing]
/** @internal */
final class AllInOneRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        // Use the same linter flow as other rule tests to collect violations
        $env = new StubbedEnvironment();
        $tokenizer = new Tokenizer($env);
        $linter = new Linter($env, $tokenizer);
        $ruleset = new Ruleset();
        $ruleset->addRule(new AllInOneRule());

        $report = $linter->run([new \SplFileInfo($fixture)], $ruleset);
        $violations = $report->getFileViolations($fixture);

        $messages = [];
        foreach ($violations as $violation) {
            $id = $violation->getIdentifier();
            $messages[$id?->toString() ?? ''] = $violation->getMessage();
        }

        if ([] === $expectedErrors) {
            // valid fixture: expect no violations
            self::assertSame([], $messages);

            return;
        }

        // For invalid fixtures: ensure keys exist and messages contain expected substring
        foreach ($expectedErrors as $key => $msg) {
            self::assertArrayHasKey($key, $messages);
            $actual = $messages[$key];

            // Some fixtures use null to indicate we only expect the identifier,
            // not to assert a message substring. Skip message assertions when
            // the expected message is null.
            if (null === $msg) {
                continue;
            }

            if (!str_contains($actual, $msg)) {
                // tolerate a single trailing dot mismatch (punctuation differences)
                $alt = str_ends_with($msg, '.') ? rtrim($msg, '.') : $msg.'.';
                self::assertTrue(str_contains($actual, $alt), "Message for {$key} did not match expected (got: '{$actual}', expected: '{$msg}').");
            }
        }
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid content' => [__DIR__.'/Fixtures/valid/valid.html.twig', []];

        yield 'invalid content' => [__DIR__.'/Fixtures/invalid/invalid.html.twig', [
            'AllInOne.LangAttribute.MissingLang:1:1' => 'The <html> element should have a lang attribute.',
            'AllInOne.BannedTags.Used:3:5' => 'Banned tag used (e.g. <marquee> or <blink>).',
            'AllInOne.ImgAlt.MissingAlt:4:5' => 'Missing alt attribute on <img> tag.',
            'AllInOne.TabIndex.PositiveTabindex:5:10' => 'Avoid positive tabindex values — use 0 or manage focus order differently.',
            'AllInOne.ButtonContent.MissingContent:6:5' => 'Button element without textual content must have an aria-label.',
            'AllInOne.AnchorContent.Warning.LinkName:7:5' => 'Anchor element without accessible name (axe-core: link-name) should have an aria-label or title.',
        ]];
    }
}
