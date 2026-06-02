<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Standard;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use TwigA11y\Rules\Forms\InputLabelRule;
use TwigA11y\Rules\Media\ImgAltRule;
use TwigA11y\Rules\Structure\BannedTagsRule;
use TwigA11y\Rules\Structure\ButtonContentRule;
use TwigA11y\Rules\Structure\LangAttributeRule;
use TwigA11y\Standard\StandardRuleSets;
use TwigCsFixer\Rules\Delimiter\BlockNameSpacingRule;
use TwigCsFixer\Rules\Delimiter\DelimiterSpacingRule;
use TwigCsFixer\Rules\Function\IncludeFunctionRule;
use TwigCsFixer\Rules\Function\MacroArgumentNameRule;
use TwigCsFixer\Rules\Function\NamedArgumentNameRule;
use TwigCsFixer\Rules\Function\NamedArgumentSeparatorRule;
use TwigCsFixer\Rules\Function\NamedArgumentSpacingRule;
use TwigCsFixer\Rules\Literal\CompactHashRule;
use TwigCsFixer\Rules\Literal\HashQuoteRule;
use TwigCsFixer\Rules\Literal\SingleQuoteRule;
use TwigCsFixer\Rules\Operator\OperatorNameSpacingRule;
use TwigCsFixer\Rules\Operator\OperatorSpacingRule;
use TwigCsFixer\Rules\Punctuation\PunctuationSpacingRule;
use TwigCsFixer\Rules\Punctuation\TrailingCommaMultiLineRule;
use TwigCsFixer\Rules\Punctuation\TrailingCommaSingleLineRule;
use TwigCsFixer\Rules\Node\NodeRuleInterface;
use TwigCsFixer\Rules\RuleInterface;
use TwigCsFixer\Rules\Variable\VariableNameRule;
use TwigCsFixer\Rules\Whitespace\BlankEOFRule;
use TwigCsFixer\Rules\Whitespace\EmptyLinesRule;
use TwigCsFixer\Rules\Whitespace\IndentRule;
use TwigCsFixer\Rules\Whitespace\TrailingSpaceRule;

/**
 * @internal
 */
#[CoversClass(StandardRuleSets::class)]
final class StandardRuleSetsTest extends TestCase
{
    public function testBasicReturnsRuleInterfaceInstances(): void
    {
        $rules = StandardRuleSets::basic();

        $this->assertNotEmpty($rules);
        $this->assertCount(25, $rules);
    }

    public function testBasicContainsExpectedClasses(): void
    {
        $classes = array_map(static fn (RuleInterface|NodeRuleInterface $r): string => $r::class, StandardRuleSets::basic());

        $this->assertSame([
            // TwigCsFixer base formatting rules (bundled for plug-and-play usage)
            DelimiterSpacingRule::class,
            MacroArgumentNameRule::class,
            NamedArgumentNameRule::class,
            NamedArgumentSeparatorRule::class,
            NamedArgumentSpacingRule::class,
            OperatorNameSpacingRule::class,
            OperatorSpacingRule::class,
            PunctuationSpacingRule::class,
            VariableNameRule::class,
            BlankEOFRule::class,
            BlockNameSpacingRule::class,
            EmptyLinesRule::class,
            CompactHashRule::class,
            HashQuoteRule::class,
            IncludeFunctionRule::class,
            IndentRule::class,
            SingleQuoteRule::class,
            TrailingCommaMultiLineRule::class,
            TrailingCommaSingleLineRule::class,
            TrailingSpaceRule::class,
            // A11y rules
            ImgAltRule::class,
            BannedTagsRule::class,
            ButtonContentRule::class,
            InputLabelRule::class,
            LangAttributeRule::class,
        ], $classes);
    }

    public function testRecommendedReturnsRuleInterfaceInstances(): void
    {
        $rules = StandardRuleSets::recommended();

        $this->assertNotEmpty($rules);
        $this->assertCount(35, $rules);
    }

    public function testRecommendedIsSupersetOfBasic(): void
    {
        $basicClasses = array_map(static fn (RuleInterface|NodeRuleInterface $r): string => $r::class, StandardRuleSets::basic());
        $recommendedClasses = array_map(static fn (RuleInterface|NodeRuleInterface $r): string => $r::class, StandardRuleSets::recommended());

        foreach ($basicClasses as $class) {
            $this->assertContains($class, $recommendedClasses, \sprintf('Recommended should include basic rule "%s".', $class));
        }
    }

    public function testStandardReturnsRuleInterfaceInstances(): void
    {
        $rules = StandardRuleSets::standard();

        $this->assertNotEmpty($rules);
        $this->assertCount(46, $rules);
    }

    public function testStandardIsSupersetOfRecommended(): void
    {
        $recommendedClasses = array_map(static fn (RuleInterface|NodeRuleInterface $r): string => $r::class, StandardRuleSets::recommended());
        $standardClasses = array_map(static fn (RuleInterface|NodeRuleInterface $r): string => $r::class, StandardRuleSets::standard());

        foreach ($recommendedClasses as $class) {
            $this->assertContains($class, $standardClasses, \sprintf('Standard should include recommended rule "%s".', $class));
        }
    }

    public function testStrictReturnsRuleInterfaceInstances(): void
    {
        $rules = StandardRuleSets::strict();

        $this->assertNotEmpty($rules);
        $this->assertGreaterThan(26, \count($rules));
    }

    public function testStrictIsSupersetOfStandard(): void
    {
        $standardClasses = array_map(static fn (RuleInterface|NodeRuleInterface $r): string => $r::class, StandardRuleSets::standard());
        $strictClasses = array_map(static fn (RuleInterface|NodeRuleInterface $r): string => $r::class, StandardRuleSets::strict());

        foreach ($standardClasses as $class) {
            $this->assertContains($class, $strictClasses, \sprintf('Strict should include standard rule "%s".', $class));
        }
    }

    public function testEachRuleSetContainsUniqueClasses(): void
    {
        foreach ([
            'basic' => StandardRuleSets::basic(),
            'recommended' => StandardRuleSets::recommended(),
            'standard' => StandardRuleSets::standard(),
            'strict' => StandardRuleSets::strict(),
        ] as $level => $rules) {
            $classes = array_map(static fn (RuleInterface|NodeRuleInterface $r): string => $r::class, $rules);
            $this->assertSame(array_unique($classes), $classes, \sprintf('Rule set "%s" should not contain duplicate rule classes.', $level));
        }
    }

    public function testInstantiateProducesNewInstancesEachCall(): void
    {
        $rulesA = StandardRuleSets::basic();
        $rulesB = StandardRuleSets::basic();

        $this->assertCount(\count($rulesA), $rulesB);

        foreach ($rulesA as $i => $ruleA) {
            $this->assertNotSame($ruleA, $rulesB[$i], 'Each call to basic() should return fresh instances.');
        }
    }
}
