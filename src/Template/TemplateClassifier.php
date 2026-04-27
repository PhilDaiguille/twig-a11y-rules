<?php

declare(strict_types=1);

namespace TwigA11y\Template;

final class TemplateClassifier
{
    public static function classify(string $content): TemplateKind
    {
        $hasExtends = str_contains($content, '{% extends');
        $hasBlock   = str_contains($content, '{% block');
        $hasHtml    = stripos($content, '<html') !== false;
        $hasBody    = stripos($content, '<body') !== false;
        $hasProps   = str_contains($content, '{% props');

        if ($hasProps) {
            return TemplateKind::TwigUxComponent;
        }

        if ($hasExtends && $hasBlock) {
            return TemplateKind::MixedTemplate;
        }

        if ($hasExtends) {
            return TemplateKind::ChildTemplate;
        }

        if ($hasBlock && !$hasHtml) {
            return TemplateKind::ParentTemplate;
        }

        if ($hasHtml && $hasBody) {
            return TemplateKind::FullPage;
        }

        return TemplateKind::Partial;
    }
}
