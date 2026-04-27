<?php

declare(strict_types=1);

namespace TwigA11y\Rules;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

abstract class AbstractA11yRule extends AbstractRule implements EvaluatableRuleInterface
{
    use TokenCollectorTrait;

    // By default rules apply to all template kinds. Rules that should be
    // limited to specific kinds can override supportedKinds().
    protected function supportedKinds(): array
    {
        return \TwigA11y\Template\TemplateKind::cases();
    }

    final protected function process(int $tokenIndex, Tokens $tokens): void
    {
        // On the first token, determine the template kind and bail out if
        // this rule does not support that kind.
        if (0 === $tokenIndex) {
            $kind = \TwigA11y\Template\TemplateClassifier::classify(
                $this->getFullContent($tokens)
            );

            if (!in_array($kind, $this->supportedKinds(), true)) {
                return;
            }
        }

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
