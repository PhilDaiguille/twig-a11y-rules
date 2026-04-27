<?php

declare(strict_types=1);

namespace TwigA11y\Rules;

use TwigCsFixer\Token\Tokens;

interface EvaluatableRuleInterface
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void;
}
