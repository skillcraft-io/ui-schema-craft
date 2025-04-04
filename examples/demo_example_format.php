<?php

// Include necessary files
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/LoginSchema.php';

// 1. Create the LoginSchema instance
$loginSchema = new \App\UiSchemas\LoginSchema();

// 2. Create service class with public testing method
class TestService extends \Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService
{
    public function __construct()
    {
        // No resolver needed for this test
    }
    
    // Make protected method accessible for testing
    public function testExtractValues($component)
    {
        return $this->extractComponentValues($component);
    }
}

// 3. Create test service
$service = new TestService();

// 4. STEP 1: Get the expected example value directly
$reflection = new \ReflectionClass($loginSchema);
$exampleProp = $reflection->getProperty('example');
$exampleProp->setAccessible(true);
$expectedValue = $exampleProp->getValue($loginSchema);

// 5. STEP 2: Get the actual extracted value using our method
$actualValue = $service->testExtractValues($loginSchema);

// 6. Display both for comparison
echo "====== EXPECTED FORMAT (from LoginSchema \$example) ======\n";
echo json_encode($expectedValue, JSON_PRETTY_PRINT) . "\n\n";

echo "====== ACTUAL EXTRACTED FORMAT ======\n";
echo json_encode($actualValue, JSON_PRETTY_PRINT) . "\n\n";

// 7. Compare and show if they match exactly
$exactMatch = ($expectedValue === $actualValue);
echo "EXACT MATCH: " . ($exactMatch ? "✅ YES" : "❌ NO") . "\n";

// 8. Show deep comparison if needed
if (!$exactMatch) {
    echo "\nDIFFERENCES:\n";
    $diff = array_diff_assoc_recursive($expectedValue, $actualValue);
    echo json_encode($diff, JSON_PRETTY_PRINT) . "\n";
}

// Helper function to do recursive comparison
function array_diff_assoc_recursive($array1, $array2) {
    $diff = [];
    foreach ($array1 as $key => $value) {
        if (!array_key_exists($key, $array2)) {
            $diff[$key] = $value;
            continue;
        }
        if (is_array($value)) {
            if (!is_array($array2[$key])) {
                $diff[$key] = $value;
                continue;
            }
            $recursiveDiff = array_diff_assoc_recursive($value, $array2[$key]);
            if (count($recursiveDiff)) {
                $diff[$key] = $recursiveDiff;
            }
            continue;
        }
        if ($value !== $array2[$key]) {
            $diff[$key] = $value;
        }
    }
    return $diff;
}
