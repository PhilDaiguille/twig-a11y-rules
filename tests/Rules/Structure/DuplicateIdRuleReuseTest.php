<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use TwigA11y\Rules\Structure\DuplicateIdRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(DuplicateIdRule::class)]
final class DuplicateIdRuleReuseTest extends AbstractRuleTestCase
{
    public function testRuleWorksWhenTheSameInstanceIsReusedAcrossFiles(): void
    {
        $rule = new DuplicateIdRule();

        $this->checkRule($rule, [], __DIR__.'/Fixtures/valid/valid.html.twig');
        // Note: duplicate_ids.html.twig contains duplicate id "foo"
        $this->checkRule($rule, ['DuplicateId.DuplicateId.Duplicate:1:1' => 'Duplicate id "foo" found in document.'], __DIR__.'/Fixtures/invalid/duplicate_ids.html.twig');
    }
}
