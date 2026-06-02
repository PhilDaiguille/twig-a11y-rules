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

    protected function isHidden(string $opening): bool
    {
        return (bool) preg_match('/\btype\s*=\s*["\']hidden["\']/i', $opening);
    }
}
