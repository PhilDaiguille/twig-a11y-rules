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

                // Create a Closure from the delegate's protected method and
                // rebind its $this to the current AllInOneRule instance so
                // that calls to $this->addError/addWarning inside the
                // delegate are routed through AllInOneRule. This preserves
                // the AllInOne.* identifiers expected by callers.
                // Ensure the delegate has the same report context so its
                // addError/addWarning calls actually emit into the current
                // report. The RuleTrait defines private properties on the
                // delegate that we need to copy across.
                $thisReflection = new \ReflectionObject($this);
                $reportVal = null;
                $ignoredVal = [];
                if ($thisReflection->hasProperty('report')) {
                    $p = $thisReflection->getProperty('report');
                    $p->setAccessible(true);
                    $reportVal = $p->getValue($this);
                }
                if ($thisReflection->hasProperty('ignoredViolations')) {
                    $p = $thisReflection->getProperty('ignoredViolations');
                    $p->setAccessible(true);
                    $ignoredVal = $p->getValue($this);
                }

                    $delegateReflection = new \ReflectionObject($delegate);

                    // Find property in class hierarchy (may be declared on a
                    // parent class where the trait is applied).
                    $propClass = $delegateReflection;
                    while ($propClass && !$propClass->hasProperty('report')) {
                        $propClass = $propClass->getParentClass();
                    }
                    if ($propClass && $propClass->hasProperty('report')) {
                        $p = $propClass->getProperty('report');
                        $p->setAccessible(true);
                        $p->setValue($delegate, $reportVal);
                    }

                    $propClass = $delegateReflection;
                    while ($propClass && !$propClass->hasProperty('ignoredViolations')) {
                        $propClass = $propClass->getParentClass();
                    }
                    if ($propClass && $propClass->hasProperty('ignoredViolations')) {
                        $p = $propClass->getProperty('ignoredViolations');
                        $p->setAccessible(true);
                        $p->setValue($delegate, $ignoredVal);
                    }

                // Attempt to obtain a Closure for the non-public method.
                // Calling setAccessible() is deprecated on some PHP versions
                // so suppress the deprecation warning when necessary.
                if (!$ref->isPublic()) {
                    // @ operator hides deprecation warnings about setAccessible
                    @\Closure::fromCallable(function (): void {});
                    @$ref->setAccessible(true);
                }

                $closure = $ref->getClosure($delegate);
                if (null === $closure) {
                    // As a last resort, invoke directly on the delegate. This
                    // will cause messages to be reported under the delegate
                    // rule name rather than AllInOne, but ensures we don't
                    // lose violations when reflection cannot produce a
                    // callable.
                    $ref->invoke($delegate, $tokenIndex, $tokens);

                    continue;
                }

                $bound = @\Closure::bind($closure, $this, \get_class($delegate));
                if (is_callable($bound)) {
                    $bound($tokenIndex, $tokens);
                } else {
                    // Fallback: invoke directly if we couldn't obtain a
                    // callable bound closure.
                    $ref->invoke($delegate, $tokenIndex, $tokens);
                }
            }
        }
    }
}
