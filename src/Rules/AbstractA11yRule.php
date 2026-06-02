<?php

declare(strict_types=1);

namespace TwigA11y\Rules;

use TwigA11y\Template\TemplateClassifier;
use TwigA11y\Template\TemplateKind;
use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

abstract class AbstractA11yRule extends AbstractRule
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

    /**
     * Implement this method to perform the actual accessibility check.
     *
     * Called for each token (or once per file when evaluateOncePerFile()
     * returns true). Use the $emit callable to report violations — it
     * handles deduplication and warning/error routing automatically.
     */
    abstract public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void;

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

    /**
     * Called once per file just before the first evaluate() call. Rules that
     * maintain per-file state (e.g. counters, deduplication hashes) should
     * override this instead of checking "if (0 === $tokenIndex)" in evaluate().
     */
    protected function evaluateStart(Tokens $tokens): void {}

    /**
     * Build a synthetic Token pointing at a specific line. Useful when a rule
     * has already computed the correct line number from a full-content regex
     * offset and wants to report the error at that precise line.
     */
    protected function fakeTokenForLine(Tokens $tokens, int $line, string $value): Token
    {
        $token = $tokens->get(0);

        return new Token(
            $token->getType(),
            $line,
            1,
            $token->getFilename(),
            $value
        );
    }

    /**
     * @deprecated This guard is a no-op when evaluateOncePerFile() returns true
     *             because AbstractA11yRule::process() already skips subsequent
     *             tokens before calling evaluate(). Remove the call from
     *             evaluate() in concrete rules — no behaviour change results.
     *             This method will be removed in a future major version.
     */
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

            $this->evaluateStart($tokens);
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

        $reporter = $this->emitAsWarning
            ? function (string $message, Token $token, ?string $id): void {
                $this->addWarning($message, $token, $id);
            }
        : function (string $message, Token $token, ?string $id): void {
            $this->addError($message, $token, $id);
        };

        return function (string $message, Token $token, ?string $id = null) use ($ruleFileKey, $reporter): void {
            $key = $message.'|'.($id ?? '');
            if (isset($this->emitted[$ruleFileKey][$key])) {
                return;
            }

            $this->emitted[$ruleFileKey][$key] = true;

            $reporter($message, $token, $id);
        };
    }
}
