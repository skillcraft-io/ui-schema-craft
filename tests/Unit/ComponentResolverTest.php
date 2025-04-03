<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit;

use RuntimeException;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\ComponentResolver;
use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;

class TestFormSchema extends UIComponentSchema
{
    protected string $type = 'test-form';
    protected string $version = '1.0.0';

    public function properties(): array
    {
        return [
            'name' => [
                'type' => 'string',
                'required' => true
            ]
        ];
    }

    protected function getValidationSchema(): ?array
    {
        return [
            'name' => ['type' => 'string', 'required' => true]
        ];
    }
}

class TestButtonSchema extends UIComponentSchema
{
    protected string $type = 'test-button';
    protected string $version = '1.0.0';

    public function properties(): array
    {
        return [
            'label' => [
                'type' => 'string',
                'required' => true
            ]
        ];
    }

    protected function getValidationSchema(): ?array
    {
        return [
            'label' => ['type' => 'string', 'required' => true]
        ];
    }
}

class ComponentResolverTest extends TestCase
{
    private ComponentResolver $resolver;
    private string $testDir;
    private string $originalComposerJson;
    private string $composerPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new ComponentResolver();
        
        // Create test directory
        $this->testDir = __DIR__ . '/../Fixtures';
        if (!is_dir($this->testDir)) {
            mkdir($this->testDir, 0777, true);
        }

        // Backup composer.json for modification tests
        $this->composerPath = dirname(__DIR__, 2) . '/composer.json';
        $this->originalComposerJson = file_get_contents($this->composerPath);
    }

    protected function tearDown(): void
    {
        // Restore original composer.json
        if (isset($this->originalComposerJson)) {
            file_put_contents($this->composerPath, $this->originalComposerJson);
            chmod($this->composerPath, 0644);
        }
        parent::tearDown();
    }

    public function test_register_adds_component_to_map(): void
    {
        $this->resolver->register(TestFormSchema::class);
        
        $this->assertEquals(TestFormSchema::class, $this->resolver->resolve('test-form'));
        $this->assertEquals(['test-form'], $this->resolver->getTypes());
    }

    public function test_register_many_adds_multiple_components(): void
    {
        $this->resolver->registerMany([
            TestFormSchema::class,
            TestButtonSchema::class
        ]);

        $this->assertEquals(TestFormSchema::class, $this->resolver->resolve('test-form'));
        $this->assertEquals(TestButtonSchema::class, $this->resolver->resolve('test-button'));
        $this->assertEqualsCanonicalizing(
            ['test-form', 'test-button'],
            $this->resolver->getTypes()
        );
    }

    public function test_resolve_returns_null_for_unknown_type(): void
    {
        $this->assertNull($this->resolver->resolve('unknown-type'));
    }

    public function test_register_ignores_non_component_classes(): void
    {
        $this->resolver->register(self::class);
        $this->assertEmpty($this->resolver->getTypes());
    }

    public function test_register_namespace_adds_all_components_from_namespace(): void
    {
        $this->resolver->registerNamespace('Skillcraft\\UiSchemaCraft\\Tests\\Fixtures\\Components');

        $this->assertEquals(
            'Skillcraft\\UiSchemaCraft\\Tests\\Fixtures\\Components\\InputSchema',
            $this->resolver->resolve('input')
        );
        $this->assertEquals(
            'Skillcraft\\UiSchemaCraft\\Tests\\Fixtures\\Components\\SelectSchema',
            $this->resolver->resolve('select')
        );
        $this->assertEqualsCanonicalizing(
            ['input', 'select'],
            $this->resolver->getTypes()
        );
    }

    public function test_register_namespace_ignores_non_component_classes(): void
    {
        $this->resolver->registerNamespace('Skillcraft\\UiSchemaCraft\\Tests\\Fixtures\\OtherComponents');
        $this->assertEmpty($this->resolver->getTypes());
    }

    public function test_register_namespace_with_invalid_namespace(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No PSR-4 autoload path found for namespace "NonExistent\\Namespace"');
        $this->resolver->registerNamespace('NonExistent\\Namespace');
    }

    public function test_register_namespace_with_empty_namespace(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Namespace cannot be empty');
        $this->resolver->registerNamespace('\\\\');
    }

    public function test_register_namespace_handles_trailing_slashes(): void
    {
        $this->resolver->registerNamespace('\\Skillcraft\\UiSchemaCraft\\Tests\\Fixtures\\Components\\');

        $this->assertEquals(
            'Skillcraft\\UiSchemaCraft\\Tests\\Fixtures\\Components\\InputSchema',
            $this->resolver->resolve('input')
        );
    }

    public function test_register_namespace_with_invalid_composer_json(): void
    {
        file_put_contents($this->composerPath, 'invalid json content');
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid composer.json format');
        
        $this->resolver->registerNamespace('Skillcraft\\UiSchemaCraft\\Tests\\Fixtures\\Components');
    }

    public function test_register_namespace_with_unreadable_composer_json(): void
    {
        // Make composer.json unreadable
        chmod($this->composerPath, 0000);
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to read composer.json: file is not readable');
        
        try {
            $this->resolver->registerNamespace('Skillcraft\\UiSchemaCraft\\Tests\\Fixtures\\Components');
        } finally {
            chmod($this->composerPath, 0644);
        }
    }

    public function test_register_namespace_ignores_non_existent_classes(): void
    {
        // Create a PHP file with a class that doesn't exist in autoloader
        $nonExistentClassFile = $this->testDir . '/Components/NonExistentSchema.php';
        if (!is_dir(dirname($nonExistentClassFile))) {
            mkdir(dirname($nonExistentClassFile), 0777, true);
        }
        
        file_put_contents($nonExistentClassFile, '<?php
            namespace Skillcraft\\UiSchemaCraft\\Tests\\Fixtures\\Components;
            class NonExistentSchema {}
        ');

        $this->resolver->registerNamespace('Skillcraft\\UiSchemaCraft\\Tests\\Fixtures\\Components');

        // Should still find our valid components
        $this->assertEqualsCanonicalizing(
            ['input', 'select'],
            $this->resolver->getTypes()
        );

        // Clean up
        unlink($nonExistentClassFile);
    }
}
