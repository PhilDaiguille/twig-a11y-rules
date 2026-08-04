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

    /**
     * These input types take no <label>: hidden is not rendered, and the others
     * get their accessible name from value= or alt=. Those are checked by
     * InputButtonNameRule and InputImageAltRule instead.
     */
    #[\Override]
    protected function isHidden(string $opening): bool
    {
        return (bool) preg_match('/\btype\s*=\s*["\'](?:hidden|submit|reset|button|image)["\']/i', $opening);
    }
}
