<?php

namespace App\Http\Controllers;

use App\Builder\Actions\PublishWebsite;
use App\Models\Website;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebsiteController extends Controller
{
    public function store(Request $request, Workspace $workspace): RedirectResponse
    {
        abort_unless($request->user()->belongsToWorkspace($workspace), 403);
        $role = $workspace->users()->whereKey($request->user()->id)->first()?->pivot->role;
        abort_unless(in_array($role, ['owner', 'admin', 'designer'], true), 403);
        $data = $request->validate(['name' => ['required', 'string', 'max:120']]);
        $website = DB::transaction(function () use ($data, $workspace, $request): Website {
            $base = Str::slug($data['name']) ?: 'website';
            $slug = $base;
            for ($suffix = 2; Website::where('workspace_id', $workspace->id)->where('slug', $slug)->exists(); $suffix++) {
                $slug = $base.'-'.$suffix;
            }
            $website = Website::create(['workspace_id' => $workspace->id, 'created_by' => $request->user()->id, 'name' => $data['name'], 'slug' => $slug]);
            $website->pages()->create(['name' => 'Home', 'slug' => 'home', 'is_homepage' => true]);

            return $website;
        });

        return redirect()->route('editor', [$website, $website->pages()->first()])->with('status', 'Website created.');
    }

    public function publish(Request $request, Website $website, PublishWebsite $publisher): RedirectResponse
    {
        $this->authorize('publish', $website);
        $publisher->execute($website, $request->user());

        return back()->with('status', 'Website published successfully.');
    }

    public function destroy(Website $website): RedirectResponse
    {
        $this->authorize('delete', $website);
        $website->delete();

        return redirect()->route('dashboard')->with('status', 'Website moved to trash.');
    }
}
