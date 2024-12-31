<?php

namespace Skillcraft\UiSchemaCraft\Schema;

class PropertyBuilder
{
    protected array $properties = [];

    public static function make(): static
    {
        return new static();
    }

    public function add(Property $property): Property
    {
        $this->properties[$property->getName()] = $property;
        return $property;
    }

    // Basic Types
    public function string(string $name): Property
    {
        $property = Property::string($name);
        $this->properties[$name] = $property;
        return $property;
    }

    public function number(string $name): Property
    {
        $property = Property::number($name);
        $this->properties[$name] = $property;
        return $property;
    }

    public function boolean(string $name): Property
    {
        $property = Property::boolean($name);
        $this->properties[$name] = $property;
        return $property;
    }

    public function object(string $name): Property
    {
        $property = Property::object($name);
        $this->properties[$name] = $property;
        return $property;
    }

    public function array(string $name): Property
    {
        $property = Property::array($name);
        $this->properties[$name] = $property;
        return $property;
    }

    // Layout Patterns
    public function grid(string $name, callable $callback, array $defaults = []): Property
    {
        return $this->group($name, function (PropertyBuilder $builder) use ($callback, $defaults) {
            $builder->string('cols')
                ->default($defaults['cols'] ?? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3')
                ->description('Grid columns configuration');

            $builder->string('gap')
                ->default($defaults['gap'] ?? 'gap-4')
                ->description('Gap between grid items');

            $builder->group('items', $callback);
        });
    }

    public function flex(string $name, callable $callback, array $defaults = []): Property
    {
        return $this->group($name, function (PropertyBuilder $builder) use ($callback, $defaults) {
            $builder->string('direction')
                ->default($defaults['direction'] ?? 'flex-col md:flex-row')
                ->description('Flex direction');

            $builder->string('justify')
                ->default($defaults['justify'] ?? 'justify-start')
                ->enum(['justify-start', 'justify-end', 'justify-center', 'justify-between', 'justify-around', 'justify-evenly'])
                ->description('Justify content');

            $builder->string('align')
                ->default($defaults['align'] ?? 'items-start')
                ->enum(['items-start', 'items-end', 'items-center', 'items-baseline', 'items-stretch'])
                ->description('Align items');

            $builder->string('gap')
                ->default($defaults['gap'] ?? 'gap-4')
                ->description('Gap between flex items');

            $builder->string('wrap')
                ->default($defaults['wrap'] ?? 'flex-wrap')
                ->enum(['flex-wrap', 'flex-nowrap', 'flex-wrap-reverse'])
                ->description('Flex wrap behavior');

            $builder->group('items', $callback);
        });
    }

    public function stack(string $name, callable $callback, array $defaults = []): Property
    {
        return $this->flex($name, $callback, array_merge([
            'direction' => 'flex-col',
            'gap' => 'gap-2',
        ], $defaults));
    }

    public function container(string $name, callable $callback, array $defaults = []): Property
    {
        return $this->group($name, function (PropertyBuilder $builder) use ($callback, $defaults) {
            $builder->string('maxWidth')
                ->default($defaults['maxWidth'] ?? 'max-w-7xl')
                ->enum(['max-w-xs', 'max-w-sm', 'max-w-md', 'max-w-lg', 'max-w-xl', 'max-w-2xl', 'max-w-3xl', 'max-w-4xl', 'max-w-5xl', 'max-w-6xl', 'max-w-7xl'])
                ->description('Maximum width of container');

            $builder->string('padding')
                ->default($defaults['padding'] ?? 'px-4 sm:px-6 lg:px-8')
                ->description('Container padding');

            $builder->string('margin')
                ->default($defaults['margin'] ?? 'mx-auto')
                ->description('Container margin');

            $builder->group('content', $callback);
        });
    }

    public function card(string $name, callable $callback, array $defaults = []): Property
    {
        return $this->group($name, function (PropertyBuilder $builder) use ($callback, $defaults) {
            $builder->string('rounded')
                ->default($defaults['rounded'] ?? 'rounded-lg')
                ->description('Border radius');

            $builder->string('shadow')
                ->default($defaults['shadow'] ?? 'shadow-md')
                ->description('Box shadow');

            $builder->string('background')
                ->default($defaults['background'] ?? 'bg-white')
                ->description('Background color');

            $builder->string('border')
                ->default($defaults['border'] ?? 'border border-gray-200')
                ->description('Border style');

            $builder->string('padding')
                ->default($defaults['padding'] ?? 'p-4')
                ->description('Card padding');

            $builder->group('content', $callback);
        });
    }

    public function section(string $name, callable $callback, array $defaults = []): Property
    {
        return $this->group($name, function (PropertyBuilder $builder) use ($callback, $defaults) {
            $builder->string('spacing')
                ->default($defaults['spacing'] ?? 'py-12')
                ->description('Section spacing');

            $builder->string('background')
                ->default($defaults['background'] ?? 'bg-white')
                ->description('Background color');

            $builder->container('container', $callback, $defaults['container'] ?? []);
        });
    }

    public function sidebar(string $name, callable $main, callable $aside, array $defaults = []): Property
    {
        return $this->group($name, function (PropertyBuilder $builder) use ($main, $aside, $defaults) {
            $builder->string('layout')
                ->default($defaults['layout'] ?? 'lg:grid-cols-[1fr_16rem]')
                ->description('Sidebar layout configuration');

            $builder->string('gap')
                ->default($defaults['gap'] ?? 'gap-8')
                ->description('Gap between main and sidebar');

            $builder->group('main', $main);
            $builder->group('aside', $aside);
        });
    }

    public function divider(string $name, array $defaults = []): Property
    {
        return $this->group($name, function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('type')
                ->default($defaults['type'] ?? 'horizontal')
                ->enum(['horizontal', 'vertical'])
                ->description('Divider orientation');

            $builder->string('margin')
                ->default($defaults['margin'] ?? 'my-4')
                ->description('Divider margin');

            $builder->string('border')
                ->default($defaults['border'] ?? 'border-t border-gray-200')
                ->description('Border style');
        });
    }

    // Form Fields
    public function textField(string $name, ?string $label = null, ?string $placeholder = null): Property
    {
        $property = Property::string($name)
            ->default('')
            ->description($label ?? ucwords(str_replace('_', ' ', $name)));

        if ($placeholder) {
            $property->withBuilder(function (PropertyBuilder $builder) use ($placeholder) {
                $builder->string('placeholder')->default($placeholder);
            });
        }

        $this->properties[$name] = $property;
        return $property;
    }

    public function email(string $name, ?string $label = null): Property
    {
        return $this->textField($name, $label)
            ->pattern('^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$')
            ->description('Email address');
    }

    public function password(string $name, ?string $label = null): Property
    {
        return $this->textField($name, $label)
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->boolean('showPassword')
                    ->default(false)
                    ->description('Toggle password visibility');
                
                $builder->number('minLength')
                    ->default(8)
                    ->description('Minimum password length');

                $builder->boolean('requireSpecialChar')
                    ->default(true)
                    ->description('Require special character');

                $builder->boolean('requireNumber')
                    ->default(true)
                    ->description('Require number');

                $builder->boolean('requireUppercase')
                    ->default(true)
                    ->description('Require uppercase letter');
            })
            ->description('Password field');
    }

    public function phone(string $name, ?string $label = null): Property
    {
        return $this->textField($name, $label)
            ->pattern('^\+?[1-9]\d{1,14}$')
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('format')
                    ->default('international')
                    ->enum(['international', 'national'])
                    ->description('Phone number format');
                
                $builder->string('countryCode')
                    ->default('US')
                    ->description('Default country code');
            })
            ->description('Phone number field');
    }

    public function url(string $name, ?string $label = null): Property
    {
        return $this->textField($name, $label)
            ->pattern('^https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b(?:[-a-zA-Z0-9()@:%_\+.~#?&\/=]*)$')
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->boolean('requireHttps')
                    ->default(true)
                    ->description('Require HTTPS protocol');
            })
            ->description('URL field');
    }

    public function color(string $name, ?string $label = null): Property
    {
        return $this->textField($name, $label)
            ->pattern('^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$')
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('format')
                    ->default('hex')
                    ->enum(['hex', 'rgb', 'hsl'])
                    ->description('Color format');
                
                $builder->boolean('showPicker')
                    ->default(true)
                    ->description('Show color picker');

                $builder->array('presets')
                    ->default([])
                    ->description('Preset color options');
            })
            ->description('Color picker field');
    }

    public function file(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->array('accept')
                    ->default(['image/*'])
                    ->description('Accepted file types');

                $builder->number('maxSize')
                    ->default(5 * 1024 * 1024) // 5MB
                    ->description('Maximum file size in bytes');

                $builder->boolean('multiple')
                    ->default(false)
                    ->description('Allow multiple files');

                $builder->boolean('dragDrop')
                    ->default(true)
                    ->description('Enable drag and drop');

                $builder->string('uploadUrl')
                    ->required()
                    ->description('Upload endpoint URL');

                $builder->object('preview')
                    ->withBuilder(function (PropertyBuilder $preview) {
                        $preview->boolean('enabled')
                            ->default(true)
                            ->description('Show file preview');
                        
                        $preview->string('maxWidth')
                            ->default('200px')
                            ->description('Preview max width');
                        
                        $preview->string('maxHeight')
                            ->default('200px')
                            ->description('Preview max height');
                    });
            });
    }

    public function richText(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('value')
                    ->default('')
                    ->description('Rich text content');

                $builder->array('toolbar')
                    ->default(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                    ->description('Toolbar options');

                $builder->boolean('markdown')
                    ->default(false)
                    ->description('Enable markdown support');

                $builder->number('maxLength')
                    ->default(null)
                    ->description('Maximum content length');

                $builder->object('placeholder')
                    ->withBuilder(function (PropertyBuilder $placeholder) {
                        $placeholder->string('text')
                            ->default('Start typing...')
                            ->description('Placeholder text');
                        
                        $placeholder->string('color')
                            ->default('text-gray-400')
                            ->description('Placeholder color');
                    });
            });
    }

    public function range(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->number('value')
                    ->default(0)
                    ->description('Current value');

                $builder->number('min')
                    ->default(0)
                    ->description('Minimum value');

                $builder->number('max')
                    ->default(100)
                    ->description('Maximum value');

                $builder->number('step')
                    ->default(1)
                    ->description('Step increment');

                $builder->boolean('showTicks')
                    ->default(false)
                    ->description('Show tick marks');

                $builder->boolean('showValue')
                    ->default(true)
                    ->description('Show current value');

                $builder->object('marks')
                    ->withBuilder(function (PropertyBuilder $marks) {
                        $marks->array('values')
                            ->default([])
                            ->description('Custom mark values');
                        
                        $marks->string('color')
                            ->default('bg-blue-500')
                            ->description('Mark color');
                    });
            });
    }

    public function tags(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->array('value')
                    ->default([])
                    ->description('Selected tags');

                $builder->array('suggestions')
                    ->default([])
                    ->description('Tag suggestions');

                $builder->boolean('allowNew')
                    ->default(true)
                    ->description('Allow creating new tags');

                $builder->number('maxTags')
                    ->default(null)
                    ->description('Maximum number of tags');

                $builder->string('delimiter')
                    ->default(',')
                    ->description('Tag delimiter');

                $builder->object('style')
                    ->withBuilder(function (PropertyBuilder $style) {
                        $style->string('tag')
                            ->default('bg-blue-100 text-blue-800')
                            ->description('Tag style');
                        
                        $style->string('remove')
                            ->default('text-blue-500 hover:text-blue-700')
                            ->description('Remove button style');
                    });
            });
    }

    public function rating(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->number('value')
                    ->default(0)
                    ->description('Current rating');

                $builder->number('max')
                    ->default(5)
                    ->description('Maximum rating');

                $builder->boolean('allowHalf')
                    ->default(false)
                    ->description('Allow half ratings');

                $builder->boolean('readonly')
                    ->default(false)
                    ->description('Read-only mode');

                $builder->object('icon')
                    ->withBuilder(function (PropertyBuilder $icon) {
                        $icon->string('filled')
                            ->default('fas fa-star')
                            ->description('Filled star icon');
                        
                        $icon->string('empty')
                            ->default('far fa-star')
                            ->description('Empty star icon');
                        
                        $icon->string('color')
                            ->default('text-yellow-400')
                            ->description('Star color');
                    });
            });
    }

    // Data Handling
    public function id(string $name = 'id'): Property
    {
        return $this->string($name)
            ->required()
            ->description('Unique identifier');
    }

    public function foreignKey(string $name, string $references): Property
    {
        return $this->string($name)
            ->required()
            ->description("Foreign key reference to $references");
    }

    public function timestamp(string $name): Property
    {
        return $this->datetime($name)
            ->description('Timestamp field');
    }

    public function slug(string $name = 'slug'): Property
    {
        return $this->string($name)
            ->pattern('^[a-z0-9]+(?:-[a-z0-9]+)*$')
            ->description('URL-friendly slug');
    }

    public function json(string $name): Property
    {
        return $this->object($name)
            ->description('JSON data field');
    }

    // Layout Helpers
    public function group(string $name, callable $callback): Property
    {
        $property = Property::object($name);
        $property->withBuilder($callback);
        $this->properties[$name] = $property;
        return $property;
    }

    public function list(string $name, Property|callable $itemSchema): Property
    {
        $property = Property::array($name);
        
        if (is_callable($itemSchema)) {
            $builder = new static();
            $itemSchema($builder);
            $property->items($builder->toArray());
        } else {
            $property->items($itemSchema);
        }

        $this->properties[$name] = $property;
        return $property;
    }

    public function tabs(string $name, array $tabs): Property
    {
        return $this->group($name, function (PropertyBuilder $builder) use ($tabs) {
            $builder->string('activeTab')
                ->enum(array_keys($tabs))
                ->default(array_key_first($tabs));

            foreach ($tabs as $key => $tab) {
                $builder->group($key, $tab);
            }
        });
    }

    public function conditional(string $name, string $condition, callable $callback): Property
    {
        $property = Property::object($name);
        $property->withBuilder(function (PropertyBuilder $builder) use ($condition, $callback) {
            $builder->string('condition')->default($condition);
            $builder->group('content', $callback);
        });
        $this->properties[$name] = $property;
        return $property;
    }

    // Utility Methods
    public function merge(PropertyBuilder $other): static
    {
        foreach ($other->getProperties() as $name => $property) {
            $this->properties[$name] = $property;
        }
        return $this;
    }

    public function prefix(string $prefix): static
    {
        $properties = [];
        foreach ($this->properties as $name => $property) {
            $properties[$prefix . $name] = $property;
        }
        $this->properties = $properties;
        return $this;
    }

    public function toArray(): array
    {
        $schema = [];
        foreach ($this->properties as $name => $property) {
            $schema[$name] = $property->toArray();
        }
        return $schema;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function autocomplete(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('value')
                    ->default('')
                    ->description('Selected value');

                $builder->array('options')
                    ->default([])
                    ->description('Available options');

                $builder->string('searchUrl')
                    ->description('URL for remote search');

                $builder->number('minChars')
                    ->default(2)
                    ->description('Minimum characters before search');

                $builder->number('debounce')
                    ->default(300)
                    ->description('Debounce time in milliseconds');

                $builder->boolean('allowCustom')
                    ->default(false)
                    ->description('Allow custom values');

                $builder->object('display')
                    ->withBuilder(function (PropertyBuilder $display) {
                        $display->string('labelKey')
                            ->default('label')
                            ->description('Key for option label');

                        $display->string('valueKey')
                            ->default('value')
                            ->description('Key for option value');

                        $display->string('groupKey')
                            ->default('group')
                            ->description('Key for option grouping');
                    });
            });
    }

    public function dateRange(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('start')
                    ->nullable()
                    ->format('date')
                    ->description('Start date');

                $builder->string('end')
                    ->nullable()
                    ->format('date')
                    ->description('End date');

                $builder->array('presets')
                    ->default([
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'last7days' => 'Last 7 Days',
                        'last30days' => 'Last 30 Days',
                        'thisMonth' => 'This Month',
                        'lastMonth' => 'Last Month',
                    ])
                    ->description('Date range presets');

                $builder->boolean('allowSingleDate')
                    ->default(false)
                    ->description('Allow selecting single date');

                $builder->object('constraints')
                    ->withBuilder(function (PropertyBuilder $constraints) {
                        $constraints->string('minDate')
                            ->nullable()
                            ->description('Minimum selectable date');

                        $constraints->string('maxDate')
                            ->nullable()
                            ->description('Maximum selectable date');

                        $constraints->number('maxRange')
                            ->nullable()
                            ->description('Maximum date range in days');
                    });
            });
    }

    public function repeater(string $name, callable $itemSchema, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) use ($itemSchema) {
                $builder->array('items')
                    ->default([])
                    ->description('Repeater items');

                $subBuilder = new PropertyBuilder();
                $itemSchema($subBuilder);
                $builder->add(Property::object('itemSchema')->properties($subBuilder->toArray()));

                $builder->number('minItems')
                    ->default(0)
                    ->description('Minimum number of items');

                $builder->number('maxItems')
                    ->nullable()
                    ->description('Maximum number of items');

                $builder->boolean('sortable')
                    ->default(true)
                    ->description('Allow reordering items');

                $builder->object('labels')
                    ->withBuilder(function (PropertyBuilder $labels) {
                        $labels->string('add')
                            ->default('Add Item')
                            ->description('Add button label');

                        $labels->string('remove')
                            ->default('Remove')
                            ->description('Remove button label');
                    });
            });
    }

    public function transfer(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->array('selected')
                    ->default([])
                    ->description('Selected items');

                $builder->array('available')
                    ->default([])
                    ->description('Available items');

                $builder->boolean('searchable')
                    ->default(true)
                    ->description('Enable search');

                $builder->boolean('sortable')
                    ->default(true)
                    ->description('Allow reordering');

                $builder->object('display')
                    ->withBuilder(function (PropertyBuilder $display) {
                        $display->string('titleLeft')
                            ->default('Available')
                            ->description('Left panel title');

                        $display->string('titleRight')
                            ->default('Selected')
                            ->description('Right panel title');

                        $display->string('labelKey')
                            ->default('label')
                            ->description('Key for item label');

                        $display->string('valueKey')
                            ->default('value')
                            ->description('Key for item value');
                    });

                $builder->object('pagination')
                    ->withBuilder(function (PropertyBuilder $pagination) {
                        $pagination->boolean('enabled')
                            ->default(false)
                            ->description('Enable pagination');

                        $pagination->number('pageSize')
                            ->default(10)
                            ->description('Items per page');
                    });
            });
    }

    public function cascader(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->array('value')
                    ->default([])
                    ->description('Selected values');

                $builder->array('options')
                    ->default([])
                    ->description('Cascading options');

                $builder->boolean('multiple')
                    ->default(false)
                    ->description('Allow multiple selection');

                $builder->boolean('checkStrictly')
                    ->default(false)
                    ->description('Allow selecting parent nodes');

                $builder->string('expandTrigger')
                    ->default('click')
                    ->enum(['click', 'hover'])
                    ->description('Trigger mode for expanding nodes');

                $builder->object('display')
                    ->withBuilder(function (PropertyBuilder $display) {
                        $display->string('labelKey')
                            ->default('label')
                            ->description('Key for option label');

                        $display->string('valueKey')
                            ->default('value')
                            ->description('Key for option value');

                        $display->string('childrenKey')
                            ->default('children')
                            ->description('Key for child options');

                        $display->boolean('showIcon')
                            ->default(true)
                            ->description('Show node icons');

                        $display->boolean('showLine')
                            ->default(true)
                            ->description('Show connecting lines');

                        $display->string('expandIcon')
                            ->default('fas fa-chevron-right')
                            ->description('Icon for expandable nodes');
                    });

                $builder->boolean('searchable')
                    ->default(true)
                    ->description('Enable search');

                $builder->boolean('clearable')
                    ->default(true)
                    ->description('Show clear button');
            });
    }

    public function schedule(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->object('schedule')
                    ->withBuilder(function (PropertyBuilder $schedule) {
                        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
                            $schedule->array($day)->withBuilder(function (PropertyBuilder $slots) {
                                $slots->string('start')
                                    ->format('time')
                                    ->description('Start time');

                                $slots->string('end')
                                    ->format('time')
                                    ->description('End time');

                                $slots->boolean('enabled')
                                    ->default(true)
                                    ->description('Slot enabled');
                            });
                        }
                    });

                $builder->object('config')
                    ->withBuilder(function (PropertyBuilder $config) {
                        $config->number('interval')
                            ->default(30)
                            ->description('Time slot interval in minutes');

                        $config->string('minTime')
                            ->default('09:00')
                            ->description('Minimum time');

                        $config->string('maxTime')
                            ->default('17:00')
                            ->description('Maximum time');

                        $config->boolean('excludeHolidays')
                            ->default(true)
                            ->description('Exclude holidays');

                        $config->array('holidays')
                            ->default([])
                            ->description('Holiday dates');
                    });

                $builder->object('display')
                    ->withBuilder(function (PropertyBuilder $display) {
                        $display->string('timeFormat')
                            ->default('HH:mm')
                            ->description('Time display format');

                        $display->boolean('showWeekends')
                            ->default(true)
                            ->description('Show weekend slots');
                    });
            });
    }

    // Advanced Selection Fields
    public function treeSelect(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->array('value')
                    ->default([])
                    ->description('Selected values');

                $builder->array('options')
                    ->default([])
                    ->description('Tree options');

                $builder->boolean('multiple')
                    ->default(false)
                    ->description('Allow multiple selection');

                $builder->boolean('cascade')
                    ->default(true)
                    ->description('Enable cascading selection');

                $builder->boolean('checkStrictly')
                    ->default(false)
                    ->description('Allow selecting parent and children independently');

                $builder->object('display')
                    ->withBuilder(function (PropertyBuilder $display) {
                        $display->string('labelKey')
                            ->default('label')
                            ->description('Key for node label');

                        $display->string('valueKey')
                            ->default('value')
                            ->description('Key for node value');

                        $display->string('childrenKey')
                            ->default('children')
                            ->description('Key for child nodes');

                        $display->boolean('showIcon')
                            ->default(true)
                            ->description('Show node icons');

                        $display->boolean('showLine')
                            ->default(true)
                            ->description('Show connecting lines');

                        $display->string('expandIcon')
                            ->default('fas fa-chevron-right')
                            ->description('Icon for expandable nodes');
                    });

                $builder->boolean('searchable')
                    ->default(true)
                    ->description('Enable search');

                $builder->boolean('expandOnSearch')
                    ->default(true)
                    ->description('Auto-expand nodes on search');
            });
    }

    public function multiSelect(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->array('value')
                    ->default([])
                    ->description('Selected values');

                $builder->array('options')
                    ->default([])
                    ->description('Available options');

                $builder->number('maxItems')
                    ->nullable()
                    ->description('Maximum number of selections');

                $builder->boolean('searchable')
                    ->default(true)
                    ->description('Enable search');

                $builder->boolean('creatable')
                    ->default(false)
                    ->description('Allow creating new options');

                $builder->object('display')
                    ->withBuilder(function (PropertyBuilder $display) {
                        $display->string('labelKey')
                            ->default('label')
                            ->description('Key for option label');

                        $display->string('valueKey')
                            ->default('value')
                            ->description('Key for option value');

                        $display->string('groupKey')
                            ->default('group')
                            ->description('Key for option grouping');

                        $display->string('chipStyle')
                            ->default('bg-blue-100 text-blue-800')
                            ->description('Style for selected chips');

                        $display->string('removeIcon')
                            ->default('fas fa-times')
                            ->description('Icon for removing chips');
                    });

                $builder->object('validation')
                    ->withBuilder(function (PropertyBuilder $validation) {
                        $validation->array('required')
                            ->default([])
                            ->description('Values that must be selected');

                        $validation->array('disabled')
                            ->default([])
                            ->description('Values that cannot be selected');
                    });
            });
    }

    public function combobox(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('value')
                    ->default('')
                    ->description('Current value');

                $builder->array('options')
                    ->default([])
                    ->description('Predefined options');

                $builder->boolean('allowCustom')
                    ->default(true)
                    ->description('Allow custom values');

                $builder->boolean('searchable')
                    ->default(true)
                    ->description('Enable search');

                $builder->string('mode')
                    ->default('suggest')
                    ->enum(['suggest', 'complete'])
                    ->description('Input mode (suggest shows options, complete tries to complete input)');

                $builder->object('display')
                    ->withBuilder(function (PropertyBuilder $display) {
                        $display->string('labelKey')
                            ->default('label')
                            ->description('Key for option label');

                        $display->string('valueKey')
                            ->default('value')
                            ->description('Key for option value');

                        $display->string('groupKey')
                            ->default('group')
                            ->description('Key for option grouping');

                        $display->boolean('showIcon')
                            ->default(true)
                            ->description('Show option icons');
                    });

                $builder->object('validation')
                    ->withBuilder(function (PropertyBuilder $validation) {
                        $validation->string('pattern')
                            ->nullable()
                            ->description('Pattern for custom values');

                        $validation->array('allowedValues')
                            ->default([])
                            ->description('List of allowed custom values');
                    });
            });
    }

    // Code/Technical Fields
    public function codeEditor(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('value')
                    ->default('')
                    ->description('Code content');

                $builder->string('language')
                    ->default('javascript')
                    ->enum([
                        'javascript', 'typescript', 'php', 'python', 'ruby', 'java',
                        'html', 'css', 'scss', 'json', 'xml', 'yaml', 'markdown',
                        'sql', 'shell', 'plaintext'
                    ])
                    ->description('Programming language');

                $builder->object('editor')
                    ->withBuilder(function (PropertyBuilder $editor) {
                        $editor->string('theme')
                            ->default('vs')
                            ->enum(['vs', 'vs-dark', 'hc-black'])
                            ->description('Editor theme');

                        $editor->boolean('minimap')
                            ->default(false)
                            ->description('Show minimap');

                        $editor->boolean('lineNumbers')
                            ->default(true)
                            ->description('Show line numbers');

                        $editor->boolean('wordWrap')
                            ->default(false)
                            ->description('Enable word wrapping');

                        $editor->number('fontSize')
                            ->default(14)
                            ->description('Font size in pixels');

                        $editor->number('tabSize')
                            ->default(2)
                            ->description('Tab size');

                        $editor->boolean('readOnly')
                            ->default(false)
                            ->description('Read-only mode');
                    });

                $builder->object('lint')
                    ->withBuilder(function (PropertyBuilder $lint) {
                        $lint->boolean('enabled')
                            ->default(true)
                            ->description('Enable linting');

                        $lint->object('rules')
                            ->description('Linting rules configuration');

                        $lint->boolean('formatOnPaste')
                            ->default(true)
                            ->description('Format code on paste');

                        $lint->boolean('formatOnType')
                            ->default(false)
                            ->description('Format code while typing');
                    });

                $builder->object('autocomplete')
                    ->withBuilder(function (PropertyBuilder $autocomplete) {
                        $autocomplete->boolean('enabled')
                            ->default(true)
                            ->description('Enable autocompletion');

                        $autocomplete->array('snippets')
                            ->default([])
                            ->description('Custom code snippets');

                        $autocomplete->boolean('suggestOnTriggerCharacters')
                            ->default(true)
                            ->description('Show suggestions on trigger characters');
                    });
            });
    }

    public function jsonEditor(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->object('value')
                    ->default(new \stdClass)
                    ->description('JSON content');

                $builder->object('schema')
                    ->nullable()
                    ->description('JSON schema for validation');

                $builder->object('editor')
                    ->withBuilder(function (PropertyBuilder $editor) {
                        $editor->string('mode')
                            ->default('tree')
                            ->enum(['tree', 'code', 'form', 'text'])
                            ->description('Editor mode');

                        $editor->boolean('search')
                            ->default(true)
                            ->description('Enable search');

                        $editor->boolean('history')
                            ->default(true)
                            ->description('Enable undo/redo');

                        $editor->boolean('navigationBar')
                            ->default(true)
                            ->description('Show navigation bar');

                        $editor->boolean('statusBar')
                            ->default(true)
                            ->description('Show status bar');

                        $editor->boolean('mainMenuBar')
                            ->default(true)
                            ->description('Show main menu bar');
                    });

                $builder->object('validation')
                    ->withBuilder(function (PropertyBuilder $validation) {
                        $validation->boolean('enabled')
                            ->default(true)
                            ->description('Enable validation');

                        $validation->boolean('validateOnChange')
                            ->default(true)
                            ->description('Validate while typing');
                    });
            });
    }

    public function markdown(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('value')
                    ->default('')
                    ->description('Markdown content');

                $builder->object('editor')
                    ->withBuilder(function (PropertyBuilder $editor) {
                        $editor->boolean('preview')
                            ->default(true)
                            ->description('Show preview panel');

                        $editor->string('previewPosition')
                            ->default('right')
                            ->enum(['right', 'bottom'])
                            ->description('Preview panel position');

                        $editor->array('toolbar')
                            ->default([
                                'bold', 'italic', 'heading', '|',
                                'quote', 'code', 'link', '|',
                                'bulletList', 'orderedList', '|',
                                'image', 'table'
                            ])
                            ->description('Toolbar items');

                        $editor->boolean('spellcheck')
                            ->default(true)
                            ->description('Enable spellcheck');

                        $editor->boolean('lineNumbers')
                            ->default(true)
                            ->description('Show line numbers');

                        $editor->string('theme')
                            ->default('light')
                            ->enum(['light', 'dark'])
                            ->description('Editor theme');
                    });

                $builder->object('preview')
                    ->withBuilder(function (PropertyBuilder $preview) {
                        $preview->boolean('syncScroll')
                            ->default(true)
                            ->description('Sync scroll position');

                        $preview->string('sanitize')
                            ->default('strict')
                            ->enum(['strict', 'relaxed', 'none'])
                            ->description('HTML sanitization level');

                        $preview->object('highlight')
                            ->withBuilder(function (PropertyBuilder $highlight) {
                                $highlight->boolean('enabled')
                                    ->default(true)
                                    ->description('Enable syntax highlighting');

                                $highlight->string('theme')
                                    ->default('github')
                                    ->description('Highlight.js theme');
                            });
                    });
            });
    }

    // Media Fields
    public function imageUpload(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('value')
                    ->nullable()
                    ->description('Image URL or path');

                $builder->object('upload')
                    ->withBuilder(function (PropertyBuilder $upload) {
                        $upload->array('accept')
                            ->default(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->description('Accepted file types');

                        $upload->number('maxSize')
                            ->default(5 * 1024 * 1024) // 5MB
                            ->description('Maximum file size in bytes');

                        $upload->boolean('multiple')
                            ->default(false)
                            ->description('Allow multiple uploads');

                        $upload->string('endpoint')
                            ->nullable()
                            ->description('Upload endpoint URL');
                    });

                $builder->object('crop')
                    ->withBuilder(function (PropertyBuilder $crop) {
                        $crop->boolean('enabled')
                            ->default(true)
                            ->description('Enable image cropping');

                        $crop->number('aspectRatio')
                            ->nullable()
                            ->description('Fixed aspect ratio (width/height)');

                        $crop->object('size')
                            ->withBuilder(function (PropertyBuilder $size) {
                                $size->number('width')
                                    ->nullable()
                                    ->description('Target width in pixels');

                                $size->number('height')
                                    ->nullable()
                                    ->description('Target height in pixels');

                                $size->boolean('maintain')
                                    ->default(true)
                                    ->description('Maintain aspect ratio');
                            });
                    });

                $builder->object('preview')
                    ->withBuilder(function (PropertyBuilder $preview) {
                        $preview->boolean('enabled')
                            ->default(true)
                            ->description('Show image preview');

                        $preview->number('maxWidth')
                            ->default(200)
                            ->description('Preview max width');

                        $preview->number('maxHeight')
                            ->default(200)
                            ->description('Preview max height');
                    });
            });
    }

    public function mediaGallery(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->array('value')
                    ->default([])
                    ->description('Selected media items');

                $builder->object('gallery')
                    ->withBuilder(function (PropertyBuilder $gallery) {
                        $gallery->array('accept')
                            ->default(['image/*', 'video/*', 'audio/*'])
                            ->description('Accepted file types');

                        $gallery->number('maxItems')
                            ->nullable()
                            ->description('Maximum number of items');

                        $gallery->number('maxSize')
                            ->default(10 * 1024 * 1024) // 10MB
                            ->description('Maximum file size per item');

                        $gallery->boolean('sortable')
                            ->default(true)
                            ->description('Allow reordering');
                    });

                $builder->object('display')
                    ->withBuilder(function (PropertyBuilder $display) {
                        $display->string('view')
                            ->default('grid')
                            ->enum(['grid', 'list', 'masonry'])
                            ->description('Gallery view mode');

                        $display->number('columns')
                            ->default(4)
                            ->description('Number of columns in grid view');

                        $display->boolean('showFilename')
                            ->default(true)
                            ->description('Show file names');

                        $display->boolean('showFilesize')
                            ->default(true)
                            ->description('Show file sizes');
                    });

                $builder->object('metadata')
                    ->withBuilder(function (PropertyBuilder $metadata) {
                        $metadata->boolean('enabled')
                            ->default(true)
                            ->description('Enable metadata editing');

                        $metadata->array('fields')
                            ->default(['title', 'alt', 'description'])
                            ->description('Editable metadata fields');
                    });
            });
    }

    public function avatar(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('value')
                    ->nullable()
                    ->description('Avatar URL or path');

                $builder->object('upload')
                    ->withBuilder(function (PropertyBuilder $upload) {
                        $upload->array('accept')
                            ->default(['image/jpeg', 'image/png'])
                            ->description('Accepted file types');

                        $upload->number('maxSize')
                            ->default(2 * 1024 * 1024) // 2MB
                            ->description('Maximum file size');

                        $upload->string('endpoint')
                            ->nullable()
                            ->description('Upload endpoint URL');
                    });

                $builder->object('crop')
                    ->withBuilder(function (PropertyBuilder $crop) {
                        $crop->boolean('enabled')
                            ->default(true)
                            ->description('Enable cropping');

                        $crop->boolean('circular')
                            ->default(true)
                            ->description('Force circular crop');

                        $crop->number('size')
                            ->default(200)
                            ->description('Target size in pixels');
                    });

                $builder->object('preview')
                    ->withBuilder(function (PropertyBuilder $preview) {
                        $preview->string('placeholder')
                            ->default('fas fa-user')
                            ->description('Placeholder icon');

                        $preview->string('background')
                            ->default('bg-gray-200')
                            ->description('Placeholder background');

                        $preview->string('size')
                            ->default('md')
                            ->enum(['sm', 'md', 'lg', 'xl'])
                            ->description('Preview size');
                    });
            });
    }

    // Specialized Input Fields
    public function currency(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->number('value')
                    ->nullable()
                    ->description('Amount value');

                $builder->string('currency')
                    ->default('USD')
                    ->description('Currency code');

                $builder->object('format')
                    ->withBuilder(function (PropertyBuilder $format) {
                        $format->string('locale')
                            ->default('en-US')
                            ->description('Formatting locale');

                        $format->boolean('showSymbol')
                            ->default(true)
                            ->description('Show currency symbol');

                        $format->number('precision')
                            ->default(2)
                            ->description('Decimal precision');

                        $format->boolean('showSeparators')
                            ->default(true)
                            ->description('Show thousand separators');
                    });

                $builder->object('validation')
                    ->withBuilder(function (PropertyBuilder $validation) {
                        $validation->number('min')
                            ->nullable()
                            ->description('Minimum value');

                        $validation->number('max')
                            ->nullable()
                            ->description('Maximum value');

                        $validation->array('allowedCurrencies')
                            ->default(['USD', 'EUR', 'GBP'])
                            ->description('Allowed currency codes');
                    });
            });
    }

    public function percentage(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->number('value')
                    ->nullable()
                    ->description('Percentage value');

                $builder->object('format')
                    ->withBuilder(function (PropertyBuilder $format) {
                        $format->number('precision')
                            ->default(1)
                            ->description('Decimal precision');

                        $format->boolean('showSymbol')
                            ->default(true)
                            ->description('Show % symbol');
                    });

                $builder->object('validation')
                    ->withBuilder(function (PropertyBuilder $validation) {
                        $validation->number('min')
                            ->default(0)
                            ->description('Minimum value');

                        $validation->number('max')
                            ->default(100)
                            ->description('Maximum value');

                        $validation->number('step')
                            ->default(0.1)
                            ->description('Step increment');
                    });

                $builder->object('display')
                    ->withBuilder(function (PropertyBuilder $display) {
                        $display->boolean('slider')
                            ->default(false)
                            ->description('Show as slider');

                        $display->boolean('colorScale')
                            ->default(false)
                            ->description('Show color scale');
                    });
            });
    }

    public function mask(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('value')
                    ->nullable()
                    ->description('Input value');

                $builder->string('mask')
                    ->description('Input mask pattern');

                $builder->object('options')
                    ->withBuilder(function (PropertyBuilder $options) {
                        $options->string('placeholder')
                            ->default('_')
                            ->description('Placeholder character');

                        $options->boolean('guide')
                            ->default(true)
                            ->description('Show guide overlay');

                        $options->boolean('keepCharPositions')
                            ->default(false)
                            ->description('Keep character positions');

                        $options->object('patterns')
                            ->withBuilder(function (PropertyBuilder $patterns) {
                                $patterns->string('9')
                                    ->default('[0-9]')
                                    ->description('Numeric pattern');

                                $patterns->string('a')
                                    ->default('[a-zA-Z]')
                                    ->description('Alpha pattern');

                                $patterns->string('*')
                                    ->default('[a-zA-Z0-9]')
                                    ->description('Alphanumeric pattern');
                            });
                    });

                $builder->object('presets')
                    ->withBuilder(function (PropertyBuilder $presets) {
                        $presets->string('phone')
                            ->default('(999) 999-9999')
                            ->description('Phone mask');

                        $presets->string('creditCard')
                            ->default('9999 9999 9999 9999')
                            ->description('Credit card mask');

                        $presets->string('date')
                            ->default('99/99/9999')
                            ->description('Date mask');

                        $presets->string('ssn')
                            ->default('999-99-9999')
                            ->description('SSN mask');
                    });
            });
    }

    // Location Fields
    public function address(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->object('value')
                    ->withBuilder(function (PropertyBuilder $value) {
                        $value->string('street1')
                            ->nullable()
                            ->description('Street address line 1');

                        $value->string('street2')
                            ->nullable()
                            ->description('Street address line 2');

                        $value->string('city')
                            ->nullable()
                            ->description('City');

                        $value->string('state')
                            ->nullable()
                            ->description('State/Province/Region');

                        $value->string('postalCode')
                            ->nullable()
                            ->description('Postal/ZIP code');

                        $value->string('country')
                            ->nullable()
                            ->description('Country');
                    });

                $builder->object('format')
                    ->withBuilder(function (PropertyBuilder $format) {
                        $format->string('layout')
                            ->default('standard')
                            ->enum(['standard', 'compact', 'international'])
                            ->description('Address layout format');

                        $format->string('countryDisplay')
                            ->default('full')
                            ->enum(['full', 'code'])
                            ->description('Country display format');

                        $format->boolean('uppercase')
                            ->default(false)
                            ->description('Convert to uppercase');
                    });

                $builder->object('validation')
                    ->withBuilder(function (PropertyBuilder $validation) {
                        $validation->array('requiredFields')
                            ->default(['street1', 'city', 'country'])
                            ->description('Required address fields');

                        $validation->boolean('validatePostalCode')
                            ->default(true)
                            ->description('Validate postal code format');

                        $validation->array('allowedCountries')
                            ->nullable()
                            ->description('List of allowed countries');
                    });

                $builder->object('autocomplete')
                    ->withBuilder(function (PropertyBuilder $autocomplete) {
                        $autocomplete->boolean('enabled')
                            ->default(true)
                            ->description('Enable address autocomplete');

                        $autocomplete->string('provider')
                            ->default('google')
                            ->enum(['google', 'mapbox', 'here'])
                            ->description('Autocomplete provider');

                        $autocomplete->string('apiKey')
                            ->nullable()
                            ->description('Provider API key');

                        $autocomplete->array('types')
                            ->default(['address'])
                            ->description('Place types to search');
                    });
            });
    }

    public function coordinates(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->object('value')
                    ->withBuilder(function (PropertyBuilder $value) {
                        $value->number('latitude')
                            ->nullable()
                            ->description('Latitude');

                        $value->number('longitude')
                            ->nullable()
                            ->description('Longitude');

                        $value->number('altitude')
                            ->nullable()
                            ->description('Altitude in meters');

                        $value->number('accuracy')
                            ->nullable()
                            ->description('Accuracy in meters');
                    });

                $builder->object('format')
                    ->withBuilder(function (PropertyBuilder $format) {
                        $format->string('display')
                            ->default('decimal')
                            ->enum(['decimal', 'dms'])
                            ->description('Coordinate display format');

                        $format->number('precision')
                            ->default(6)
                            ->description('Decimal precision');

                        $format->boolean('showLabels')
                            ->default(true)
                            ->description('Show coordinate labels');
                    });

                $builder->object('validation')
                    ->withBuilder(function (PropertyBuilder $validation) {
                        $validation->object('bounds')
                            ->withBuilder(function (PropertyBuilder $bounds) {
                                $bounds->number('minLat')
                                    ->default(-90)
                                    ->description('Minimum latitude');

                                $bounds->number('maxLat')
                                    ->default(90)
                                    ->description('Maximum latitude');

                                $bounds->number('minLng')
                                    ->default(-180)
                                    ->description('Minimum longitude');

                                $bounds->number('maxLng')
                                    ->default(180)
                                    ->description('Maximum longitude');
                            });
                    });

                $builder->object('geolocation')
                    ->withBuilder(function (PropertyBuilder $geolocation) {
                        $geolocation->boolean('enabled')
                            ->default(true)
                            ->description('Enable geolocation');

                        $geolocation->boolean('watchPosition')
                            ->default(false)
                            ->description('Watch position changes');

                        $geolocation->number('timeout')
                            ->default(10000)
                            ->description('Geolocation timeout in milliseconds');
                    });
            });
    }

    public function map(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->object('value')
                    ->withBuilder(function (PropertyBuilder $value) {
                        $value->object('center')
                            ->withBuilder(function (PropertyBuilder $center) {
                                $center->number('lat')
                                    ->nullable()
                                    ->description('Center latitude');

                                $center->number('lng')
                                    ->nullable()
                                    ->description('Center longitude');
                            });

                        $value->number('zoom')
                            ->default(13)
                            ->description('Zoom level');

                        $value->array('markers')
                            ->default([])
                            ->description('Map markers');

                        $value->array('polygons')
                            ->default([])
                            ->description('Map polygons');
                    });

                $builder->object('provider')
                    ->withBuilder(function (PropertyBuilder $provider) {
                        $provider->string('name')
                            ->default('leaflet')
                            ->enum(['leaflet', 'google', 'mapbox'])
                            ->description('Map provider');

                        $provider->string('apiKey')
                            ->nullable()
                            ->description('Provider API key');

                        $provider->string('style')
                            ->default('streets')
                            ->description('Map style');
                    });

                $builder->object('controls')
                    ->withBuilder(function (PropertyBuilder $controls) {
                        $controls->boolean('zoom')
                            ->default(true)
                            ->description('Show zoom controls');

                        $controls->boolean('fullscreen')
                            ->default(true)
                            ->description('Show fullscreen control');

                        $controls->boolean('search')
                            ->default(true)
                            ->description('Show search control');

                        $controls->boolean('locate')
                            ->default(true)
                            ->description('Show locate control');

                        $controls->boolean('draw')
                            ->default(false)
                            ->description('Show draw controls');
                    });

                $builder->object('interaction')
                    ->withBuilder(function (PropertyBuilder $interaction) {
                        $interaction->boolean('dragging')
                            ->default(true)
                            ->description('Enable map dragging');

                        $interaction->boolean('touchZoom')
                            ->default(true)
                            ->description('Enable touch zoom');

                        $interaction->boolean('scrollWheelZoom')
                            ->default(true)
                            ->description('Enable scroll wheel zoom');

                        $interaction->boolean('doubleClickZoom')
                            ->default(true)
                            ->description('Enable double click zoom');
                    });

                $builder->object('display')
                    ->withBuilder(function (PropertyBuilder $display) {
                        $display->number('height')
                            ->default(400)
                            ->description('Map height in pixels');

                        $display->string('width')
                            ->default('100%')
                            ->description('Map width');

                        $display->boolean('responsive')
                            ->default(true)
                            ->description('Make map responsive');
                    });
            });
    }

    // Time-Related Fields
    public function time(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('value')
                    ->nullable()
                    ->description('Time value');

                $builder->object('format')
                    ->withBuilder(function (PropertyBuilder $format) {
                        $format->string('display')
                            ->default('HH:mm')
                            ->description('Time display format');

                        $format->boolean('use24Hours')
                            ->default(true)
                            ->description('Use 24-hour format');

                        $format->string('timezone')
                            ->default('local')
                            ->description('Time zone');
                    });

                $builder->object('picker')
                    ->withBuilder(function (PropertyBuilder $picker) {
                        $picker->boolean('enabled')
                            ->default(true)
                            ->description('Enable time picker');

                        $picker->number('minuteStep')
                            ->default(15)
                            ->description('Minute step interval');

                        $picker->number('secondStep')
                            ->default(30)
                            ->description('Second step interval');

                        $picker->boolean('showSeconds')
                            ->default(false)
                            ->description('Show seconds');

                        $picker->boolean('showMeridiem')
                            ->default(false)
                            ->description('Show AM/PM selector');
                    });

                $builder->object('validation')
                    ->withBuilder(function (PropertyBuilder $validation) {
                        $validation->string('min')
                            ->nullable()
                            ->description('Minimum allowed time');

                        $validation->string('max')
                            ->nullable()
                            ->description('Maximum allowed time');

                        $validation->array('disabledTimes')
                            ->default([])
                            ->description('Disabled time slots');
                    });

                $builder->object('display')
                    ->withBuilder(function (PropertyBuilder $display) {
                        $display->boolean('clearable')
                            ->default(true)
                            ->description('Show clear button');

                        $display->string('placement')
                            ->default('bottom-start')
                            ->enum(['top-start', 'top', 'top-end', 'bottom-start', 'bottom', 'bottom-end'])
                            ->description('Picker placement');

                        $display->string('size')
                            ->default('md')
                            ->enum(['sm', 'md', 'lg'])
                            ->description('Input size');
                    });
            });
    }

    public function timeRange(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->object('value')
                    ->withBuilder(function (PropertyBuilder $value) {
                        $value->string('start')
                            ->nullable()
                            ->description('Start time');

                        $value->string('end')
                            ->nullable()
                            ->description('End time');
                    });

                $builder->object('format')
                    ->withBuilder(function (PropertyBuilder $format) {
                        $format->string('display')
                            ->default('HH:mm')
                            ->description('Time display format');

                        $format->boolean('use24Hours')
                            ->default(true)
                            ->description('Use 24-hour format');

                        $format->string('timezone')
                            ->default('local')
                            ->description('Time zone');

                        $format->string('separator')
                            ->default(' - ')
                            ->description('Range separator');
                    });

                $builder->object('picker')
                    ->withBuilder(function (PropertyBuilder $picker) {
                        $picker->number('minuteStep')
                            ->default(15)
                            ->description('Minute step interval');

                        $picker->boolean('showSeconds')
                            ->default(false)
                            ->description('Show seconds');

                        $picker->boolean('linkedPickers')
                            ->default(true)
                            ->description('Link start/end pickers');
                    });

                $builder->object('validation')
                    ->withBuilder(function (PropertyBuilder $validation) {
                        $validation->number('minDuration')
                            ->nullable()
                            ->description('Minimum duration in minutes');

                        $validation->number('maxDuration')
                            ->nullable()
                            ->description('Maximum duration in minutes');

                        $validation->array('disabledTimes')
                            ->default([])
                            ->description('Disabled time slots');
                    });

                $builder->object('presets')
                    ->withBuilder(function (PropertyBuilder $presets) {
                        $presets->boolean('enabled')
                            ->default(true)
                            ->description('Show time range presets');

                        $presets->array('options')
                            ->default([
                                'morning' => ['09:00', '12:00'],
                                'afternoon' => ['13:00', '17:00'],
                                'evening' => ['18:00', '22:00']
                            ])
                            ->description('Preset options');
                    });
            });
    }

    public function duration(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->number('value')
                    ->nullable()
                    ->description('Duration value in seconds');

                $builder->object('format')
                    ->withBuilder(function (PropertyBuilder $format) {
                        $format->string('display')
                            ->default('human')
                            ->enum(['human', 'digital', 'compact'])
                            ->description('Duration display format');

                        $format->array('units')
                            ->default(['hours', 'minutes', 'seconds'])
                            ->description('Displayed time units');

                        $format->boolean('showZero')
                            ->default(false)
                            ->description('Show zero units');

                        $format->object('labels')
                            ->withBuilder(function (PropertyBuilder $labels) {
                                $labels->string('hours')
                                    ->default('h')
                                    ->description('Hours label');

                                $labels->string('minutes')
                                    ->default('m')
                                    ->description('Minutes label');

                                $labels->string('seconds')
                                    ->default('s')
                                    ->description('Seconds label');
                            });
                    });

                $builder->object('input')
                    ->withBuilder(function (PropertyBuilder $input) {
                        $input->string('mode')
                            ->default('single')
                            ->enum(['single', 'multi', 'slider'])
                            ->description('Input mode');

                        $input->string('baseUnit')
                            ->default('seconds')
                            ->enum(['hours', 'minutes', 'seconds'])
                            ->description('Base unit for single input');

                        $input->boolean('allowDecimals')
                            ->default(false)
                            ->description('Allow decimal values');
                    });

                $builder->object('validation')
                    ->withBuilder(function (PropertyBuilder $validation) {
                        $validation->number('min')
                            ->nullable()
                            ->description('Minimum duration in seconds');

                        $validation->number('max')
                            ->nullable()
                            ->description('Maximum duration in seconds');

                        $validation->number('step')
                            ->default(60)
                            ->description('Step interval in seconds');
                    });

                $builder->object('display')
                    ->withBuilder(function (PropertyBuilder $display) {
                        $display->boolean('editable')
                            ->default(true)
                            ->description('Allow manual editing');

                        $display->boolean('showControls')
                            ->default(true)
                            ->description('Show increment/decrement controls');

                        $display->string('size')
                            ->default('md')
                            ->enum(['sm', 'md', 'lg'])
                            ->description('Input size');
                    });
            });
    }

    // Authentication Fields
    public function otp(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('value')
                    ->nullable()
                    ->description('OTP value');

                $builder->object('config')
                    ->withBuilder(function (PropertyBuilder $config) {
                        $config->string('type')
                            ->default('numeric')
                            ->enum(['numeric', 'alphanumeric'])
                            ->description('OTP type');

                        $config->number('length')
                            ->default(6)
                            ->description('OTP length');

                        $config->number('expiryTime')
                            ->default(300)
                            ->description('Expiry time in seconds');

                        $config->boolean('caseSensitive')
                            ->default(false)
                            ->description('Case-sensitive verification');
                    });

                $builder->object('delivery')
                    ->withBuilder(function (PropertyBuilder $delivery) {
                        $delivery->string('method')
                            ->default('email')
                            ->enum(['email', 'sms', 'authenticator'])
                            ->description('OTP delivery method');

                        $delivery->string('recipient')
                            ->nullable()
                            ->description('Delivery recipient');

                        $delivery->object('template')
                            ->withBuilder(function (PropertyBuilder $template) {
                                $template->string('subject')
                                    ->nullable()
                                    ->description('Message subject');

                                $template->string('body')
                                    ->nullable()
                                    ->description('Message body template');
                            });
                    });

                $builder->object('input')
                    ->withBuilder(function (PropertyBuilder $input) {
                        $input->string('mode')
                            ->default('single')
                            ->enum(['single', 'segmented'])
                            ->description('Input mode');

                        $input->boolean('autofocus')
                            ->default(true)
                            ->description('Autofocus first field');

                        $input->boolean('autosubmit')
                            ->default(true)
                            ->description('Submit on complete');

                        $input->number('debounce')
                            ->default(300)
                            ->description('Validation debounce time');
                    });

                $builder->object('retry')
                    ->withBuilder(function (PropertyBuilder $retry) {
                        $retry->boolean('enabled')
                            ->default(true)
                            ->description('Allow retry');

                        $retry->number('maxAttempts')
                            ->default(3)
                            ->description('Maximum retry attempts');

                        $retry->number('cooldown')
                            ->default(60)
                            ->description('Retry cooldown in seconds');
                    });
            });
    }

    public function captcha(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('value')
                    ->nullable()
                    ->description('CAPTCHA response');

                $builder->object('provider')
                    ->withBuilder(function (PropertyBuilder $provider) {
                        $provider->string('type')
                            ->default('recaptcha')
                            ->enum(['recaptcha', 'hcaptcha', 'turnstile'])
                            ->description('CAPTCHA provider');

                        $provider->string('siteKey')
                            ->nullable()
                            ->description('Provider site key');

                        $provider->string('secretKey')
                            ->nullable()
                            ->description('Provider secret key');

                        $provider->string('version')
                            ->default('v2')
                            ->enum(['v2', 'v3'])
                            ->description('Provider API version');
                    });

                $builder->object('appearance')
                    ->withBuilder(function (PropertyBuilder $appearance) {
                        $appearance->string('theme')
                            ->default('light')
                            ->enum(['light', 'dark'])
                            ->description('Widget theme');

                        $appearance->string('size')
                            ->default('normal')
                            ->enum(['normal', 'compact'])
                            ->description('Widget size');

                        $appearance->string('position')
                            ->default('bottomright')
                            ->enum(['bottomright', 'bottomleft', 'inline'])
                            ->description('Widget position');
                    });

                $builder->object('behavior')
                    ->withBuilder(function (PropertyBuilder $behavior) {
                        $behavior->boolean('hideOnSuccess')
                            ->default(true)
                            ->description('Hide after success');

                        $behavior->boolean('resetOnError')
                            ->default(true)
                            ->description('Reset on error');

                        $behavior->number('timeout')
                            ->default(120)
                            ->description('Response timeout');
                    });
            });
    }

    public function mfa(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->array('methods')
                    ->default([])
                    ->description('Available MFA methods');

                $builder->object('totp')
                    ->withBuilder(function (PropertyBuilder $totp) {
                        $totp->string('secret')
                            ->nullable()
                            ->description('TOTP secret key');

                        $totp->string('algorithm')
                            ->default('SHA1')
                            ->enum(['SHA1', 'SHA256', 'SHA512'])
                            ->description('TOTP algorithm');

                        $totp->number('digits')
                            ->default(6)
                            ->description('Code length');

                        $totp->number('period')
                            ->default(30)
                            ->description('Code validity period');

                        $totp->object('qrcode')
                            ->withBuilder(function (PropertyBuilder $qrcode) {
                                $qrcode->string('issuer')
                                    ->nullable()
                                    ->description('QR code issuer');

                                $qrcode->number('size')
                                    ->default(200)
                                    ->description('QR code size');
                            });
                    });

                $builder->object('backup')
                    ->withBuilder(function (PropertyBuilder $backup) {
                        $backup->number('codeCount')
                            ->default(10)
                            ->description('Number of backup codes');

                        $backup->number('codeLength')
                            ->default(8)
                            ->description('Backup code length');

                        $backup->boolean('singleUse')
                            ->default(true)
                            ->description('Single-use backup codes');
                    });

                $builder->object('recovery')
                    ->withBuilder(function (PropertyBuilder $recovery) {
                        $recovery->array('questions')
                            ->default([])
                            ->description('Recovery questions');

                        $recovery->number('requiredAnswers')
                            ->default(2)
                            ->description('Required correct answers');

                        $recovery->boolean('caseSensitive')
                            ->default(false)
                            ->description('Case-sensitive answers');
                    });

                $builder->object('session')
                    ->withBuilder(function (PropertyBuilder $session) {
                        $session->boolean('rememberDevice')
                            ->default(true)
                            ->description('Allow remember device');

                        $session->number('trustDuration')
                            ->default(30)
                            ->description('Device trust duration in days');

                        $session->boolean('requireReauth')
                            ->default(true)
                            ->description('Require periodic reauth');
                    });
            });
    }

    // Analytics Components
    public function chart(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)));
    }

    public function table(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->array('columns')
                    ->description('Table column definitions');

                $builder->array('rows')
                    ->description('Table row data');

                $builder->object('sorting')
                    ->nullable()
                    ->description('Table sorting configuration');

                $builder->object('pagination')
                    ->nullable()
                    ->description('Table pagination configuration');
            });
    }

    public function dynamicForm(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->array('metrics')
                    ->description('Available metrics for reporting')
                    ->required();

                $builder->array('dimensions')
                    ->description('Available dimensions for reporting')
                    ->required();

                $builder->object('filters')
                    ->nullable()
                    ->description('Filter configuration options');
            });
    }

    public function matrix(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function (PropertyBuilder $builder) {
                $builder->string('widget_id')
                    ->required()
                    ->description('Widget identifier');

                $builder->number('position')
                    ->required()
                    ->description('Widget position in layout');

                $builder->string('size')
                    ->enum(['small', 'medium', 'large'])
                    ->required()
                    ->description('Widget size in layout');
            });
    }
}
