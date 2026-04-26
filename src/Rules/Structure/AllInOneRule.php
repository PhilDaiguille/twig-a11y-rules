<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use Closure;
use TwigA11y\Rules\Aria\TabIndexRule;
use TwigA11y\Rules\Media\ImgAltRule;
use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * Convenience rule that delegates to existing smaller rules.
 *
 * It simply instantiates a set of delegate rule objects and invokes their
 * protected process() methods using a Closure bound to the delegate instance.
 * This preserves the original error identifiers and messages produced by
 * the delegated rules (BannedTags.*, ImgAlt.*, etc.), avoiding any need to
 * change those rules for DX.
 */
final class AllInOneRule extends AbstractRule
{
    /** @var null|array<object> */
    private ?array $delegates = null;

    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        if (null === $this->delegates) {
            // instantiate delegates per instance to avoid shared mutable state
            $this->delegates = [
                new BannedTagsRule(),
                new ImgAltRule(),
                new LangAttributeRule(),
                new TabIndexRule(),
                new ButtonContentRule(),
                new AnchorContentRule(),
            ];
        }

        foreach ($this->delegates as $delegate) {
            // Each delegate provides an evaluate(Tokens,int,callable) method that
            // will call the provided $emit callable for each finding. The callable
            // should accept (string $message, Token $token, ?string $id).
            $emit = function (string $message, Token $token, ?string $id = null): void {
                if (null !== $id && str_contains($id, 'Warning')) {
                    // Preserve warning semantics when delegate emitted a warning id
                    $this->addWarning($message, $token, $id);
                } else {
                    $this->addError($message, $token, $id);
                }
            };

            if (method_exists($delegate, 'evaluate')) {
                $delegate->evaluate($tokens, $tokenIndex, $emit);
            }
        }
    }
}
