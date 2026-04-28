<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use TwigA11y\Rules\Structure\HeadingOrderRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class HeadingOrderRuleReuseTest extends AbstractRuleTestCase
{
    public function testRuleWorksWhenTheSameInstanceIsReusedAcrossFiles(): void
    {
        $rule = new HeadingOrderRule();

        $this->checkRule($rule, [], __DIR__.'/Fixtures/valid/headings_ok_more.html.twig');
        $this->checkRule($rule, ['HeadingOrder.HeadingOrder.Invalid:1:1' => 'Heading level jumped from h1 to h3.'], __DIR__.'/Fixtures/invalid/headings_jump.html.twig');
    }
}
