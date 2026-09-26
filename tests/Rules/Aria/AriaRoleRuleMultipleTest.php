<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\CoversClass;
use TwigA11y\Rules\Aria\AriaRoleRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(AriaRoleRule::class)]
final class AriaRoleRuleMultipleTest extends AbstractRuleTestCase
{
    public function testMultipleInvalidRolesAreReported(): void
    {
        $this->checkRule(new AriaRoleRule(), [
            'AriaRole.InvalidRole:2:12' => 'Invalid ARIA role "foo".',
            'AriaRole.InvalidRole:3:13' => 'Invalid ARIA role "bar".',
        ], __DIR__.'/Fixtures/invalid/role_multiple_invalid.html.twig');
    }
}
