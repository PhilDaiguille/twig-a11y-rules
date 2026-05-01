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

        $this->assertNotEmpty($roles);
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
            $this->assertContains($role, $roles, \sprintf('Expected role "%s" to be in the allowed roles list.', $role));
        }
    }

    public function testGetAllowedRolesContainsOnlyStrings(): void
    {
        foreach (RoleCatalog::getAllowedRoles() as $role) {
            $this->assertNotEmpty($role);
        }
    }

    public function testGetCatalogReturnsNonEmptyArray(): void
    {
        $catalog = RoleCatalog::getCatalog();

        $this->assertNotEmpty($catalog);
    }

    public function testGetCatalogEntriesHaveExpectedShape(): void
    {
        foreach (RoleCatalog::getCatalog() as $role => $entry) {
            $this->assertArrayHasKey('required_attrs', $entry, \sprintf('Role "%s" must have required_attrs.', $role));
            $this->assertArrayHasKey('required_children', $entry, \sprintf('Role "%s" must have required_children.', $role));
        }
    }

    public function testGetCatalogContainsCompositeWidgets(): void
    {
        $catalog = RoleCatalog::getCatalog();

        $this->assertArrayHasKey('table', $catalog);
        $this->assertContains('row', $catalog['table']['required_children']);

        $this->assertArrayHasKey('tablist', $catalog);
        $this->assertContains('tab', $catalog['tablist']['required_children']);

        $this->assertArrayHasKey('list', $catalog);
        $this->assertContains('listitem', $catalog['list']['required_children']);

        $this->assertArrayHasKey('radiogroup', $catalog);
        $this->assertContains('radio', $catalog['radiogroup']['required_children']);

        $this->assertArrayHasKey('tree', $catalog);
        $this->assertContains('treeitem', $catalog['tree']['required_children']);

        $this->assertArrayHasKey('grid', $catalog);
        $this->assertContains('row', $catalog['grid']['required_children']);

        $this->assertArrayHasKey('listbox', $catalog);
        $this->assertContains('option', $catalog['listbox']['required_children']);

        $this->assertArrayHasKey('menu', $catalog);
        $this->assertContains('menuitem', $catalog['menu']['required_children']);

        $this->assertArrayHasKey('menubar', $catalog);
        $this->assertContains('menuitem', $catalog['menubar']['required_children']);
    }

    public function testGetCatalogRolesWithRequiredAttrs(): void
    {
        $catalog = RoleCatalog::getCatalog();

        $this->assertArrayHasKey('tab', $catalog);
        $this->assertContains('aria-selected', $catalog['tab']['required_attrs']);

        $this->assertArrayHasKey('checkbox', $catalog);
        $this->assertContains('aria-checked', $catalog['checkbox']['required_attrs']);

        $this->assertArrayHasKey('radio', $catalog);
        $this->assertContains('aria-checked', $catalog['radio']['required_attrs']);
    }

    public function testGetCatalogRolesWithNoRequiredAttrs(): void
    {
        $catalog = RoleCatalog::getCatalog();

        foreach (['button', 'table', 'list', 'menu', 'menubar', 'row'] as $role) {
            $this->assertArrayHasKey($role, $catalog);
            $this->assertSame([], $catalog[$role]['required_attrs'], \sprintf('Role "%s" should have no required_attrs.', $role));
        }
    }
}
