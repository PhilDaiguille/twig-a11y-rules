<?php

declare(strict_types=1);

namespace TwigA11y\Standard;

use TwigA11y\Rules\Aria\AriaHiddenFocusRule;
use TwigA11y\Rules\Aria\AriaLabelRule;
use TwigA11y\Rules\Aria\AriaRequiredAttrRule;
use TwigA11y\Rules\Aria\AriaRoleRule;
use TwigA11y\Rules\Aria\TabIndexRule;
use TwigA11y\Rules\Forms\FormLabelRule;
use TwigA11y\Rules\Forms\InputLabelRule;
use TwigA11y\Rules\Forms\InputTypeRule;
use TwigA11y\Rules\Forms\SelectLabelRule;
use TwigA11y\Rules\Forms\TextareaLabelRule;
use TwigA11y\Rules\Media\AutoplayRule;
use TwigA11y\Rules\Media\ImgAltRule;
use TwigA11y\Rules\Media\ObjectAltRule;
use TwigA11y\Rules\Media\VideoTrackRule;
use TwigA11y\Rules\Structure\AnchorContentRule;
use TwigA11y\Rules\Structure\BannedTagsRule;
use TwigA11y\Rules\Structure\ButtonContentRule;
use TwigA11y\Rules\Structure\DuplicateIdRule;
use TwigA11y\Rules\Structure\HeadingEmptyRule;
use TwigA11y\Rules\Structure\HeadingOrderRule;
use TwigA11y\Rules\Structure\IframeTitleRule;
use TwigA11y\Rules\Structure\LandmarkRule;
use TwigA11y\Rules\Structure\LangAttributeRule;
use TwigA11y\Rules\Structure\MetaViewportRule;
use TwigA11y\Rules\Structure\SkipLinkRule;
use TwigA11y\Rules\Structure\TableHeaderRule;
use TwigA11y\Rules\Ui\ColorContrastRule;
use TwigCsFixer\Rules\RuleInterface;

final class StandardRuleSets
{
    /**
     * @return list<RuleInterface>
     */
    public static function basic(): array
    {
        return self::instantiate([
            ImgAltRule::class,
            BannedTagsRule::class,
            ButtonContentRule::class,
            InputLabelRule::class,
            LangAttributeRule::class,
        ]);
    }

    /**
     * @return list<RuleInterface>
     */
    public static function recommended(): array
    {
        return self::instantiate([
            ...self::classes(self::basic()),
            ObjectAltRule::class,
            VideoTrackRule::class,
            HeadingOrderRule::class,
            IframeTitleRule::class,
            DuplicateIdRule::class,
            LandmarkRule::class,
            FormLabelRule::class,
            SelectLabelRule::class,
            TextareaLabelRule::class,
        ]);
    }

    /**
     * @return list<RuleInterface>
     */
    public static function standard(): array
    {
        return self::instantiate([
            ...self::classes(self::recommended()),
            AutoplayRule::class,
            AnchorContentRule::class,
            HeadingEmptyRule::class,
            MetaViewportRule::class,
            SkipLinkRule::class,
            TableHeaderRule::class,
            TabIndexRule::class,
            InputTypeRule::class,
        ]);
    }

    /**
     * @return list<RuleInterface>
     */
    public static function strict(): array
    {
        return self::instantiate([
            ...self::classes(self::standard()),
            AriaRoleRule::class,
            AriaLabelRule::class,
            AriaHiddenFocusRule::class,
            AriaRequiredAttrRule::class,
            ColorContrastRule::class,
        ]);
    }

    /**
     * @param list<class-string<RuleInterface>> $classes
     *
     * @return list<RuleInterface>
     */
    private static function instantiate(array $classes): array
    {
        return array_map(
            static fn (string $class): RuleInterface => new $class(),
            $classes
        );
    }

    /**
     * Accept either a list of RuleInterface instances or a list of class-string
     * and return the corresponding list of class-string entries.
     *
     * @param list<RuleInterface|string> $rules
     *
     * @return list<class-string<RuleInterface>>
     */
    private static function classes(array $rules): array
    {
        $out = [];

        foreach ($rules as $rule) {
            if (is_string($rule)) {
                if (!is_a($rule, RuleInterface::class, true)) {
                    throw new \InvalidArgumentException($rule.' does not implement RuleInterface');
                }

                $out[] = $rule;

                continue;
            }

            $out[] = $rule::class;
        }

        return $out;
    }
}
