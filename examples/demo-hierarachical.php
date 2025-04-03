<?php

// This script demonstrates the hierarchical serialization in UI Schema Craft

require_once __DIR__ . '/../vendor/autoload.php';

use App\UiSchemas\LoginSchema;
use Skillcraft\SchemaValidation\Contracts\ValidatorInterface;

// Create a simple mock validator for demonstration
class DemoValidator implements ValidatorInterface
{
    public function validate($data, array $rules): bool
    {
        return true; // Simple mock always validates successfully
    }
}

// Create a sample validator
$validator = new DemoValidator();

// Create the login schema component
$loginSchema = new LoginSchema($validator);

// Convert to array with hierarchical structure
$output = $loginSchema->toArray();

// Display the output in a readable format
echo "=== Hierarchical Component Structure ===\n\n";
echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

// Comparison with flat structure (for demonstration)
echo "=== DIFFERENCE FROM PREVIOUS FLAT STRUCTURE ===\n\n";
echo "The previous structure flattened all properties to the top level.\n";
echo "The new hierarchical structure maintains nested objects like:\n";
echo "- form_text.title instead of just title\n";
echo "- form_config.schema.email.label instead of just label\n\n";
echo "This makes it easier to organize complex component data in the frontend.\n";
