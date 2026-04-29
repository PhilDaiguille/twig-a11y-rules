<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\BannedTagsRule;
use TwigCsFixer\Environment\StubbedEnvironment;
use TwigCsFixer\Ruleset\Ruleset;
use TwigCsFixer\Runner\Linter;
use TwigCsFixer\Test\AbstractRuleTestCase;
use TwigCsFixer\Token\Tokenizer;

/**
 * @internal
 */
#[CoversClass(BannedTagsRule::class)]
final class BannedTagsRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $env = new StubbedEnvironment();
        $tokenizer = new Tokenizer($env);
        $linter = new Linter($env, $tokenizer);
        $ruleset = new Ruleset();
        $ruleset->addRule(new BannedTagsRule());

        $report = $linter->run([new \SplFileInfo($fixture)], $ruleset);
        $violations = $report->getFileViolations($fixture);

        $messages = [];
        foreach ($violations as $violation) {
            $id = $violation->getIdentifier();
            $messages[$id?->toString() ?? ''] = $violation->getMessage();
        }

        $this->assertSame($expectedErrors, $messages);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid content' => [__DIR__.'/Fixtures/valid/no_banned.html.twig', []];

        yield 'invalid marquee' => [__DIR__.'/Fixtures/invalid/has_marquee.html.twig', ['BannedTags.BannedTags.Used:1:1' => 'Banned tag used (e.g. <marquee> or <blink>).']];
    }
}
