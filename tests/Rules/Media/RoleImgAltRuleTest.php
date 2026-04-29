<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Media;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Media\RoleImgAltRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class RoleImgAltRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new RoleImgAltRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<(array<int, array<string, string>>|array<int, string>)>
     */
    public static function provideFixtures(): iterable
    {
        yield 'svg role img without title' => [__DIR__.'/Fixtures/invalid/svg_role_img_no_title.html.twig', [
            'RoleImgAlt.RoleImg.MissingTitle:1:1' => 'SVG with role="img" must include a <title>.',
        ]];
    }
}
