<?php

declare(strict_types=1);

namespace TwigA11y\Rules;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

abstract class AbstractA11yRule extends AbstractRule implements EvaluatableRuleInterface
{
    use TokenCollectorTrait;

    final protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $this->evaluate($tokens, $tokenIndex, $this->createEmitter());
    }

    protected function emitsWarnings(): bool
    {
        return false;
    }

    protected function getFullContent(Tokens $tokens): string
    {
        $content = '';
        foreach ($tokens->toArray() as $token) {
            $content .= $token->getValue();
        }

        return $content;
    }

    private function createEmitter(): callable
    {
        if ($this->emitsWarnings()) {
            return function (string $message, Token $token, ?string $id = null): void {
                if (null === $id) {
                    $this->addWarning($message, $token);

                    return;
                }

                $this->addWarning($message, $token, $id);
            };
        }

        return function (string $message, Token $token, ?string $id = null): void {
            $this->addError($message, $token, $id);
        };
    }
}
