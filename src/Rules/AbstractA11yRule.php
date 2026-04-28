<?php

declare(strict_types=1);

namespace TwigA11y\Rules;

use TwigA11y\Template\TemplateClassifier;
use TwigA11y\Template\TemplateKind;
use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

abstract class AbstractA11yRule extends AbstractRule implements EvaluatableRuleInterface
{
    use TokenCollectorTrait;

    /** Cached decision for the currently-processed file when rules are reused */
    private ?bool $skipThisFile = null;

    /**
     * Shared cache of TemplateKind decisions keyed by content hash to avoid
     * repeatedly classifying the same file across multiple rule instances.
     *
     * @var array<string, TemplateKind>
     */
    private static array $kindCache = [];

    // By default rules apply to all template kinds. Rules that should be
    // limited to specific kinds can override supportedKinds().
    /**
     * @return TemplateKind[]
     */
    protected function supportedKinds(): array
    {
        return TemplateKind::cases();
    }

    // Rules can opt to run only once per file (for page-level scans).
    protected function evaluateOncePerFile(): bool
    {
        return false;
    }

    // Backwards-compatible helper used by existing rules that used the
    // pattern "if (0 !== $tokenIndex) return;". When refactoring rules to
    // use evaluateOncePerFile(), replace those guards with a call to
    // shouldSkipByTokenIndex().
    protected function shouldSkipByTokenIndex(int $tokenIndex): bool
    {
        return $this->evaluateOncePerFile() && 0 !== $tokenIndex;
    }

    final protected function process(int $tokenIndex, Tokens $tokens): void
    {
        // On the first token, determine the template kind and record whether
        // this rule applies to the file. This supports rule instances being
        // reused across multiple files.
        if (0 === $tokenIndex) {
            $content = $this->getFullContent($tokens);
            $hash = md5($content);

            if (!isset(self::$kindCache[$hash])) {
                self::$kindCache[$hash] = TemplateClassifier::classify($content);
            }

            $kind = self::$kindCache[$hash];

            $this->skipThisFile = !in_array($kind, $this->supportedKinds(), true);
        }

        // If earlier we decided this rule doesn't apply to this file, skip.
        if (true === $this->skipThisFile) {
            return;
        }

        // If the rule only runs once per file, only evaluate at tokenIndex 0.
        if ($this->evaluateOncePerFile() && 0 !== $tokenIndex) {
            return;
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
