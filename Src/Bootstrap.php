<?php

declare(strict_types=1);

namespace Src;

use App\Exceptions\FileNotFoundException;
use App\Render\BladeOne;
use App\Render\RenderService;
use Exception;
use Src\Http\Routes\Admin;
use Src\Http\Routes\Website;

class Bootstrap
{
    public function __construct(
        private readonly string $repository,
        private readonly string $cacheDir,
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function boot(): string
    {
        $response = match (true) {
            Admin::canRender()   => Admin::render(),
            Website::canRender() => Website::render(),
            default              => throw new \Exception('Route not found'),
        };

        $view                 = $response->getView();
        $driver               = new BladeOne($this->repository, $this->cacheDir, BladeOne::MODE_DEBUG);
        $driver->includeScope = false;

        // Define the current view directory
        if (!defined('__REPOSITORY_PATH__')) {
            define('__REPOSITORY_PATH__', $this->repository);
        }

        $path = str_replace('.', '/', $view);
        if (!file_exists("$this->repository/$path.blade.php")) {
            throw new Exception(
                "View not found. Looking for file: $path.blade.php"
            );
        }

        return $driver->run($view, $response->getVariables());
    }
}