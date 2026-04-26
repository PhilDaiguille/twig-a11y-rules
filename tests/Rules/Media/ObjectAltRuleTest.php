<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Media;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Media\ObjectAltRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

final class ObjectAltRuleTest extends AbstractRuleTestCase
{
    /** @param array<string|null> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new ObjectAltRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string|null>}> */
    public static function provideFixtures(): iterable
    {
        yield 'no alt' => [__DIR__.'/Fixtures/invalid/object_no_alt.html.twig', ['ObjectAlt.ObjectAlt.Missing:1:1' => 'Object element should have alternative text.']];
        yield 'ok' => [__DIR__.'/Fixtures/valid/img_with_alt.html.twig', []];
    }
}
