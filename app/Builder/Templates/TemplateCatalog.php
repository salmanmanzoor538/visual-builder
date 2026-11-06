<?php

namespace App\Builder\Templates;

use App\Models\Workspace;

final class TemplateCatalog
{
    public function all(Workspace $workspace): array
    {
        return collect($this->builtIns())
            ->merge($workspace->builderTemplates()->get()->map(fn ($template) => [
                'key' => $template->id, 'name' => $template->name, 'category' => $template->category,
                'scope' => $template->scope, 'thumbnail' => $template->thumbnail, 'snapshot' => $template->snapshot,
                'builtin' => false,
            ]))->values()->all();
    }

    public function find(Workspace $workspace, string $key): array
    {
        return collect($this->all($workspace))->firstWhere('key', $key)
            ?? abort(404, 'Template not found.');
    }

    private function builtIns(): array
    {
        $section = fn (string $headline, string $copy, string $button = 'Get started') => [
            'schema_version' => 1,
            'nodes' => [[
                'type' => 'section', 'content' => [], 'settings' => ['tag' => 'section'],
                'styles' => ['paddingTop' => 88, 'paddingBottom' => 88, 'backgroundColor' => '#f6f3ff'], 'responsive' => [], 'children' => [[
                    'type' => 'container', 'content' => [], 'settings' => [], 'styles' => ['maxWidth' => 1120], 'responsive' => [], 'children' => [
                        ['type' => 'heading', 'content' => ['text' => $headline], 'settings' => ['level' => 1], 'styles' => ['fontSize' => 56, 'fontWeight' => 700], 'responsive' => ['mobile' => ['fontSize' => 38]], 'children' => []],
                        ['type' => 'text', 'content' => ['text' => $copy], 'settings' => [], 'styles' => ['fontSize' => 20], 'responsive' => [], 'children' => []],
                        ['type' => 'button', 'content' => ['label' => $button, 'url' => '#contact'], 'settings' => ['target' => '_self'], 'styles' => [], 'responsive' => [], 'children' => []],
                    ],
                ]],
            ]],
        ];

        $pages = [
            ['business', 'Business Starter', 'business', 'Build trust. Win more customers.', 'A polished foundation for a modern service business.'],
            ['portfolio', 'Creative Portfolio', 'portfolio', 'Selected work with a point of view.', 'Introduce your work, process, and strongest projects.'],
            ['agency', 'Agency Launch', 'agency', 'Ideas that move brands forward.', 'A conversion-focused agency page ready for your case studies.'],
            ['saas', 'SaaS Product', 'saas', 'One workspace. Remarkable momentum.', 'Explain your product and turn interest into trials.'],
            ['restaurant', 'Restaurant', 'restaurant', 'Seasonal food, made with care.', 'Show your menu, story, hours, and reservations.'],
            ['landing', 'Campaign Landing', 'landing', 'The simpler way to get results.', 'A focused landing page built around one clear action.'],
        ];

        $templates = collect($pages)->map(fn ($item) => [
            'key' => 'page-'.$item[0], 'name' => $item[1], 'category' => $item[2], 'scope' => 'page',
            'thumbnail' => null, 'snapshot' => $section($item[3], $item[4]), 'builtin' => true,
        ])->all();

        foreach ([
            ['hero', 'Hero', 'Launch something people love.', 'Start with a sharp value proposition.'],
            ['features', 'Features', 'Everything your team needs.', 'Fast, flexible, and designed for real work.'],
            ['cta', 'Call to action', 'Ready to begin?', 'Create your next page in minutes.'],
            ['pricing', 'Pricing', 'Simple pricing that scales.', 'Choose the plan that fits your team.'],
            ['testimonials', 'Testimonials', 'Loved by growing teams.', 'Add customer stories and proof here.'],
            ['faq', 'FAQ', 'Questions, answered.', 'Make the decision easier for your visitors.'],
            ['contact', 'Contact', 'Let’s talk.', 'Tell visitors how to reach your team.'],
            ['navigation', 'Navigation', 'Your brand', 'Home · Work · About · Contact'],
            ['footer', 'Footer', 'Stay in the loop.', '© Your company. All rights reserved.'],
        ] as $item) {
            $templates[] = ['key' => 'section-'.$item[0], 'name' => $item[1], 'category' => $item[0], 'scope' => 'section', 'thumbnail' => null, 'snapshot' => $section($item[2], $item[3]), 'builtin' => true];
        }

        return $templates;
    }
}
