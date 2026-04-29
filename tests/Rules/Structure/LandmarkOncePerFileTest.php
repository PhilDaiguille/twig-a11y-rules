<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use TwigA11y\Rules\Structure\LandmarkRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @covers \TwigA11y\Rules\Structure\LandmarkRule
 */
final class LandmarkOncePerFileTest extends AbstractRuleTestCase
{
    public function testDuplicateLandmarksAreReportedOncePerFileWhenReused(): void
    {
        $rule = new LandmarkRule();

        // First file has duplicate landmarks
        $this->checkRule($rule, ['Landmark.Landmark.MissingMain:1:1' => 'Page should include a main landmark'], __DIR__.'/Fixtures/invalid/no_main.html.twig');

        // When reusing same instance on a partial fragment it should not re-report
        $this->checkRule($rule, [], __DIR__.'/../Aria/Fixtures/valid/landmark_with_label.html.twig');
    }
}
