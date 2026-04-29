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
final class AriaRoleRuleReuseTest extends AbstractRuleTestCase
{
    public function testRuleWorksWhenTheSameInstanceIsReusedAcrossFiles(): void
    {
        $rule = new AriaRoleRule();

        $this->checkRule($rule, [], __DIR__.'/Fixtures/valid/role_valid.html.twig');
        $this->checkRule($rule, ['AriaRole.AriaRole.InvalidRole:1:1' => 'Invalid ARIA role "marquee".'], __DIR__.'/Fixtures/invalid/role_invalid.html.twig');
    }
}
