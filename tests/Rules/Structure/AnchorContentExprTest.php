<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use TwigA11y\Rules\Structure\AnchorContentRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AnchorContentExprTest extends AbstractRuleTestCase
{
    public function testAnchorWithTwigExpressionInTitleIsConsideredAccessible(): void
    {
        // Both anchors include a title attribute (one via Twig expression), so
        // no warning should be emitted by the rule.
        $this->checkRule(new AnchorContentRule(), [], __DIR__.'/Fixtures/valid/expr_in_attr_anchor.html.twig');
    }
}
