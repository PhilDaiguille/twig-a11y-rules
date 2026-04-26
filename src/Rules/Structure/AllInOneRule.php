<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use Closure;
use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Rules\Aria\AriaLabelRule;
use TwigA11y\Rules\Aria\AriaRoleRule;
use TwigA11y\Rules\Aria\TabIndexRule;
use TwigA11y\Rules\Forms\FormLabelRule;
use TwigA11y\Rules\Forms\InputLabelRule;
use TwigA11y\Rules\Forms\SelectLabelRule;
use TwigA11y\Rules\Forms\TextareaLabelRule;
use TwigA11y\Rules\Media\AutoplayRule;
use TwigA11y\Rules\Media\ImgAltRule;
use TwigA11y\Rules\Media\ObjectAltRule;
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
final class AllInOneRule extends AbstractA11yRule
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

                // Additional delegates to make AllInOneRule more comprehensive
                new AriaLabelRule(),
                new AriaRoleRule(),
                new FormLabelRule(),
                new InputLabelRule(),
                new SelectLabelRule(),
                new TextareaLabelRule(),
                new AutoplayRule(),
                new ObjectAltRule(),
            ];
        }

        foreach ($this->delegates as $delegate) {
            // The delegate may provide a public evaluate(Tokens,int,callable) API
            // or a protected process(int, Tokens) method. Support both to
            // interoperate with existing rule implementations.
            $emit = function (string $message, Token $token, ?string $id = null): void {
                if (null !== $id && str_contains($id, 'Warning')) {
                    $this->addWarning($message, $token, $id);
                } else {
                    $this->addError($message, $token, $id);
                }
            };

            if (method_exists($delegate, 'evaluate')) {
                $delegate->evaluate($tokens, $tokenIndex, $emit);

                continue;
            }

            // If the delegate exposes a protected process() method (existing
            // rule implementations do), invoke it via Reflection to avoid
            // changing visibility. Fall back to skipping the delegate if not
            // present.
            if (method_exists($delegate, 'process')) {
                $ref = new \ReflectionMethod($delegate, 'process');
                // setAccessible(true) is deprecated on recent PHP versions and
                // has no effect since PHP 8.1. We therefore invoke the method
                // directly; tests run under PHP 8.5 and this avoids deprecation
                // notices while preserving behavior.
                $ref->invoke($delegate, $tokenIndex, $tokens);
            }
        }
    }
}
