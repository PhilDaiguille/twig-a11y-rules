<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\FrameTitleRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(FrameTitleRule::class)]
final class FrameTitleRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new FrameTitleRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'no title' => [
            __DIR__.'/Fixtures/invalid/frame_no_title.html.twig',
            ['FrameTitle.FrameTitle.Missing:1:1' => 'Frame element must have a non-empty title attribute.'],
        ];

        yield 'with title' => [__DIR__.'/Fixtures/valid/frame_with_title.html.twig', []];
    }
}
