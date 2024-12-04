<?php

declare(strict_types=1);

namespace Src\Http\Routes;

use Src\Http\Entity\View;

class Website
{
    public static function canRender(): bool
    {
        return true;
    }

    /** @noinspection PhpSwitchCanBeReplacedWithMatchExpressionInspection */
    public static function render(): View
    {
        switch (true) {
            case request()->uri() === '/':
                return new View('website.homepage');
            case request()->uri() === '/auth/callback':
                return new View('website.auth.callback');
            case request()->uri() === '/waiting-list-step-1':
                return new View('website.waiting-list-step-1');
            case request()->uri() === '/waiting-list-step-2':
                return new View('website.waiting-list-step-2');
            case request()->uri() === '/pricing':
                return new View('website.pricing');
            case str_starts_with(request()->uri(), '/docs'):
                return new View('website.documentation');
            case request()->uri() === '/blogs':
                return new View('website.blog_overview');
            case str_starts_with(request()->uri(), '/blogs/'):
                return new View('website.blog_detail');
            case str_starts_with(request()->uri(), '/features'):
                return new View('website.feature');
            default:
                return new View('website.errors.page_not_found');
        }
    }
}