<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

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
