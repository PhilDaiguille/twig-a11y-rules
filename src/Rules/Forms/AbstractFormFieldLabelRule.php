<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

abstract class AbstractFormFieldLabelRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        $tag = $this->tagName();
        if (!str_contains($value, '<'.$tag)) {
            return;
        }

        $opening = $this->collectUntil($tokenIndex, $tokens, '>', 50);

        if ($this->openingProvidesLabel($opening)) {
            return;
        }

        if ($this->isHidden($opening)) {
            return;
        }

        $id = $this->extractFirstId($opening);
        $full = $this->getFullContent($tokens);

        if ('' !== $id && $this->hasLabelFor($full, $id)) {
            return;
        }

        // Label wrapping the field
        if (preg_match('/<label[^>]*>\s*<'.preg_quote($tag, '/').'[^>]*>/i', $full)) {
            return;
        }

        $emit($this->missingMessage(), $token, $this->messageId());
    }

    /**
     * Name of the HTML tag this rule targets, e.g. 'input', 'select', 'textarea'.
     */
    abstract protected function tagName(): string;

    abstract protected function missingMessage(): string;

    abstract protected function messageId(): string;

    /**
     * Allow subclasses to perform extra checks on the opening tag. Return true
     * when the opening tag already provides a label (eg. aria-labelledby).
     */
    protected function openingProvidesLabel(string $opening): bool
    {
        return (bool) preg_match('/\baria-label\s*=\s*(?:"|\')/i', $opening);
    }

    /**
     * Some tags may be considered hidden (eg. <input type="hidden">) and do
     * not require labels. Subclasses may override.
     */
    protected function isHidden(string $opening): bool
    {
        return false;
    }
}
