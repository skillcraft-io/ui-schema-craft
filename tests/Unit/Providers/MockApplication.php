<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Providers;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

class MockApplication extends Container implements ApplicationContract
{
    protected $basePath;
    protected $environmentPath;
    protected $environment;

    public function version()
    {
        return '10.0.0';
    }

    public function basePath($path = '')
    {
        return $this->basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    public function bootstrapPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'bootstrap'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    public function configPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'config'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    public function databasePath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'database'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    public function resourcePath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'resources'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    public function storagePath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'storage'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    public function environment(...$environments)
    {
        if (count($environments) > 0) {
            return in_array($this->environment, $environments);
        }

        return $this->environment;
    }

    public function runningInConsole()
    {
        return false;
    }

    public function runningUnitTests()
    {
        return true;
    }

    public function maintenanceMode()
    {
        return null;
    }

    public function isDownForMaintenance()
    {
        return false;
    }

    public function registerConfiguredProviders()
    {
    }

    public function register($provider, $force = false)
    {
    }

    public function registerDeferredProvider($provider, $service = null)
    {
    }

    public function resolveProvider($provider)
    {
    }

    public function boot()
    {
    }

    public function booting($callback)
    {
    }

    public function booted($callback)
    {
    }

    public function bootstrapWith(array $bootstrappers)
    {
    }

    public function getLocale()
    {
        return 'en';
    }

    public function getNamespace()
    {
        return 'App';
    }

    public function getProviders($provider)
    {
        return [];
    }

    public function hasBeenBootstrapped()
    {
        return true;
    }

    public function loadDeferredProviders()
    {
    }

    public function setLocale($locale)
    {
    }

    public function shouldSkipMiddleware()
    {
        return false;
    }

    public function terminate()
    {
    }

    public function terminating($callback)
    {
    }

    public function hasDebugModeEnabled()
    {
        return true;
    }

    public function environmentPath()
    {
        return $this->environmentPath ?: $this->basePath;
    }

    public function environmentFile()
    {
        return '.env';
    }

    public function environmentFilePath()
    {
        return $this->environmentPath().DIRECTORY_SEPARATOR.$this->environmentFile();
    }

    public function getCachedServicesPath()
    {
        return '';
    }

    public function getCachedPackagesPath()
    {
        return '';
    }

    public function getCachedConfigPath()
    {
        return '';
    }

    public function getCachedRoutesPath()
    {
        return '';
    }

    public function getCachedEventsPath()
    {
        return '';
    }

    public function vendorPath()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'vendor';
    }

    public function langPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'lang'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    public function publicPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'public'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}
