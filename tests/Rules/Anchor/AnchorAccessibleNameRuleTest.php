<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Anchor;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Anchor\AnchorAccessibleNameRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
final class AnchorAccessibleNameRuleTest extends AbstractRuleTestCase
{
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AnchorAccessibleNameRule(), $expectedErrors, $fixture);
    }

    public static function provideFixtures(): iterable
    {
        yield 'link with image alt' => [
            __DIR__.'/Fixtures/valid/link_with_img_alt.html.twig',
            [],
        ];

        yield 'link without name' => [
            __DIR__.'/Fixtures/invalid/link_without_name.html.twig',
            ['AnchorAccessibleName.Anchor.AccessibleName:4:5' => 'Anchor element without accessible name (axe-core: link-name).'],
        ];
    }
}
