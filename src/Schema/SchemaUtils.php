<?php

namespace Skillcraft\UiSchemaCraft\Schema;

use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;

/**
 * Utility class for creating common schema structures.
 */
class SchemaUtils
{
    /**
     * Create a Tailwind color property schema.
     *
     * @param string $name
     * @param array $defaults
     * @return Property
     */
    public static function tailwindColor(string $name, array $defaults = []): Property
    {
        $property = PropertyBuilder::object($name);
        
        $property->addProperty('background', 
            PropertyBuilder::string('background')
                ->default($defaults['background'] ?? 'bg-gray-500')
                ->addAttribute('title', 'Background Color')
                ->description('Background color class')
        );
        
        $property->addProperty('text', 
            PropertyBuilder::string('text')
                ->default($defaults['text'] ?? 'text-white')
                ->addAttribute('title', 'Text Color')
                ->description('Text color class')
        );
        
        $property->addProperty('hover', 
            PropertyBuilder::string('hover')
                ->default($defaults['hover'] ?? 'hover:bg-gray-600')
                ->addAttribute('title', 'Hover State')
                ->description('Hover state color class')
        );
        
        if (isset($defaults['focus'])) {
            $property->addProperty('focus', 
                PropertyBuilder::string('focus')
                    ->default($defaults['focus'])
                    ->addAttribute('title', 'Focus State')
                    ->description('Focus state color class')
            );
        }
        
        if (isset($defaults['active'])) {
            $property->addProperty('active', 
                PropertyBuilder::string('active')
                    ->default($defaults['active'])
                    ->addAttribute('title', 'Active State')
                    ->description('Active state color class')
            );
        }
        
        return $property;
    }
    
    /**
     * Create a Tailwind spacing property schema.
     *
     * @param string $name
     * @param array $defaults
     * @return Property
     */
    public static function tailwindSpacing(string $name, array $defaults = []): Property
    {
        $property = PropertyBuilder::object($name);
        
        $property->addProperty('padding', 
            PropertyBuilder::string('padding')
                ->default($defaults['padding'] ?? 'p-4')
                ->addAttribute('title', 'Padding')
                ->description('Padding class')
        );
        
        $property->addProperty('margin', 
            PropertyBuilder::string('margin')
                ->default($defaults['margin'] ?? 'm-0')
                ->addAttribute('title', 'Margin')
                ->description('Margin class')
        );
        
        $property->addProperty('gap', 
            PropertyBuilder::string('gap')
                ->default($defaults['gap'] ?? 'gap-4')
                ->addAttribute('title', 'Gap')
                ->description('Gap class for flex and grid layouts')
        );
        
        if (isset($defaults['space'])) {
            $property->addProperty('space', 
                PropertyBuilder::string('space')
                    ->default($defaults['space'])
                    ->addAttribute('title', 'Space Between')
                    ->description('Space between class for child elements')
            );
        }
        
        return $property;
    }
    
    /**
     * Create a Tailwind typography property schema.
     *
     * @param string $name
     * @param array $defaults
     * @return Property
     */
    public static function tailwindTypography(string $name, array $defaults = []): Property
    {
        $property = PropertyBuilder::object($name);
        
        $property->addProperty('fontSize', 
            PropertyBuilder::string('fontSize')
                ->default($defaults['fontSize'] ?? 'text-base')
                ->addAttribute('title', 'Font Size')
                ->description('Font size class')
        );
        
        $property->addProperty('fontWeight', 
            PropertyBuilder::string('fontWeight')
                ->default($defaults['fontWeight'] ?? 'font-normal')
                ->addAttribute('title', 'Font Weight')
                ->description('Font weight class')
        );
        
        $property->addProperty('textAlign', 
            PropertyBuilder::string('textAlign')
                ->default($defaults['textAlign'] ?? 'text-left')
                ->addAttribute('title', 'Text Alignment')
                ->description('Text alignment class')
        );
        
        $property->addProperty('lineHeight', 
            PropertyBuilder::string('lineHeight')
                ->default($defaults['lineHeight'] ?? 'leading-normal')
                ->addAttribute('title', 'Line Height')
                ->description('Line height class')
        );
        
        // Add size property for test compatibility
        $property->addProperty('size', 
            PropertyBuilder::string('size')
                ->default($defaults['size'] ?? 'text-base')
                ->addAttribute('title', 'Text Size')
                ->description('Text size class')
        );
        
        // Add weight property for test compatibility
        $property->addProperty('weight', 
            PropertyBuilder::string('weight')
                ->default($defaults['weight'] ?? 'font-normal')
                ->addAttribute('title', 'Font Weight')
                ->description('Font weight class')
        );
        
        // Add color property for test compatibility
        $property->addProperty('color', 
            PropertyBuilder::string('color')
                ->default($defaults['color'] ?? 'text-gray-900')
                ->addAttribute('title', 'Text Color')
                ->description('Text color class')
        );
        
        // Add align property for test compatibility
        $property->addProperty('align', 
            PropertyBuilder::string('align')
                ->default($defaults['align'] ?? 'text-left')
                ->addAttribute('title', 'Text Alignment')
                ->description('Text alignment class')
        );
        
        if (isset($defaults['fontFamily'])) {
            $property->addProperty('fontFamily', 
                PropertyBuilder::string('fontFamily')
                    ->default($defaults['fontFamily'])
                    ->addAttribute('title', 'Font Family')
                    ->description('Font family class')
            );
        }
        
        if (isset($defaults['textTransform'])) {
            $property->addProperty('textTransform', 
                PropertyBuilder::string('textTransform')
                    ->default($defaults['textTransform'])
                    ->addAttribute('title', 'Text Transform')
                    ->description('Text transform class')
            );
        }
        
        return $property;
    }
    
    /**
     * Create a Tailwind container property schema.
     *
     * @param string $name
     * @param array $defaults
     * @return Property
     */
    public static function tailwindContainer(string $name, array $defaults = []): Property
    {
        $property = PropertyBuilder::object($name);
        
        $property->addProperty('width', 
            PropertyBuilder::string('width')
                ->default($defaults['width'] ?? 'w-full')
                ->addAttribute('title', 'Width')
                ->description('Container width class')
        );
        
        $property->addProperty('maxWidth', 
            PropertyBuilder::string('maxWidth')
                ->default($defaults['maxWidth'] ?? 'max-w-7xl')
                ->addAttribute('title', 'Max Width')
                ->description('Maximum width class')
        );
        
        $property->addProperty('mx', 
            PropertyBuilder::string('mx')
                ->default($defaults['mx'] ?? 'mx-auto')
                ->addAttribute('title', 'Horizontal Margin')
                ->description('Horizontal margin class')
        );
        
        $property->addProperty('overflow', 
            PropertyBuilder::string('overflow')
                ->default($defaults['overflow'] ?? 'overflow-hidden')
                ->addAttribute('title', 'Overflow')
                ->description('Overflow handling class')
        );
        
        // Add background for test compatibility
        $property->addProperty('background', 
            PropertyBuilder::string('background')
                ->default($defaults['background'] ?? 'bg-white')
                ->addAttribute('title', 'Background')
                ->description('Background color class')
        );
        
        // Add rounded for test compatibility
        $property->addProperty('rounded', 
            PropertyBuilder::string('rounded')
                ->default($defaults['rounded'] ?? 'rounded-lg')
                ->addAttribute('title', 'Border Radius')
                ->description('Border radius class')
        );
        
        // Add shadow for test compatibility
        $property->addProperty('shadow', 
            PropertyBuilder::string('shadow')
                ->default($defaults['shadow'] ?? 'shadow')
                ->addAttribute('title', 'Shadow')
                ->description('Shadow class')
        );
        
        // Add border for test compatibility
        $property->addProperty('border', 
            PropertyBuilder::string('border')
                ->default($defaults['border'] ?? 'border')
                ->addAttribute('title', 'Border')
                ->description('Border class')
        );
        
        return $property;
    }
    
    /**
     * Create a Tailwind flex property schema.
     *
     * @param string $name
     * @param array $defaults
     * @return Property
     */
    public static function tailwindFlex(string $name, array $defaults = []): Property
    {
        $property = PropertyBuilder::object($name);
        
        $property->addProperty('display', 
            PropertyBuilder::string('display')
                ->default($defaults['display'] ?? 'flex')
                ->addAttribute('title', 'Display')
                ->description('Display class')
        );
        
        $property->addProperty('direction', 
            PropertyBuilder::string('direction')
                ->default($defaults['direction'] ?? 'flex-row')
                ->addAttribute('title', 'Flex Direction')
                ->description('Flex direction class')
        );
        
        $property->addProperty('wrap', 
            PropertyBuilder::string('wrap')
                ->default($defaults['wrap'] ?? 'flex-wrap')
                ->addAttribute('title', 'Flex Wrap')
                ->description('Flex wrap class')
        );
        
        $property->addProperty('justify', 
            PropertyBuilder::string('justify')
                ->default($defaults['justify'] ?? 'justify-start')
                ->addAttribute('title', 'Justify Content')
                ->description('Justify content class')
        );
        
        // Add align property for test compatibility, instead of items
        $property->addProperty('align', 
            PropertyBuilder::string('align')
                ->default($defaults['align'] ?? 'items-start')
                ->addAttribute('title', 'Align Items')
                ->description('Align items class')
        );
        
        // Add gap property for test compatibility
        $property->addProperty('gap', 
            PropertyBuilder::string('gap')
                ->default($defaults['gap'] ?? 'gap-4')
                ->addAttribute('title', 'Gap')
                ->description('Gap between items')
        );
        
        return $property;
    }
    
    /**
     * Create a Tailwind grid property schema.
     *
     * @param string $name
     * @param array $defaults
     * @return Property
     */
    public static function tailwindGrid(string $name, array $defaults = []): Property
    {
        $property = PropertyBuilder::object($name);
        
        $property->addProperty('display', 
            PropertyBuilder::string('display')
                ->default($defaults['display'] ?? 'grid')
                ->addAttribute('title', 'Display')
                ->description('Display class')
        );
        
        $property->addProperty('cols', 
            PropertyBuilder::string('cols')
                ->default($defaults['cols'] ?? 'grid-cols-1')
                ->addAttribute('title', 'Grid Columns')
                ->description('Grid columns class')
        );
        
        $property->addProperty('rows', 
            PropertyBuilder::string('rows')
                ->default($defaults['rows'] ?? 'grid-rows-1')
                ->addAttribute('title', 'Grid Rows')
                ->description('Grid rows class')
        );
        
        $property->addProperty('gap', 
            PropertyBuilder::string('gap')
                ->default($defaults['gap'] ?? 'gap-4')
                ->addAttribute('title', 'Gap')
                ->description('Grid gap class')
        );
        
        // Add flow property for test compatibility, replaces flowRow
        $property->addProperty('flow', 
            PropertyBuilder::string('flow')
                ->default($defaults['flow'] ?? 'grid-flow-row')
                ->addAttribute('title', 'Grid Flow')
                ->description('Grid flow class')
        );
        
        if (isset($defaults['autoRows'])) {
            $property->addProperty('autoRows', 
                PropertyBuilder::string('autoRows')
                    ->default($defaults['autoRows'])
                    ->addAttribute('title', 'Auto Rows')
                    ->description('Auto rows class')
            );
        }
        
        if (isset($defaults['autoCols'])) {
            $property->addProperty('autoCols', 
                PropertyBuilder::string('autoCols')
                    ->default($defaults['autoCols'])
                    ->addAttribute('title', 'Auto Columns')
                    ->description('Auto columns class')
            );
        }
        
        return $property;
    }
    
    /**
     * Create a Tailwind responsive property schema.
     *
     * @param string $name
     * @param array $defaults
     * @return Property
     */
    public static function tailwindResponsive(string $name, array $defaults = []): Property
    {
        $property = PropertyBuilder::object($name);
        
        $property->addProperty('sm', 
            PropertyBuilder::string('sm')
                ->default($defaults['sm'] ?? 'sm:text-sm')
                ->addAttribute('title', 'Small Screen')
                ->description('Small screen class')
        );
        
        $property->addProperty('md', 
            PropertyBuilder::string('md')
                ->default($defaults['md'] ?? 'md:text-base')
                ->addAttribute('title', 'Medium Screen')
                ->description('Medium screen class')
        );
        
        $property->addProperty('lg', 
            PropertyBuilder::string('lg')
                ->default($defaults['lg'] ?? 'lg:text-lg')
                ->addAttribute('title', 'Large Screen')
                ->description('Large screen class')
        );
        
        if (isset($defaults['xl'])) {
            $property->addProperty('xl', 
                PropertyBuilder::string('xl')
                    ->default($defaults['xl'])
                    ->addAttribute('title', 'Extra Large Screen')
                    ->description('Extra large screen class')
            );
        }
        
        if (isset($defaults['2xl'])) {
            $property->addProperty('2xl', 
                PropertyBuilder::string('2xl')
                    ->default($defaults['2xl'])
                    ->addAttribute('title', '2XL Screen')
                    ->description('2XL screen class')
            );
        }
        
        return $property;
    }
    
    /**
     * Create a Tailwind animation property schema.
     *
     * @param string $name
     * @param array $defaults
     * @return Property
     */
    public static function tailwindAnimation(string $name, array $defaults = []): Property
    {
        $property = PropertyBuilder::object($name);
        
        $property->addProperty('transition', 
            PropertyBuilder::string('transition')
                ->default($defaults['transition'] ?? 'transition')
                ->addAttribute('title', 'Transition')
                ->description('Transition class')
        );
        
        $property->addProperty('duration', 
            PropertyBuilder::string('duration')
                ->default($defaults['duration'] ?? 'duration-300')
                ->addAttribute('title', 'Duration')
                ->description('Duration class')
        );
        
        $property->addProperty('timing', 
            PropertyBuilder::string('timing')
                ->default($defaults['timing'] ?? 'ease-in-out')
                ->addAttribute('title', 'Timing Function')
                ->description('Timing function class')
        );
        
        $property->addProperty('animate', 
            PropertyBuilder::string('animate')
                ->default($defaults['animate'] ?? '')
                ->addAttribute('title', 'Animation')
                ->description('Animation class')
        );
        
        if (isset($defaults['delay'])) {
            $property->addProperty('delay', 
                PropertyBuilder::string('delay')
                    ->default($defaults['delay'])
                    ->addAttribute('title', 'Delay')
                    ->description('Delay class')
            );
        }
        
        return $property;
    }
    
    /**
     * Create a Tailwind interactive property schema.
     *
     * @param string $name
     * @param array $defaults
     * @return Property
     */
    public static function tailwindInteractive(string $name, array $defaults = []): Property
    {
        $property = PropertyBuilder::object($name);
        
        $property->addProperty('hover', 
            PropertyBuilder::string('hover')
                ->default($defaults['hover'] ?? '')
                ->addAttribute('title', 'Hover')
                ->description('Hover state class')
        );
        
        $property->addProperty('focus', 
            PropertyBuilder::string('focus')
                ->default($defaults['focus'] ?? '')
                ->addAttribute('title', 'Focus')
                ->description('Focus state class')
        );
        
        $property->addProperty('active', 
            PropertyBuilder::string('active')
                ->default($defaults['active'] ?? '')
                ->addAttribute('title', 'Active')
                ->description('Active state class')
        );
        
        $property->addProperty('disabled', 
            PropertyBuilder::string('disabled')
                ->default($defaults['disabled'] ?? '')
                ->addAttribute('title', 'Disabled')
                ->description('Disabled state class')
        );
        
        return $property;
    }
}
