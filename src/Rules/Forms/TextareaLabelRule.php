<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

final class TextareaLabelRule extends AbstractFormFieldLabelRule
{
    #[\Override]
    protected function tagName(): string
    {
        return 'textarea';
    }

    #[\Override]
    protected function missingMessage(): string
    {
        return 'Textarea must have an associated <label>.';
    }

    #[\Override]
    protected function messageId(): string
    {
        return 'Missing';
    }
}
