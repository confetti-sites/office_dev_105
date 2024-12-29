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
            case request()->uri() === '/waiting-list':
                return new View('website.waitlist');
            case request()->uri() === '/waiting-callback':
                return new View('website.waitlist_callback');
            case request()->uri() === '/auth/callback':
                return new View('website.includes.auth.callback');
            case request()->uri() === '/waiting-list-step-1':
                return new View('website.waiting-list-step-1');
            case request()->uri() === '/waiting-list-step-2':
                return new View('website.waiting-list-step-2');
            case request()->uri() === '/pricing':
                return new View('website.pricing');
            case str_starts_with(request()->uri(), '/docs'):
                return new View('website.docs');
            case request()->uri() === '/blogs':
                return new View('website.tmp.blog_overview');
            case str_starts_with(request()->uri(), '/blogs/'):
                return new View('website.tmp.blog_detail');
            case str_starts_with(request()->uri(), '/features'):
                return new View('website.features');
            default:
                return new View('website.404');
        }
    }
}