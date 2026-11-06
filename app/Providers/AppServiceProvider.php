<?php

namespace App\Providers;

use App\Builder\Blocks\ButtonBlock;
use App\Builder\Blocks\ConfiguredBlock;
use App\Builder\Blocks\ContainerBlock;
use App\Builder\Blocks\CustomCodeBlock;
use App\Builder\Blocks\HeadingBlock;
use App\Builder\Blocks\SectionBlock;
use App\Builder\Blocks\TextBlock;
use App\Builder\Registry\BlockRegistry;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BlockRegistry::class, function (): BlockRegistry {
            $registry = (new BlockRegistry)
                ->register(new SectionBlock)
                ->register(new ContainerBlock)
                ->register(new HeadingBlock)
                ->register(new TextBlock)
                ->register(new ButtonBlock)
                ->register(new ConfiguredBlock('stack', 'Stack', 'layout', 'builder.blocks.element', ['content' => [], 'settings' => [], 'styles' => ['gap' => 24]], ['section', 'container', 'stack', 'grid'], ['heading', 'text', 'button', 'image', 'divider', 'spacer', 'quote', 'badge', 'stack', 'grid']))
                ->register(new ConfiguredBlock('grid', 'Grid', 'layout', 'builder.blocks.element', ['content' => [], 'settings' => ['columns' => 3], 'styles' => ['gap' => 24]], ['section', 'container', 'stack'], ['heading', 'text', 'button', 'image', 'quote', 'badge', 'stack'], ['settings.columns' => ['required', 'integer', 'between:1,6']]))
                ->register(new ConfiguredBlock('spacer', 'Spacer', 'layout', 'builder.blocks.element', ['content' => [], 'settings' => [], 'styles' => ['paddingTop' => 24, 'paddingBottom' => 24]], ['section', 'container', 'stack', 'grid']))
                ->register(new ConfiguredBlock('divider', 'Divider', 'layout', 'builder.blocks.element', ['content' => [], 'settings' => [], 'styles' => ['backgroundColor' => '#e5e7eb']], ['section', 'container', 'stack', 'grid']))
                ->register(new ConfiguredBlock('quote', 'Quote', 'content', 'builder.blocks.element', ['content' => ['text' => 'Add a memorable quote.'], 'settings' => [], 'styles' => ['fontSize' => 24]], ['section', 'container', 'stack', 'grid'], [], ['content.text' => ['required', 'string', 'max:2000']]))
                ->register(new ConfiguredBlock('badge', 'Badge', 'content', 'builder.blocks.element', ['content' => ['text' => 'New'], 'settings' => [], 'styles' => ['fontSize' => 14]], ['section', 'container', 'stack', 'grid'], [], ['content.text' => ['required', 'string', 'max:80']]))
                ->register(new ConfiguredBlock('image', 'Image', 'media', 'builder.blocks.image', ['content' => ['url' => 'https://images.unsplash.com/photo-1497366811353-6870744d04b2', 'alt' => ''], 'settings' => [], 'styles' => ['maxWidth' => 1200]], ['section', 'container', 'stack', 'grid', 'columns', 'card'], [], ['content.url' => ['required', 'url:http,https', 'max:2048'], 'content.alt' => ['nullable', 'string', 'max:500']], ['content.url' => ['control' => 'url', 'label' => 'Image URL'], 'content.alt' => ['control' => 'text', 'label' => 'Alternative text']]))
                ->register(new CustomCodeBlock);

            $parents = ['section', 'container', 'stack', 'grid', 'columns', 'card'];
            $leaf = [
                ['navigation', 'Navigation', 'layout', ['text' => 'Home | About | Contact']],
                ['logo', 'Logo', 'media', ['text' => 'Your brand']],
                ['icon', 'Icon', 'media', ['text' => '★']],
                ['video', 'Video', 'media', ['url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ']],
                ['gallery', 'Gallery', 'media', ['text' => 'Add gallery images']],
                ['list', 'List', 'content', ['text' => "First item\nSecond item\nThird item"]],
                ['testimonial', 'Testimonial', 'content', ['text' => '“An excellent experience.” — Customer']],
                ['pricing', 'Pricing', 'content', ['text' => 'Professional — $49/month']],
                ['faq', 'FAQ', 'content', ['text' => 'What do you offer? | A clear answer.']],
                ['social-links', 'Social links', 'content', ['text' => 'LinkedIn | Instagram | X']],
                ['embed', 'Map / Embed', 'advanced', ['url' => 'https://www.openstreetmap.org/export/embed.html']],
                ['form', 'Form', 'forms', ['text' => 'Contact form']],
            ];
            foreach ($leaf as [$type, $label, $category, $content]) {
                $schema = isset($content['url'])
                    ? ['content.url' => ['control' => 'url', 'label' => 'Embed URL']]
                    : ['content.text' => ['control' => 'textarea', 'label' => 'Content']];
                $registry->register(new ConfiguredBlock($type, $label, $category, 'builder.blocks.element', ['content' => $content, 'settings' => [], 'styles' => []], $parents, [], [], $schema));
            }
            $children = ['heading', 'text', 'button', 'image', 'icon', 'list', 'custom-code'];
            $registry->register(new ConfiguredBlock('columns', 'Columns', 'layout', 'builder.blocks.element', ['content' => [], 'settings' => ['columns' => 2], 'styles' => ['gap' => 24]], $parents, $children, ['settings.columns' => ['required', 'integer', 'between:1,6']], ['settings.columns' => ['control' => 'number', 'label' => 'Columns', 'min' => 1, 'max' => 6]]));
            $registry->register(new ConfiguredBlock('card', 'Card', 'layout', 'builder.blocks.element', ['content' => [], 'settings' => [], 'styles' => ['paddingTop' => 24, 'paddingBottom' => 24, 'borderRadius' => 16]], $parents, $children));

            return $registry;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
