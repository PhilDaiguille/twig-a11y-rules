<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Template;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TwigA11y\Template\TemplateClassifier;
use TwigA11y\Template\TemplateKind;

/**
 * @covers \TwigA11y\Template\TemplateClassifier
 * @covers \TwigA11y\Template\TemplateKind
 */
final class TemplateClassifierTest extends TestCase
{
    #[DataProvider('provideTemplates')]
    public function testClassify(string $content, TemplateKind $expected): void
    {
        $kind = TemplateClassifier::classify($content);

        $this->assertSame($expected, $kind);
    }

    /**
     * @return \Iterator<string, array<mixed>>
     */
    public static function provideTemplates(): \Iterator
    {
        yield 'full page' => ["<!DOCTYPE html>\n<html>\n<body>\n<p>hi</p>\n</body>\n</html>", TemplateKind::FullPage];

        yield 'child template (extends)' => ["{% extends 'base.html.twig' %}\n<div>child content</div>", TemplateKind::ChildTemplate];

        yield 'partial fragment' => ['<div>{{ content }}</div>', TemplateKind::Partial];

        yield 'parent template (blocks only)' => ["{% block header %}{% endblock %}\n{% block content %}{% endblock %}", TemplateKind::ParentTemplate];

        yield 'mixed template (extends + blocks)' => ["{% extends 'base.html.twig' %}\n{% block content %}Hi{% endblock %}", TemplateKind::MixedTemplate];

        yield 'twig ux component' => ["{% props title %}\n<div>{{ title }}</div>", TemplateKind::TwigUxComponent];
    }
}
