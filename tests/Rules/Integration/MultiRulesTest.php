<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Integration;

use PHPUnit\Framework\Attributes\CoversNothing;
use TwigA11y\Rules\Aria\AriaRoleRule;
use TwigA11y\Rules\Structure\AnchorContentRule;
use TwigA11y\Rules\Structure\ButtonContentRule;
use TwigA11y\Rules\Structure\DuplicateIdRule;
use TwigA11y\Rules\Structure\HeadingOrderRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversNothing]
final class MultiRulesTest extends AbstractRuleTestCase
{
    public function testMultipleRulesProduceAllViolations(): void
    {
        $rules = [
            new HeadingOrderRule(),
            new DuplicateIdRule(),
            new AnchorContentRule(),
            new ButtonContentRule(),
            new AriaRoleRule(),
        ];

        $expects = [
            // heading jump
            'HeadingOrder.HeadingOrder.Invalid:1:1' => 'Heading level jumped from h1 to h3.',
            // duplicate id (identifier includes rule prefix + provided id)
            'DuplicateId.DuplicateId.Duplicate:1:1' => 'Duplicate id "dup" found in document.',
            // aria roles (two invalid roles)
            'AriaRole.AriaRole.InvalidRole:1:1' => 'Invalid ARIA role "foo".',
            'AriaRole.AriaRole.InvalidRole#2:1:1' => 'Invalid ARIA role "bar".',
        ];

        $this->checkRule($rules, $expects, __DIR__.'/Fixtures/multi_issues.html.twig');
    }
}
