<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Facade;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        $this->clearBootstrapCacheFiles();
        $this->resetFacadeState();

        $app = require Application::inferBasePath().'/bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Remove cached config/routes bootstrap files so tests always load fresh routes from disk.
     */
    private function clearBootstrapCacheFiles(): void
    {
        $cacheDir = Application::inferBasePath().'/bootstrap/cache';

        foreach (['routes-v7.php', 'config.php'] as $file) {
            $path = $cacheDir.'/'.$file;

            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    /**
     * Drop facade roots from the previous application instance.
     *
     * PHPUnit re-bootstraps the app per test; Route and other facades cache their
     * resolved instance per subclass. Without clearing those, route registration
     * from the second test onward targets a flushed router.
     */
    private function resetFacadeState(): void
    {
        Facade::setFacadeApplication(null);

        foreach (Facade::defaultAliases()->values()->all() as $facade) {
            if (class_exists($facade) && is_subclass_of($facade, Facade::class)) {
                $facade::clearResolvedInstances();
            }
        }

        Facade::clearResolvedInstances();
    }
}
