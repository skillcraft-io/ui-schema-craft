<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

use Skillcraft\UiSchemaCraft\Schema\Property;

trait MediaTrait
{
    /**
     * Create an image upload property.
     */
    public function imageUpload(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function ($builder) {
                $builder->string('url');
                $builder->string('thumbnail');
                $builder->string('name');
                $builder->number('size');
                $builder->string('type');
                
                $builder->object('dimensions')
                    ->properties([
                        'width' => ['type' => 'number'],
                        'height' => ['type' => 'number']
                    ]);
                
                $builder->number('maxSize')
                    ->addAttribute('default', 5120); // 5MB
                
                $builder->array('allowedTypes')
                    ->items(['type' => 'string'])
                    ->addAttribute('default', ['image/jpeg', 'image/png', 'image/gif']);
                
                $builder->boolean('crop')
                    ->addAttribute('default', false);
                
                $builder->number('aspectRatio');
            });
    }

    /**
     * Create a media gallery property.
     */
    public function mediaGallery(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function ($builder) {
                $builder->array('items')
                    ->items([
                        'type' => 'object',
                        'properties' => [
                            'url' => ['type' => 'string'],
                            'thumbnail' => ['type' => 'string'],
                            'type' => ['type' => 'string'],
                            'title' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'metadata' => ['type' => 'object']
                        ]
                    ]);

                $builder->string('layout')
                    ->enum(['grid', 'list', 'masonry'])
                    ->addAttribute('default', 'grid');

                $builder->boolean('sortable')
                    ->addAttribute('default', true);

                $builder->number('maxItems');
            });
    }

    /**
     * Create an avatar property.
     */
    public function avatar(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function ($builder) {
                $builder->string('url');
                $builder->string('initials');
                $builder->string('color');
                
                $builder->string('size')
                    ->enum(['small', 'medium', 'large'])
                    ->addAttribute('default', 'medium');
                
                $builder->string('shape')
                    ->enum(['circle', 'square'])
                    ->addAttribute('default', 'circle');
                
                $builder->string('status')
                    ->enum(['online', 'offline', 'away', 'busy']);
                
                $builder->object('badge')
                    ->properties([
                        'content' => ['type' => 'string'],
                        'color' => ['type' => 'string']
                    ]);
            });
    }
}
