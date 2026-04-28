<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

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

    protected function openingProvidesLabel(string $opening): bool
    {
        // aria-labelledby or aria-label on the select itself is acceptable
        if (preg_match('/\baria-labelledby\s*=\s*(?:"|\')/i', $opening)) {
            return true;
        }

        return (bool) preg_match('/\baria-label\s*=\s*(?:"|\')/i', $opening);
    }
}
