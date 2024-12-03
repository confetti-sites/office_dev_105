<?php

declare(strict_types=1);

namespace Src\Http\Routes;

class Admin
{
    public static function canRender(): bool
    {
        return request()->uri() === '/admin';
    }

    public static function render(): string
    {
        return 'admin.index';
    }
}