<?php

namespace App\Http\Controllers;

use App\Cms\CmsValueValidator;
use App\Models\Website;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function __invoke(Website $website, CmsValueValidator $validator): View
    {
        $this->authorize('view', $website);
        $website->load(['cmsCollections.fields', 'cmsCollections.items']);
        $assets = $website->workspace->assets()->latest()->get();

        return view('content', ['website' => $website, 'assets' => $assets, 'fieldTypes' => $validator->types()]);
    }
}
