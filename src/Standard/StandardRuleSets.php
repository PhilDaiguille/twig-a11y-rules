<?php

declare(strict_types=1);

namespace TwigA11y\Standard;

use TwigA11y\Rules\Anchor\AnchorAccessibleNameRule;
use TwigA11y\Rules\Anchor\LinkHrefValidityRule;
use TwigA11y\Rules\Aria\AriaAllowedAttrRule;
use TwigA11y\Rules\Aria\AriaControlsIdExistsRule;
use TwigA11y\Rules\Aria\AriaDeprecatedRoleRule;
use TwigA11y\Rules\Aria\AriaErrorMessageIdExistsRule;
use TwigA11y\Rules\Aria\AriaHiddenBodyRule;
use TwigA11y\Rules\Aria\AriaHiddenFocusRule;
use TwigA11y\Rules\Aria\AriaLabelRule;
use TwigA11y\Rules\Aria\AriaReferencedIdExistsRule;
use TwigA11y\Rules\Aria\AriaRequiredAttrRule;
use TwigA11y\Rules\Aria\AriaRequiredChildrenRule;
use TwigA11y\Rules\Aria\AriaRequiredParentRule;
use TwigA11y\Rules\Aria\AriaRoleRule;
use TwigA11y\Rules\Aria\AriaValidAttrRule;
use TwigA11y\Rules\Aria\AriaValidAttrValueRule;
use TwigA11y\Rules\Aria\RoleButtonTabindexRule;
use TwigA11y\Rules\Aria\TabIndexRule;
use TwigA11y\Rules\Forms\AriaInputFieldNameRule;
use TwigA11y\Rules\Forms\AutocompleteValidRule;
use TwigA11y\Rules\Forms\ButtonTypeRule;
use TwigA11y\Rules\Forms\CheckboxGroupStructureRule;
use TwigA11y\Rules\Forms\FormLabelRule;
use TwigA11y\Rules\Forms\InputButtonNameRule;
use TwigA11y\Rules\Forms\InputLabelRule;
use TwigA11y\Rules\Forms\InputTypeRule;
use TwigA11y\Rules\Forms\InvalidFieldErrorMessageRule;
use TwigA11y\Rules\Forms\LabelForTargetExistsRule;
use TwigA11y\Rules\Forms\OptGroupLabelRule;
use TwigA11y\Rules\Forms\PlaceholderOnlyLabelRule;
use TwigA11y\Rules\Forms\RadioGroupAccessibleNameRule;
use TwigA11y\Rules\Forms\RadioGroupStructureRule;
use TwigA11y\Rules\Forms\SelectLabelRule;
use TwigA11y\Rules\Forms\TextareaLabelRule;
use TwigA11y\Rules\Media\AudioControlsRule;
use TwigA11y\Rules\Media\AutoplayRule;
use TwigA11y\Rules\Media\ImgAltRule;
use TwigA11y\Rules\Media\InputImageAltRule;
use TwigA11y\Rules\Media\NoAutoplayAudioRule;
use TwigA11y\Rules\Media\ObjectAltRule;
use TwigA11y\Rules\Media\SvgAccessibilityRule;
use TwigA11y\Rules\Media\VideoDescriptionTrackRule;
use TwigA11y\Rules\Media\VideoTrackRule;
use TwigA11y\Rules\Structure\AbbrTitleRule;
use TwigA11y\Rules\Structure\AnchorContentRule;
use TwigA11y\Rules\Structure\AreaAltRule;
use TwigA11y\Rules\Structure\BannedTagsRule;
use TwigA11y\Rules\Structure\ButtonContentRule;
use TwigA11y\Rules\Structure\DetailsSummaryRule;
use TwigA11y\Rules\Structure\DialogAccessibleNameRule;
use TwigA11y\Rules\Structure\DocTypeRule;
use TwigA11y\Rules\Structure\DocumentTitleRule;
use TwigA11y\Rules\Structure\DuplicateAccessKeyRule;
use TwigA11y\Rules\Structure\DuplicateIdRule;
use TwigA11y\Rules\Structure\EmptyTableHeaderRule;
use TwigA11y\Rules\Structure\FieldsetLegendRule;
use TwigA11y\Rules\Structure\FrameTitleRule;
use TwigA11y\Rules\Structure\GenericLinkTextRule;
use TwigA11y\Rules\Structure\HeadingEmptyRule;
use TwigA11y\Rules\Structure\HeadingOrderRule;
use TwigA11y\Rules\Structure\IframeFocusableContentRule;
use TwigA11y\Rules\Structure\IframeTitleRule;
use TwigA11y\Rules\Structure\LandmarkRule;
use TwigA11y\Rules\Structure\LandmarkUniqueRule;
use TwigA11y\Rules\Structure\LangAttributeRule;
use TwigA11y\Rules\Structure\LangAttributeValueRule;
use TwigA11y\Rules\Structure\ListStructureRule;
use TwigA11y\Rules\Structure\MetaCharsetRule;
use TwigA11y\Rules\Structure\MetaRefreshRule;
use TwigA11y\Rules\Structure\MetaViewportRule;
use TwigA11y\Rules\Structure\NestedInteractiveRule;
use TwigA11y\Rules\Structure\PageHeadingOneRule;
use TwigA11y\Rules\Structure\PAsHeadingRule;
use TwigA11y\Rules\Structure\SkipLinkRule;
use TwigA11y\Rules\Structure\SummaryAttributeObsoleteRule;
use TwigA11y\Rules\Structure\TableCaptionMissingRule;
use TwigA11y\Rules\Structure\TableDuplicateNameRule;
use TwigA11y\Rules\Structure\TableFakeCaptionRule;
use TwigA11y\Rules\Structure\TableHeaderRule;
use TwigA11y\Rules\Structure\TableLayoutRoleRule;
use TwigA11y\Rules\Structure\TdHeadersAttrRule;
use TwigA11y\Rules\Ui\ClickableNonInteractiveRule;
use TwigA11y\Rules\Ui\ColorContrastRule;
use TwigA11y\Rules\Ui\MouseEventKeyboardEquivalentRule;
use TwigA11y\Rules\Ui\OutlineNoneWithoutFocusVisibleRule;
use TwigA11y\Rules\Ui\ScrollableRegionFocusableRule;
use TwigA11y\Rules\Ui\TargetSizeRule;
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
            TableFakeCaptionRule::class,
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
            InputButtonNameRule::class,
            EmptyTableHeaderRule::class,
            GenericLinkTextRule::class,
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
            AriaValidAttrRule::class,
            AriaValidAttrValueRule::class,
            AriaDeprecatedRoleRule::class,
            AriaRequiredChildrenRule::class,
            AriaRequiredParentRule::class,
            AriaReferencedIdExistsRule::class,
            AriaControlsIdExistsRule::class,
            AriaErrorMessageIdExistsRule::class,
            AriaAllowedAttrRule::class,
            AriaHiddenBodyRule::class,
            RoleButtonTabindexRule::class,
            AutocompleteValidRule::class,
            ButtonTypeRule::class,
            InvalidFieldErrorMessageRule::class,
            AriaInputFieldNameRule::class,
            AnchorAccessibleNameRule::class,
            LinkHrefValidityRule::class,
            AreaAltRule::class,
            FieldsetLegendRule::class,
            IframeFocusableContentRule::class,
            LandmarkUniqueRule::class,
            ListStructureRule::class,
            PageHeadingOneRule::class,
            TableCaptionMissingRule::class,
            TableDuplicateNameRule::class,
            TdHeadersAttrRule::class,
            ScrollableRegionFocusableRule::class,
            OutlineNoneWithoutFocusVisibleRule::class,
            TargetSizeRule::class,
            NoAutoplayAudioRule::class,
            SvgAccessibilityRule::class,
            DetailsSummaryRule::class,
            DialogAccessibleNameRule::class,
            LabelForTargetExistsRule::class,
            PlaceholderOnlyLabelRule::class,
            RadioGroupAccessibleNameRule::class,
            RadioGroupStructureRule::class,
            ColorContrastRule::class,
            FrameTitleRule::class,
            InputImageAltRule::class,
            MetaRefreshRule::class,
            DocumentTitleRule::class,
            LangAttributeValueRule::class,
            NestedInteractiveRule::class,
            DuplicateAccessKeyRule::class,
            PAsHeadingRule::class,
            AbbrTitleRule::class,
            DocTypeRule::class,
            MetaCharsetRule::class,
            TableLayoutRoleRule::class,
            SummaryAttributeObsoleteRule::class,
            OptGroupLabelRule::class,
            CheckboxGroupStructureRule::class,
            AudioControlsRule::class,
            VideoDescriptionTrackRule::class,
            ClickableNonInteractiveRule::class,
            MouseEventKeyboardEquivalentRule::class,
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
