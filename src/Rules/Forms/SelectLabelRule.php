<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

final class SelectLabelRule extends AbstractFormFieldLabelRule
{
    #[\Override]
    protected function tagName(): string
    {
        return 'select';
    }

    #[\Override]
    protected function missingMessage(): string
    {
        return 'Select element must have an associated <label>.';
    }

    #[\Override]
    protected function messageId(): string
    {
        return 'Missing';
    }
}
