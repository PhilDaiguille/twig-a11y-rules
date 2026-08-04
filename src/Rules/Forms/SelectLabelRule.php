<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Tests\Rules\Forms\SelectLabelRuleTest;

/**
 * @see SelectLabelRuleTest
 * @see SelectLabelRuleTest
 * @see SelectLabelRuleTest
 * @see SelectLabelRuleTest
 * @see SelectLabelRuleTest
 * @see SelectLabelRuleTest
 */
final class SelectLabelRule extends AbstractFormFieldLabelRule
{
    protected function tagName(): string
    {
        return 'select';
    }

    protected function missingMessage(): string
    {
        return 'Select element must have an associated <label>.';
    }

    protected function messageId(): string
    {
        return 'SelectLabel.Missing';
    }
}
