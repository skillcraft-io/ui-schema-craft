<?php

namespace Skillcraft\UiSchemaCraft\Examples;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;

class ProductConfigurationSchema extends UIComponentSchema
{
    protected string $component = 'ProductConfiguration';

    protected function properties(): array
    {
        return [
            // Basic Product Information
            PropertyBuilder::string('name')
                ->description('Product name')
                ->required(),

            PropertyBuilder::string('sku')
                ->description('Stock keeping unit')
                ->required(),

            // Pricing
            PropertyBuilder::object('pricing')
                ->description('Product pricing configuration')
                ->properties([
                    PropertyBuilder::currency('base_price')
                        ->description('Base product price')
                        ->required(),

                    PropertyBuilder::currency('sale_price')
                        ->description('Sale price if applicable')
                        ->nullable(),

                    PropertyBuilder::percentage('discount')
                        ->description('Discount percentage')
                        ->nullable(),

                    PropertyBuilder::boolean('tax_included')
                        ->description('Whether price includes tax')
                        ->default(false),
                ])
                ->required(),

            // Inventory
            PropertyBuilder::object('inventory')
                ->description('Inventory management settings')
                ->properties([
                    PropertyBuilder::number('stock_level')
                        ->description('Current stock quantity')
                        ->required(),

                    PropertyBuilder::number('low_stock_threshold')
                        ->description('Low stock alert threshold')
                        ->default(10),

                    PropertyBuilder::boolean('track_inventory')
                        ->description('Enable inventory tracking')
                        ->default(true),

                    PropertyBuilder::string('warehouse_location')
                        ->description('Storage location code')
                        ->nullable(),
                ])
                ->required(),

            // Variants
            PropertyBuilder::object('variants')
                ->description('Product variant configuration')
                ->properties([
                    PropertyBuilder::array('options')
                        ->description('Available variant options')
                        ->required(),

                    PropertyBuilder::array('combinations')
                        ->description('Valid variant combinations')
                        ->required(),

                    PropertyBuilder::object('pricing_rules')
                        ->description('Variant-specific pricing')
                        ->nullable(),
                ])
                ->nullable(),

            // Shipping
            PropertyBuilder::object('shipping')
                ->description('Shipping configuration')
                ->properties([
                    PropertyBuilder::number('weight')
                        ->description('Product weight')
                        ->required(),

                    PropertyBuilder::object('dimensions')
                        ->description('Product dimensions')
                        ->properties([
                            PropertyBuilder::number('length')->required(),
                            PropertyBuilder::number('width')->required(),
                            PropertyBuilder::number('height')->required(),
                        ])
                        ->required(),

                    PropertyBuilder::array('shipping_classes')
                        ->description('Applicable shipping classes')
                        ->nullable(),
                ])
                ->required(),

            // Digital Product
            PropertyBuilder::object('digital')
                ->description('Digital product settings')
                ->properties([
                    PropertyBuilder::boolean('is_digital')
                        ->description('Is this a digital product')
                        ->default(false),

                    PropertyBuilder::array('download_files')
                        ->description('Downloadable files')
                        ->nullable(),

                    PropertyBuilder::number('download_limit')
                        ->description('Maximum downloads allowed')
                        ->nullable(),
                ])
                ->nullable(),
        ];
    }

    public function getExampleData(): array
    {
        return [
            'name' => 'Premium T-Shirt',
            'sku' => 'PROD-001',
            'pricing' => [
                'base_price' => 29.99,
                'discount' => 10,
            ],
            'inventory' => [
                'stock_level' => 100,
                'low_stock_threshold' => 20,
            ],
            'shipping' => [
                'weight' => 0.2,
                'dimensions' => [
                    'length' => 30,
                    'width' => 20,
                    'height' => 2,
                ],
            ],
        ];
    }

    public function getLiveData(): array
    {
        return [];
    }
}
