<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

final class InputLabelRule extends AbstractFormFieldLabelRule
{
    protected function tagName(): string
    {
        return 'input';
    }

    protected function missingMessage(): string
    {
        return 'Input element must have an associated <label> or an aria-label.';
    }

    protected function messageId(): string
    {
        return 'InputLabel.MissingLabel';
    }

    protected function openingProvidesLabel(string $opening): bool
    {
        // aria-label or aria-labelledby on the input itself is acceptable
        if (preg_match('/\baria-label\s*=\s*(?:"|\')/i', $opening)) {
            return true;
        }

        return (bool) preg_match('/\baria-labelledby\s*=\s*(?:"|\')/i', $opening);
    }

    protected function isHidden(string $opening): bool
    {
        return (bool) preg_match('/\btype\s*=\s*["\']hidden["\']/i', $opening);
    }
}
