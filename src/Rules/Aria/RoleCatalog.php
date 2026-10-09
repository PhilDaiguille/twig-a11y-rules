<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

final class RoleCatalog
{
    /**
     * Complete list of valid WAI-ARIA 1.2, DPUB-ARIA 1.1 and Graphics ARIA role values.
     * Used by AriaRoleRule for role validity checks.
     *
     * @return string[]
     */
    public static function getAllowedRoles(): array
    {
        return [
            'alert',
            'alertdialog',
            'application',
            'article',
            'banner',
            'button',
            'checkbox',
            'columnheader',
            'combobox',
            'complementary',
            'contentinfo',
            'dialog',
            'directory',
            'document',
            'feed',
            'figure',
            'form',
            'generic',
            'grid',
            'gridcell',
            'group',
            'heading',
            'img',
            'insertion',
            'link',
            'list',
            'listbox',
            'listitem',
            'log',
            'main',
            'mark',
            'math',
            'menu',
            'menubar',
            'menuitem',
            'menuitemcheckbox',
            'menuitemradio',
            'meter',
            'navigation',
            'none',
            'note',
            'option',
            'presentation',
            'progressbar',
            'radio',
            'radiogroup',
            'region',
            'row',
            'rowgroup',
            'rowheader',
            'scrollbar',
            'search',
            'searchbox',
            'separator',
            'slider',
            'spinbutton',
            'status',
            'strong',
            'subscript',
            'superscript',
            'switch',
            'tab',
            'table',
            'tablist',
            'tabpanel',
            'term',
            'textbox',
            'time',
            'timer',
            'toolbar',
            'tooltip',
            'tree',
            'treegrid',
            'treeitem',
            // DPUB-ARIA 1.1 (https://www.w3.org/TR/dpub-aria-1.1/)
            'doc-abstract',
            'doc-acknowledgments',
            'doc-afterword',
            'doc-appendix',
            'doc-backlink',
            'doc-biblioentry',
            'doc-bibliography',
            'doc-biblioref',
            'doc-chapter',
            'doc-colophon',
            'doc-conclusion',
            'doc-cover',
            'doc-credit',
            'doc-credits',
            'doc-dedication',
            'doc-endnote',
            'doc-endnotes',
            'doc-epigraph',
            'doc-epilogue',
            'doc-errata',
            'doc-example',
            'doc-footnote',
            'doc-foreword',
            'doc-glossary',
            'doc-glossref',
            'doc-index',
            'doc-introduction',
            'doc-noteref',
            'doc-notice',
            'doc-pagebreak',
            'doc-pagefooter',
            'doc-pageheader',
            'doc-pagelist',
            'doc-part',
            'doc-preface',
            'doc-prologue',
            'doc-pullquote',
            'doc-qna',
            'doc-subtitle',
            'doc-tip',
            'doc-toc',
            // Graphics ARIA 1.0 (https://www.w3.org/TR/graphics-aria-1.0/)
            'graphics-document',
            'graphics-object',
            'graphics-symbol',
        ];
    }

    /**
     * Minimal role requirements mapping used by static checks.
     * Map role => required attributes (simple case) and required children (names).
     *
     * @return array<string, array{required_attrs: string[], required_children: string[]}>
     */
    public static function getCatalog(): array
    {
        return [
            // Structural / composite widgets
            'table' => [
                'required_attrs' => [],
                'required_children' => ['row', 'rowgroup', 'rowheader'],
            ],
            'tablist' => [
                'required_attrs' => [],
                'required_children' => ['tab'],
            ],
            'list' => [
                'required_attrs' => [],
                'required_children' => ['listitem'],
            ],
            'radiogroup' => [
                'required_attrs' => [],
                'required_children' => ['radio'],
            ],

            // Hierarchical tree widget
            'tree' => [
                'required_attrs' => [],
                'required_children' => ['treeitem'],
            ],

            // Grid (data grid / spreadsheet pattern)
            'grid' => [
                'required_attrs' => [],
                'required_children' => ['row', 'rowgroup'],
            ],

            // Listbox (selection widget)
            'listbox' => [
                'required_attrs' => [],
                'required_children' => ['option'],
            ],

            // Row — allowed children depend on context (grid vs table)
            'row' => [
                'required_attrs' => [],
                'required_children' => ['gridcell', 'columnheader', 'rowheader'],
            ],

            // Common ARIA roles with basic expectations
            'menu' => [
                'required_attrs' => [],
                'required_children' => ['menuitem', 'menuitemcheckbox', 'menuitemradio'],
            ],
            'menubar' => [
                'required_attrs' => [],
                'required_children' => ['menuitem', 'menuitemcheckbox', 'menuitemradio'],
            ],
            'tab' => [
                'required_attrs' => ['aria-selected'],
                'required_children' => [],
            ],
            'button' => [
                'required_attrs' => [],
                'required_children' => [],
            ],
            'checkbox' => [
                'required_attrs' => ['aria-checked'],
                'required_children' => [],
            ],
            'radio' => [
                'required_attrs' => ['aria-checked'],
                'required_children' => [],
            ],
        ];
    }
}
