<?php

namespace Skillcraft\UiSchemaCraft\Examples;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;

class BlogPostSchema extends UIComponentSchema
{
    protected string $component = 'BlogPost';

    protected function properties(): array
    {
        return [
            // Basic Post Information
            PropertyBuilder::string('title')
                ->description('Post title')
                ->required(),
                
            PropertyBuilder::string('slug')
                ->description('URL-friendly slug')
                ->required(),

            // Content
            PropertyBuilder::markdown('content')
                ->description('Main post content')
                ->required(),
                
            PropertyBuilder::string('excerpt')
                ->description('Short post summary')
                ->required(),

            // Media
            PropertyBuilder::imageUpload('featured_image')
                ->description('Featured post image')
                ->required(),
                
            PropertyBuilder::mediaGallery('gallery')
                ->description('Additional post images')
                ->nullable(),

            // Categorization
            PropertyBuilder::treeSelect('category')
                ->description('Post category')
                ->required(),
                
            PropertyBuilder::multiSelect('tags')
                ->description('Post tags')
                ->nullable(),

            // SEO
            PropertyBuilder::object('seo')
                ->description('SEO metadata')
                ->properties([
                    PropertyBuilder::string('meta_title')
                        ->description('SEO title')
                        ->nullable(),
                        
                    PropertyBuilder::string('meta_description')
                        ->description('SEO description')
                        ->nullable(),
                        
                    PropertyBuilder::array('meta_keywords')
                        ->description('SEO keywords')
                        ->nullable(),
                ])
                ->nullable(),

            // Publishing
            PropertyBuilder::object('publishing')
                ->description('Publishing settings')
                ->properties([
                    PropertyBuilder::string('status')
                        ->enum(['draft', 'published', 'archived'])
                        ->default('draft')
                        ->required(),
                        
                    PropertyBuilder::time('publish_at')
                        ->description('Scheduled publish time')
                        ->nullable(),
                        
                    PropertyBuilder::boolean('is_featured')
                        ->description('Feature this post')
                        ->default(false),
                ])
                ->required(),

            // Comments
            PropertyBuilder::object('comments')
                ->description('Comment settings')
                ->properties([
                    PropertyBuilder::boolean('enabled')
                        ->description('Enable comments')
                        ->default(true),
                        
                    PropertyBuilder::string('moderation')
                        ->enum(['pre', 'post', 'none'])
                        ->default('pre')
                        ->description('Moderation policy'),
                        
                    PropertyBuilder::number('max_depth')
                        ->description('Maximum reply depth')
                        ->default(3),
                ])
                ->nullable(),
        ];
    }

    public function getExampleData(): array
    {
        return [
            'title' => 'Getting Started with Laravel',
            'slug' => 'getting-started-with-laravel',
            'content' => "# Getting Started with Laravel\n\nLaravel is a web application framework with expressive, elegant syntax...",
            'excerpt' => 'A beginner-friendly guide to Laravel framework',
            'category' => 'tutorials/php/laravel',
            'tags' => ['laravel', 'php', 'web-development'],
            'publishing' => [
                'status' => 'published',
                'publish_at' => '2024-01-01T10:00:00',
                'is_featured' => true,
            ],
            'seo' => [
                'meta_title' => 'Laravel Tutorial for Beginners',
                'meta_description' => 'Learn Laravel from scratch with this comprehensive guide',
                'meta_keywords' => ['laravel', 'tutorial', 'php framework'],
            ],
        ];
    }

    public function getLiveData(): array
    {
        return [];
    }
}
