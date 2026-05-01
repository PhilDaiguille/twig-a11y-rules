<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use TwigA11y\Rules\Aria\RoleCatalog;

/**
 * @internal
 */
#[CoversClass(RoleCatalog::class)]
final class RoleCatalogTest extends TestCase
{
    public function testGetAllowedRolesReturnsNonEmptyArray(): void
    {
        $roles = RoleCatalog::getAllowedRoles();

        self::assertIsArray($roles);
        self::assertNotEmpty($roles);
    }

    public function testGetAllowedRolesContainsKnownWaiAriaRoles(): void
    {
        $roles = RoleCatalog::getAllowedRoles();

        $expected = [
            'alert', 'alertdialog', 'application', 'article', 'banner',
            'button', 'checkbox', 'combobox', 'dialog', 'document',
            'feed', 'figure', 'form', 'grid', 'group', 'heading', 'img',
            'link', 'list', 'listbox', 'listitem', 'log', 'main', 'menu',
            'menubar', 'menuitem', 'navigation', 'none', 'option',
            'presentation', 'progressbar', 'radio', 'radiogroup', 'region',
            'row', 'rowgroup', 'scrollbar', 'search', 'searchbox',
            'separator', 'slider', 'spinbutton', 'status', 'switch',
            'tab', 'table', 'tablist', 'tabpanel', 'textbox', 'timer',
            'toolbar', 'tooltip', 'tree', 'treegrid', 'treeitem',
        ];

        foreach ($expected as $role) {
            self::assertContains($role, $roles, \sprintf('Expected role "%s" to be in the allowed roles list.', $role));
        }
    }

    public function testGetAllowedRolesContainsOnlyStrings(): void
    {
        foreach (RoleCatalog::getAllowedRoles() as $role) {
            self::assertIsString($role);
            self::assertNotEmpty($role);
        }
    }

    public function testGetCatalogReturnsNonEmptyArray(): void
    {
        $catalog = RoleCatalog::getCatalog();

        self::assertIsArray($catalog);
        self::assertNotEmpty($catalog);
    }

    public function testGetCatalogEntriesHaveExpectedShape(): void
    {
        foreach (RoleCatalog::getCatalog() as $role => $entry) {
            self::assertIsString($role, 'Catalog key must be a string role name.');
            self::assertArrayHasKey('required_attrs', $entry, \sprintf('Role "%s" must have required_attrs.', $role));
            self::assertArrayHasKey('required_children', $entry, \sprintf('Role "%s" must have required_children.', $role));
            self::assertIsArray($entry['required_attrs']);
            self::assertIsArray($entry['required_children']);
        }
    }

    public function testGetCatalogContainsCompositeWidgets(): void
    {
        $catalog = RoleCatalog::getCatalog();

        self::assertArrayHasKey('table', $catalog);
        self::assertContains('row', $catalog['table']['required_children']);

        self::assertArrayHasKey('tablist', $catalog);
        self::assertContains('tab', $catalog['tablist']['required_children']);

        self::assertArrayHasKey('list', $catalog);
        self::assertContains('listitem', $catalog['list']['required_children']);

        self::assertArrayHasKey('radiogroup', $catalog);
        self::assertContains('radio', $catalog['radiogroup']['required_children']);

        self::assertArrayHasKey('tree', $catalog);
        self::assertContains('treeitem', $catalog['tree']['required_children']);

        self::assertArrayHasKey('grid', $catalog);
        self::assertContains('row', $catalog['grid']['required_children']);

        self::assertArrayHasKey('listbox', $catalog);
        self::assertContains('option', $catalog['listbox']['required_children']);

        self::assertArrayHasKey('menu', $catalog);
        self::assertContains('menuitem', $catalog['menu']['required_children']);

        self::assertArrayHasKey('menubar', $catalog);
        self::assertContains('menuitem', $catalog['menubar']['required_children']);
    }

    public function testGetCatalogRolesWithRequiredAttrs(): void
    {
        $catalog = RoleCatalog::getCatalog();

        self::assertArrayHasKey('tab', $catalog);
        self::assertContains('aria-selected', $catalog['tab']['required_attrs']);

        self::assertArrayHasKey('checkbox', $catalog);
        self::assertContains('aria-checked', $catalog['checkbox']['required_attrs']);

        self::assertArrayHasKey('radio', $catalog);
        self::assertContains('aria-checked', $catalog['radio']['required_attrs']);
    }

    public function testGetCatalogRolesWithNoRequiredAttrs(): void
    {
        $catalog = RoleCatalog::getCatalog();

        foreach (['button', 'table', 'list', 'menu', 'menubar', 'row'] as $role) {
            self::assertArrayHasKey($role, $catalog);
            self::assertSame([], $catalog[$role]['required_attrs'], \sprintf('Role "%s" should have no required_attrs.', $role));
        }
    }
}
