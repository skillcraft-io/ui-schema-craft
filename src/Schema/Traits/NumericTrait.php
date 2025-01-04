<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

use Skillcraft\UiSchemaCraft\Schema\Property;

trait NumericTrait
{
    /**
     * Create a range property.
     */
    public function range(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function ($builder) {
                $builder->number('value')
                    ->addAttribute('default', 0)
                    ->description('Current value');

                $builder->number('min')
                    ->addAttribute('default', 0)
                    ->description('Minimum value');

                $builder->number('max')
                    ->addAttribute('default', 100)
                    ->description('Maximum value');

                $builder->number('step')
                    ->addAttribute('default', 1)
                    ->description('Step increment');

                $builder->boolean('showTicks')
                    ->addAttribute('default', false)
                    ->description('Show tick marks');

                $builder->boolean('showValue')
                    ->addAttribute('default', true)
                    ->description('Show current value');
            });
    }

    /**
     * Create a rating property.
     */
    public function rating(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function ($builder) {
                $builder->number('value')
                    ->addAttribute('default', 0)
                    ->description('Current rating');

                $builder->number('max')
                    ->addAttribute('default', 5)
                    ->description('Maximum rating');

                $builder->boolean('allowHalf')
                    ->addAttribute('default', false)
                    ->description('Allow half ratings');

                $builder->boolean('readonly')
                    ->addAttribute('default', false)
                    ->description('Read-only mode');

                $builder->object('icon')
                    ->withBuilder(function ($icon) {
                        $icon->string('filled')
                            ->addAttribute('default', 'fas fa-star')
                            ->description('Filled star icon');
                        
                        $icon->string('empty')
                            ->addAttribute('default', 'far fa-star')
                            ->description('Empty star icon');
                        
                        $icon->string('color')
                            ->addAttribute('default', 'text-yellow-400')
                            ->description('Star color');
                    });
            });
    }

    /**
     * Create a currency property.
     */
    public function currency(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function ($builder) {
                $builder->number('value')
                    ->description('Amount value');

                $builder->string('currency')
                    ->description('Currency code')
                    ->default('USD');

                $builder->string('locale')
                    ->description('Locale for formatting')
                    ->default('en-US');

                $builder->boolean('showSymbol')
                    ->description('Show currency symbol')
                    ->default(true);
            });
    }

    /**
     * Create a percentage property.
     */
    public function percentage(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function ($builder) {
                $builder->number('value')
                    ->description('Percentage value')
                    ->addAttribute('step', 0.01)
                    ->min(0)
                    ->max(100);

                $builder->boolean('showSymbol')
                    ->description('Show percentage symbol')
                    ->default(true);

                $builder->number('decimals')
                    ->description('Number of decimal places')
                    ->default(0)
                    ->min(0)
                    ->max(20);
            });
    }
}
