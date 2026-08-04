<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Tests\Rules\Forms\TextareaLabelRuleTest;

/**
 * @see TextareaLabelRuleTest
 * @see TextareaLabelRuleTest
 * @see TextareaLabelRuleTest
 * @see TextareaLabelRuleTest
 * @see TextareaLabelRuleTest
 * @see TextareaLabelRuleTest
 */
final class TextareaLabelRule extends AbstractFormFieldLabelRule
{
    protected function tagName(): string
    {
        return 'textarea';
    }

    protected function missingMessage(): string
    {
        return 'Textarea must have an associated <label>.';
    }

    protected function messageId(): string
    {
        return 'TextareaLabel.Missing';
    }
}
