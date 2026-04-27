<?php

declare(strict_types=1);

namespace TwigA11y\Template;

enum TemplateKind
{
    case FullPage;
    case ChildTemplate;
    case Partial;
    case ParentTemplate;
    case MixedTemplate;
    case TwigUxComponent;
}
