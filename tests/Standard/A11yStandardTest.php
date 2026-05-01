<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Standard;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use TwigA11y\Rules\Anchor\AnchorAccessibleNameRule;
use TwigA11y\Rules\Aria\AriaAllowedAttrRule;
use TwigA11y\Rules\Aria\AriaDeprecatedRoleRule;
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
use TwigA11y\Rules\Aria\TabIndexRule;
use TwigA11y\Rules\Forms\AriaInputFieldNameRule;
use TwigA11y\Rules\Forms\AutocompleteValidRule;
use TwigA11y\Rules\Forms\FormLabelRule;
use TwigA11y\Rules\Forms\InputButtonNameRule;
use TwigA11y\Rules\Forms\InputLabelRule;
use TwigA11y\Rules\Forms\InputTypeRule;
use TwigA11y\Rules\Forms\SelectLabelRule;
use TwigA11y\Rules\Forms\TextareaLabelRule;
use TwigA11y\Rules\Media\AutoplayRule;
use TwigA11y\Rules\Media\ImgAltRule;
use TwigA11y\Rules\Media\InputImageAltRule;
use TwigA11y\Rules\Media\NoAutoplayAudioRule;
use TwigA11y\Rules\Media\ObjectAltRule;
use TwigA11y\Rules\Media\RoleImgAltRule;
use TwigA11y\Rules\Media\VideoTrackRule;
use TwigA11y\Rules\Structure\AnchorContentRule;
use TwigA11y\Rules\Structure\AreaAltRule;
use TwigA11y\Rules\Structure\BannedTagsRule;
use TwigA11y\Rules\Structure\ButtonContentRule;
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
use TwigA11y\Rules\Structure\MetaRefreshRule;
use TwigA11y\Rules\Structure\MetaViewportRule;
use TwigA11y\Rules\Structure\NestedInteractiveRule;
use TwigA11y\Rules\Structure\PageHeadingOneRule;
use TwigA11y\Rules\Structure\PAsHeadingRule;
use TwigA11y\Rules\Structure\SkipLinkRule;
use TwigA11y\Rules\Structure\TableDuplicateNameRule;
use TwigA11y\Rules\Structure\TableFakeCaptionRule;
use TwigA11y\Rules\Structure\TableHeaderRule;
use TwigA11y\Rules\Structure\TdHeadersAttrRule;
use TwigA11y\Rules\Ui\ColorContrastRule;
use TwigA11y\Rules\Ui\OutlineNoneWithoutFocusVisibleRule;
use TwigA11y\Rules\Ui\ScrollableRegionFocusableRule;
use TwigA11y\Rules\Ui\TargetSizeRule;
use TwigA11y\Standard\A11yBasicStandard;
use TwigA11y\Standard\A11yRecommendedStandard;
use TwigA11y\Standard\A11yStandard;
use TwigA11y\Standard\A11yStrict;
use TwigCsFixer\Rules\Node\NodeRuleInterface;
use TwigCsFixer\Rules\RuleInterface;
use TwigCsFixer\Standard\StandardInterface;

/**
 * @internal
 */
#[CoversClass(AnchorAccessibleNameRule::class)]
#[CoversClass(AriaAllowedAttrRule::class)]
#[CoversClass(AriaDeprecatedRoleRule::class)]
#[CoversClass(AriaHiddenBodyRule::class)]
#[CoversClass(AriaHiddenFocusRule::class)]
#[CoversClass(AriaLabelRule::class)]
#[CoversClass(AriaReferencedIdExistsRule::class)]
#[CoversClass(AriaRequiredAttrRule::class)]
#[CoversClass(AriaRequiredChildrenRule::class)]
#[CoversClass(AriaRequiredParentRule::class)]
#[CoversClass(AriaRoleRule::class)]
#[CoversClass(AriaValidAttrRule::class)]
#[CoversClass(AriaValidAttrValueRule::class)]
#[CoversClass(TabIndexRule::class)]
#[CoversClass(AriaInputFieldNameRule::class)]
#[CoversClass(AutocompleteValidRule::class)]
#[CoversClass(FormLabelRule::class)]
#[CoversClass(InputButtonNameRule::class)]
#[CoversClass(InputLabelRule::class)]
#[CoversClass(InputTypeRule::class)]
#[CoversClass(SelectLabelRule::class)]
#[CoversClass(TextareaLabelRule::class)]
#[CoversClass(AutoplayRule::class)]
#[CoversClass(ImgAltRule::class)]
#[CoversClass(InputImageAltRule::class)]
#[CoversClass(NoAutoplayAudioRule::class)]
#[CoversClass(ObjectAltRule::class)]
#[CoversClass(RoleImgAltRule::class)]
#[CoversClass(VideoTrackRule::class)]
#[CoversClass(AnchorContentRule::class)]
#[CoversClass(AreaAltRule::class)]
#[CoversClass(BannedTagsRule::class)]
#[CoversClass(ButtonContentRule::class)]
#[CoversClass(DocumentTitleRule::class)]
#[CoversClass(DuplicateAccessKeyRule::class)]
#[CoversClass(DuplicateIdRule::class)]
#[CoversClass(EmptyTableHeaderRule::class)]
#[CoversClass(FieldsetLegendRule::class)]
#[CoversClass(FrameTitleRule::class)]
#[CoversClass(GenericLinkTextRule::class)]
#[CoversClass(HeadingEmptyRule::class)]
#[CoversClass(HeadingOrderRule::class)]
#[CoversClass(IframeFocusableContentRule::class)]
#[CoversClass(IframeTitleRule::class)]
#[CoversClass(LandmarkRule::class)]
#[CoversClass(LandmarkUniqueRule::class)]
#[CoversClass(LangAttributeRule::class)]
#[CoversClass(LangAttributeValueRule::class)]
#[CoversClass(ListStructureRule::class)]
#[CoversClass(MetaRefreshRule::class)]
#[CoversClass(MetaViewportRule::class)]
#[CoversClass(NestedInteractiveRule::class)]
#[CoversClass(PAsHeadingRule::class)]
#[CoversClass(PageHeadingOneRule::class)]
#[CoversClass(SkipLinkRule::class)]
#[CoversClass(TableDuplicateNameRule::class)]
#[CoversClass(TableFakeCaptionRule::class)]
#[CoversClass(TableHeaderRule::class)]
#[CoversClass(TdHeadersAttrRule::class)]
#[CoversClass(ColorContrastRule::class)]
#[CoversClass(OutlineNoneWithoutFocusVisibleRule::class)]
#[CoversClass(ScrollableRegionFocusableRule::class)]
#[CoversClass(TargetSizeRule::class)]
#[CoversClass(A11yBasicStandard::class)]
#[CoversClass(A11yRecommendedStandard::class)]
#[CoversClass(A11yStandard::class)]
#[CoversClass(A11yStrict::class)]
final class A11yStandardTest extends TestCase
{
    public function testBasicStandardProvidesExpectedRules(): void
    {
        $this->assertSame(
            [
                ImgAltRule::class,
                BannedTagsRule::class,
                ButtonContentRule::class,
                InputLabelRule::class,
                LangAttributeRule::class,
            ],
            $this->classes(new A11yBasicStandard())
        );
    }

    public function testRecommendedStandardProvidesExpectedRules(): void
    {
        $this->assertSame(
            [
                ImgAltRule::class,
                BannedTagsRule::class,
                ButtonContentRule::class,
                InputLabelRule::class,
                LangAttributeRule::class,
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
            ],
            $this->classes(new A11yRecommendedStandard())
        );
    }

    public function testA11yStandardProvidesExpectedRules(): void
    {
        $this->assertSame(
            [
                ImgAltRule::class,
                BannedTagsRule::class,
                ButtonContentRule::class,
                InputLabelRule::class,
                LangAttributeRule::class,
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
            ],
            $this->classes(new A11yStandard())
        );
    }

    public function testA11yStrictProvidesAllRules(): void
    {
        $this->assertSame(
            [
                ImgAltRule::class,
                BannedTagsRule::class,
                ButtonContentRule::class,
                InputLabelRule::class,
                LangAttributeRule::class,
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
                AriaAllowedAttrRule::class,
                AriaHiddenBodyRule::class,
                AutocompleteValidRule::class,
                AriaInputFieldNameRule::class,
                AnchorAccessibleNameRule::class,
                AreaAltRule::class,
                FieldsetLegendRule::class,
                IframeFocusableContentRule::class,
                LandmarkUniqueRule::class,
                ListStructureRule::class,
                PageHeadingOneRule::class,
                TableDuplicateNameRule::class,
                TdHeadersAttrRule::class,
                ScrollableRegionFocusableRule::class,
                OutlineNoneWithoutFocusVisibleRule::class,
                TargetSizeRule::class,
                NoAutoplayAudioRule::class,
                RoleImgAltRule::class,
                ColorContrastRule::class,
                FrameTitleRule::class,
                InputImageAltRule::class,
                MetaRefreshRule::class,
                DocumentTitleRule::class,
                LangAttributeValueRule::class,
                NestedInteractiveRule::class,
                DuplicateAccessKeyRule::class,
                PAsHeadingRule::class,
            ],
            $this->classes(new A11yStrict())
        );
    }

    public function testStandardsAreMonotonic(): void
    {
        $basic = $this->classes(new A11yBasicStandard());
        $recommended = $this->classes(new A11yRecommendedStandard());
        $standard = $this->classes(new A11yStandard());
        $strict = $this->classes(new A11yStrict());

        $this->assertSame($basic, array_values(array_intersect($recommended, $basic)));
        $this->assertSame($recommended, array_values(array_intersect($standard, $recommended)));
        $this->assertSame($standard, array_values(array_intersect($strict, $standard)));
    }

    /**
     * @return list<string>
     */
    private function classes(StandardInterface $standard): array
    {
        $rules = $standard->getRules();

        $this->assertNotEmpty($rules);
        $this->assertContainsOnlyInstancesOf(RuleInterface::class, $rules);

        return array_map(
            static fn (NodeRuleInterface|RuleInterface $rule): string => $rule::class,
            $rules
        );
    }
}
