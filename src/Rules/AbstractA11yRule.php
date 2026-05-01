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

    private const KIND_CACHE_MAX = 500;

    /**
     * Maximum number of rule-file keys in the $emitted map. When exceeded the
     * map is reset to prevent unbounded memory growth during long-running
     * linting sessions (watch-mode, large CI pipelines, etc.).
     */
    private const EMITTED_MAX = 2000;

    /** Cached decision for the currently-processed file when rules are reused */
    private ?bool $skipThisFile = null;

    /**
     * Per-instance cache of already-emitted messages keyed by file hash.
     *
     * Keyed by rule-file => array<string, bool>.
     *
     * @var array<string, array<string, bool>>
     */
    private array $emitted = [];

    /**
     * Shared cache of TemplateKind decisions keyed by content hash to avoid
     * repeatedly classifying the same file across multiple rule instances.
     *
     * Bounded to 500 entries to prevent unbounded memory growth when linting
     * very large projects in a single PHP process (e.g. via a long-running CI
     * worker or watch mode). When the limit is reached the cache is reset so
     * the next classification starts fresh.
     *
     * @var array<string, TemplateKind>
     */
    private static array $kindCache = [];

    /** Per-instance cache of the full template content for the current file. */
    private ?string $cachedContent = null;

    /**
     * @param bool $emitAsWarning Pass true for rules that should report
     *                            accessibility hints as warnings rather than
     *                            hard errors (e.g. AnchorContentRule).
     */
    public function __construct(private bool $emitAsWarning = false) {}

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
            // Reset the per-file cache so the new file's content is used.
            $this->cachedContent = null;
            $content = $this->getFullContent($tokens);
            $hash = md5($content);

            if (!isset(self::$kindCache[$hash])) {
                if (count(self::$kindCache) >= self::KIND_CACHE_MAX) {
                    self::$kindCache = [];
                }

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

        $this->evaluate($tokens, $tokenIndex, $this->createEmitter($tokens));
    }

    protected function getFullContent(Tokens $tokens): string
    {
        if (null !== $this->cachedContent) {
            return $this->cachedContent;
        }

        $content = '';
        foreach ($tokens->toArray() as $token) {
            $content .= $token->getValue();
        }

        $this->cachedContent = $content;

        return $this->cachedContent;
    }

    private function createEmitter(Tokens $tokens): callable
    {
        // Use the file content hash to deduplicate identical emissions from
        // the same rule for the same file. This prevents noisy repeated
        // messages when rules are evaluated multiple times for the same
        // template content.
        $hash = md5($this->getFullContent($tokens));

        $ruleFileKey = static::class.'::'.$hash;
        if (!isset($this->emitted[$ruleFileKey])) {
            if (count($this->emitted) >= self::EMITTED_MAX) {
                $this->emitted = [];
            }

            $this->emitted[$ruleFileKey] = [];
        }

        if ($this->emitAsWarning) {
            return function (string $message, Token $token, ?string $id = null) use ($ruleFileKey): void {
                $key = $message.'|'.($id ?? '');
                if (isset($this->emitted[$ruleFileKey][$key])) {
                    return;
                }

                $this->emitted[$ruleFileKey][$key] = true;

                if (null === $id) {
                    $this->addWarning($message, $token);

                    return;
                }

                $this->addWarning($message, $token, $id);
            };
        }

        return function (string $message, Token $token, ?string $id = null) use ($ruleFileKey): void {
            $key = $message.'|'.($id ?? '');
            if (isset($this->emitted[$ruleFileKey][$key])) {
                return;
            }

            $this->emitted[$ruleFileKey][$key] = true;

            $this->addError($message, $token, $id);
        };
    }
}
