<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Template\TemplateKind;
use TwigCsFixer\Token\Tokens;

/**
 * Checks that the lang attribute on <html> contains a valid BCP 47 language tag.
 *
 * WCAG 3.1.1 — Language of Page.
 * axe-core: html-lang-valid
 */
final class LangAttributeValueRule extends AbstractA11yRule
{
    /**
     * Minimal list of valid primary language subtags (ISO 639-1 + a few common 639-2/3).
     * Not exhaustive — covers the overwhelming majority of real-world usage.
     *
     * @var string[]
     */
    private const VALID_PRIMARY_SUBTAGS = [
        'aa', 'ab', 'ae', 'af', 'ak', 'am', 'an', 'ar', 'as', 'av', 'ay', 'az',
        'ba', 'be', 'bg', 'bh', 'bi', 'bm', 'bn', 'bo', 'br', 'bs',
        'ca', 'ce', 'ch', 'co', 'cr', 'cs', 'cu', 'cv', 'cy',
        'da', 'de', 'dv', 'dz',
        'ee', 'el', 'en', 'eo', 'es', 'et', 'eu',
        'fa', 'ff', 'fi', 'fj', 'fo', 'fr', 'fy',
        'ga', 'gd', 'gl', 'gn', 'gu', 'gv',
        'ha', 'he', 'hi', 'ho', 'hr', 'ht', 'hu', 'hy',
        'hz',
        'ia', 'id', 'ie', 'ig', 'ii', 'ik', 'io', 'is', 'it', 'iu',
        'ja', 'jv',
        'ka', 'kg', 'ki', 'kj', 'kk', 'kl', 'km', 'kn', 'ko', 'kr', 'ks', 'ku', 'kv', 'kw', 'ky',
        'la', 'lb', 'lg', 'li', 'ln', 'lo', 'lt', 'lu', 'lv',
        'mg', 'mh', 'mi', 'mk', 'ml', 'mn', 'mr', 'ms', 'mt', 'my',
        'na', 'nb', 'nd', 'ne', 'ng', 'nl', 'nn', 'no', 'nr', 'nv', 'ny',
        'oc', 'oj', 'om', 'or', 'os',
        'pa', 'pi', 'pl', 'ps', 'pt',
        'qu',
        'rm', 'rn', 'ro', 'ru', 'rw',
        'sa', 'sc', 'sd', 'se', 'sg', 'si', 'sk', 'sl', 'sm', 'sn', 'so', 'sq', 'sr', 'ss', 'st', 'su', 'sv', 'sw',
        'ta', 'te', 'tg', 'th', 'ti', 'tk', 'tl', 'tn', 'to', 'tr', 'ts', 'tt', 'tw', 'ty',
        'ug', 'uk', 'ur', 'uz',
        'va', 'vi', 'vo',
        'wa', 'wo',
        'xh',
        'yi', 'yo',
        'za', 'zh', 'zu',
        // Special IANA-registered tags
        'i', 'x',
    ];

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $content = $this->getFullContent($tokens);

        // No lang attribute at all — that is LangAttributeRule's concern, not ours
        if (!preg_match('/\blang\s*=\s*(?:"|\')([^"\']+)(?:"|\')/', $content, $m)) {
            return;
        }

        $lang = strtolower(trim($m[1]));

        // Empty value — also LangAttributeRule's concern
        if ('' === $lang) {
            return;
        }

        // Twig dynamic expression — skip (cannot evaluate statically)
        if ($this->containsTwigExpressions($lang)) {
            return;
        }

        // BCP 47 minimal validation: extract the primary language subtag
        // Valid forms: "en", "en-US", "zh-Hant-TW", "fr-CA", etc.
        $parts = explode('-', $lang);
        $primary = strtolower($parts[0]);

        if (!in_array($primary, self::VALID_PRIMARY_SUBTAGS, true)) {
            $token = $tokens->get(0);
            $emit(
                sprintf('The lang attribute value "%s" is not a valid BCP 47 language tag (invalid primary subtag "%s").', $m[1], $parts[0]),
                $token,
                'LangAttributeValue.InvalidLang'
            );
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }

    /**
     * @return TemplateKind[]
     */
    protected function supportedKinds(): array
    {
        return [TemplateKind::FullPage];
    }
}
