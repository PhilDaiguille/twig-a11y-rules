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
        // Delegate to the base implementation which checks for non-empty values.
        // Both aria-label (non-empty) and aria-labelledby (non-empty) are acceptable.
        return parent::openingProvidesLabel($opening);
    }

    protected function isHidden(string $opening): bool
    {
        return (bool) preg_match('/\btype\s*=\s*["\']hidden["\']/i', $opening);
    }
}
