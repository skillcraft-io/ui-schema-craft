<?php

namespace Skillcraft\UiSchemaCraft;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;
use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Symfony\Component\Finder\Finder;

class ComponentResolver
{
    /**
     * @var array<string, string> Map of component type to class name
     */
    private array $componentMap = [];

    /**
     * Register a component class
     */
    public function register(string $componentClass): void
    {
        if (!is_subclass_of($componentClass, UIComponentSchema::class)) {
            return;
        }

        $type = $this->getTypeFromClass($componentClass);
        $this->componentMap[$type] = $componentClass;
    }

    /**
     * Register multiple component classes
     */
    public function registerMany(array $componentClasses): void
    {
        foreach ($componentClasses as $class) {
            $this->register($class);
        }
    }

    /**
     * Register all components in a namespace
     *
     * @throws RuntimeException If composer.json cannot be read or parsed
     */
    public function registerNamespace(string $namespace): void
    {
        $composerFile = dirname(__DIR__) . '/composer.json';
        if (!file_exists($composerFile)) {
            throw new RuntimeException('composer.json not found in project root');
        }

        if (!is_readable($composerFile)) {
            throw new RuntimeException('Failed to read composer.json: file is not readable');
        }

        $composerJson = @file_get_contents($composerFile);
        if ($composerJson === false) {
            throw new RuntimeException('Failed to read composer.json: ' . error_get_last()['message'] ?? '');
        }

        $composer = json_decode($composerJson, true);
        if (!is_array($composer)) {
            throw new RuntimeException('Invalid composer.json format');
        }

        // Clean namespace input
        $namespace = trim($namespace, '\\');
        if (empty($namespace)) {
            throw new RuntimeException('Namespace cannot be empty');
        }

        $psr4 = array_merge(
            $composer['autoload']['psr-4'] ?? [],
            $composer['autoload-dev']['psr-4'] ?? []
        );

        $found = false;
        foreach ($psr4 as $prefix => $paths) {
            $prefix = rtrim($prefix, '\\');
            if (!str_starts_with($namespace, $prefix)) {
                continue;
            }

            $paths = (array) $paths;
            foreach ($paths as $path) {
                $relativePath = str_replace(
                    $prefix,
                    '',
                    $namespace
                );
                $searchPath = dirname(__DIR__) . '/' . trim($path, '/') . '/' . trim(str_replace('\\', '/', $relativePath), '/');

                if (!is_dir($searchPath)) {
                    continue;
                }

                $found = true;
                $finder = new Finder();
                $finder->files()->in($searchPath)->name('*.php');

                foreach ($finder as $file) {
                    $className = $namespace . '\\' . $file->getBasename('.php');
                    if (class_exists($className)) {
                        $this->register($className);
                    }
                }
            }
        }

        if (!$found) {
            throw new RuntimeException(sprintf(
                'No PSR-4 autoload path found for namespace "%s"',
                $namespace
            ));
        }
    }

    /**
     * Get component class by type
     */
    public function resolve(string $type): ?string
    {
        return $this->componentMap[$type] ?? null;
    }

    /**
     * Get all registered component types
     */
    public function getTypes(): array
    {
        return array_keys($this->componentMap);
    }

    /**
     * Check if a component type exists
     *
     * @param string $type Component type
     * @return bool
     */
    public function has(string $type): bool
    {
        return isset($this->componentMap[$type]);
    }

    /**
     * Get all registered components
     *
     * @return array Array of component types and their classes
     */
    public function getComponents(): array
    {
        return $this->componentMap;
    }

    /**
     * Get component type from class name
     */
    private function getTypeFromClass(string $class): string
    {
        return Str::kebab(
            Str::beforeLast(class_basename($class), 'Schema')
        );
    }
}
