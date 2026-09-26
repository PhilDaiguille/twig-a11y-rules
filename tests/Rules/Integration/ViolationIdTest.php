<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Integration;

use PHPUnit\Framework\Attributes\CoversClass;
use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Rules\Forms\InputLabelRule;
use TwigA11y\Rules\Media\ImgAltRule;
use TwigCsFixer\Ruleset\Ruleset;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * Locks the contract with twig-cs-fixer: ids are `<Rule>.<MessageId>`,
 * each violation is reported where it is, and rule options reach the Ruleset.
 *
 * @internal
 */
#[CoversClass(AbstractA11yRule::class)]
final class ViolationIdTest extends AbstractRuleTestCase
{
    public function testDocumentedDisableCommentSilencesTheViolation(): void
    {
        $this->checkRule(new InputLabelRule(), [], __DIR__.'/Fixtures/disable_by_message_id.html.twig');
    }

    public function testIdenticalViolationsAreEachReportedAtTheirOwnLine(): void
    {
        $this->checkRule(new ImgAltRule(), [
            'ImgAlt.MissingAlt:2:5' => 'Missing alt attribute on <img> tag.',
            'ImgAlt.MissingAlt:3:5' => 'Missing alt attribute on <img> tag.',
        ], __DIR__.'/Fixtures/two_images_without_alt.html.twig');
    }

    public function testEmitAsWarningIsPartOfTheRuleConfiguration(): void
    {
        $ruleset = new Ruleset();
        $ruleset->addRule(new InputLabelRule());
        $ruleset->addRule(new InputLabelRule(emitAsWarning: true));

        $this->assertCount(2, $ruleset->getRules());
        $this->assertSame(['emitAsWarning' => true], new InputLabelRule(emitAsWarning: true)->getConfiguration());
    }
}
