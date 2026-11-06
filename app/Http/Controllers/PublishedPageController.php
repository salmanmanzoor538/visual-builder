<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Contracts\View\View;

class PublishedPageController extends Controller
{
    public function __invoke(string $website, ?string $page = null): View
    {
        $site = Website::query()->where('slug', $website)->where('status', 'published')->with('activePublishVersion.pages')->firstOrFail();
        $slug = $page ?: $site->pages()->where('is_homepage', true)->value('slug') ?: 'home';
        $publishedPage = $site->activePublishVersion->pages->firstWhere('slug', $slug) ?? abort(404);

        return view('published.page', compact('site', 'publishedPage'));
    }
}
