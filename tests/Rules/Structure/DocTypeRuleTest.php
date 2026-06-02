<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\DocTypeRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(DocTypeRule::class)]
final class DocTypeRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new DocTypeRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'doctype present' => [__DIR__.'/Fixtures/valid/doctype_present.html.twig', []];

        yield 'doctype missing' => [__DIR__.'/Fixtures/invalid/doctype_missing.html.twig', [
            'DocType.MissingDoctype:1:1' => 'Full-page document is missing a <!DOCTYPE html> declaration (RGAA 8.1).',
        ]];
    }
}
