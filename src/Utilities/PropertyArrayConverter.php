<?php

namespace Skillcraft\UiSchemaCraft\Utilities;

use Skillcraft\UiSchemaCraft\Schema\Property;

/**
 * Property Array Converter Utility
 * 
 * Provides methods to safely convert Property objects to arrays recursively,
 * ensuring that all nested Property objects are properly converted.
 */
class PropertyArrayConverter
{
    /**
     * Recursively convert any Property objects to arrays throughout a structure
     * 
     * @param mixed $input The input data which may contain Property objects
     * @return mixed The input with all Property objects converted to arrays
     */
    public static function toArray(mixed $input): mixed
    {
        // If this is a Property object, convert it to array and then process its contents
        if ($input instanceof Property) {
            $input = $input->toArray();
        }
        
        // If not an array, return as is
        if (!is_array($input)) {
            return $input;
        }
        
        // Process array elements recursively
        $result = [];
        foreach ($input as $key => $value) {
            if ($value instanceof Property) {
                // Convert Property object to array
                $result[$key] = self::toArray($value->toArray());
            } elseif (is_array($value)) {
                // Recursively process arrays that might contain Property objects
                $result[$key] = self::toArray($value);
            } else {
                // Non-Property, non-array values can be used directly
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
}
