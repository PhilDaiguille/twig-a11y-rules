<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use TwigA11y\Rules\Anchor\AnchorAccessibleNameRule;
use TwigA11y\Rules\Aria\AriaLabelRule;
use TwigA11y\Rules\Aria\TabIndexRule;
use TwigA11y\Rules\Forms\AbstractFormFieldLabelRule;
use TwigA11y\Rules\Forms\FormLabelRule;
use TwigA11y\Rules\Forms\InputButtonNameRule;
use TwigA11y\Rules\Media\AutoplayRule;
use TwigA11y\Rules\Media\ImgAltRule;
use TwigA11y\Rules\Structure\AnchorContentRule;
use TwigA11y\Rules\Structure\AreaAltRule;

/**
 * Enforces that rule classes which call getFullContent() directly at the top
 * of evaluate() (i.e. without first checking a per-token guard) declare
 * evaluateOncePerFile() = true. Without this declaration the rule's logic
 * runs on every single token, which wastes CPU on large templates.
 *
 * A rule is flagged when:
 *  - it calls $this->getFullContent($tokens) or $this->shouldSkipByTokenIndex
 *    as the first meaningful statement in evaluate(), AND
 *  - it does NOT override evaluateOncePerFile().
 *
 * This is a heuristic source-level check; false positives are possible for
 * rules that deliberately run per-token for good reasons.
 *
 * @internal
 */
#[CoversNothing]
final class EvaluateOncePerFileConsistencyTest extends TestCase
{
    /**
     * Rules that intentionally run on every token and therefore are exempted
     * from this check. These rules check a per-token condition first before
     * calling getFullContent(), so running once-per-file would break them.
     *
     * @var class-string[]
     */
    private const EXEMPTED = [
        ImgAltRule::class,
        AutoplayRule::class,
        AnchorContentRule::class,
        AreaAltRule::class,
        FormLabelRule::class,
        InputButtonNameRule::class,
        TabIndexRule::class,
        AriaLabelRule::class,
        AnchorAccessibleNameRule::class,
        AbstractFormFieldLabelRule::class,
    ];

    public function testRulesThatCallGetFullContentImmediatelyDeclareEvaluateOncePerFile(): void
    {
        $rulesDir = dirname(__DIR__, 2).'/src/Rules';
        $violations = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rulesDir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            $this->assertInstanceOf(\SplFileInfo::class, $file);
            if ('php' !== $file->getExtension()) {
                continue;
            }

            $content = file_get_contents($file->getPathname());
            assert(is_string($content));
            // Only care about files that have both getFullContent AND evaluate()
            // without an early per-token guard that is separate from
            // shouldSkipByTokenIndex.
            if (!str_contains($content, 'getFullContent')) {
                continue;
            }

            if (!str_contains($content, 'function evaluate(')) {
                continue;
            }

            // Extract the FQCN from the file.
            if (!preg_match('/^namespace\s+([\w\\\]+)/m', $content, $nsMatch)) {
                continue;
            }

            if (!preg_match('/^(?:final\s+)?(?:abstract\s+)?class\s+(\w+)/m', $content, $classMatch)) {
                continue;
            }

            $fqcn = $nsMatch[1].'\\'.$classMatch[1];

            if (in_array($fqcn, self::EXEMPTED, true)) {
                continue;
            }

            // Flag: getFullContent called but evaluateOncePerFile not declared.
            if (!str_contains($content, 'evaluateOncePerFile')) {
                $violations[] = $fqcn;
            }
        }

        $this->assertSame(
            [],
            $violations,
            "The following rule classes call getFullContent() but do not declare evaluateOncePerFile().\n"
            ."Add `protected function evaluateOncePerFile(): bool { return true; }` or add the class to the exemption list in this test:\n"
            .implode("\n", $violations)
        );
    }
}
