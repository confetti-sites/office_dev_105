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
        $response = new View();

        switch (true) {
            case request()->uri() === '/waiting-list-step-1':
                return $response->view('website.waiting-list-step-1');
            case request()->uri() === '/waiting-list-step-2':
                return $response->view('website.waiting-list-step-2');
            case request()->uri() === '/pricing':
                return $response->view('website.pricing');
            case str_starts_with(request()->uri(), '/docs'):
                return $response->view('website.documentation');
            case request()->uri() === '/blogs':
                return $response->view('website.blog_overview');
            case str_starts_with(request()->uri(), '/blogs/'):
                return $response->view('website.blog_detail');
            case str_starts_with(request()->uri(), '/features'):
                return $response->view('website.feature');
            default:
                return $response->view('website.errors.page_not_found');
        }
    }
}