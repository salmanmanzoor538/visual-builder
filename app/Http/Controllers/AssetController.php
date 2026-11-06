<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function store(Request $request, Workspace $workspace): RedirectResponse
    {
        $this->authorizeWorkspace($request, $workspace, ['owner', 'admin', 'designer', 'editor']);
        $data = $request->validate(['file' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp,avif,pdf,mp4,webm,mp3,wav', 'max:20480'], 'alt_text' => ['nullable', 'string', 'max:500']]);
        $file = $data['file'];
        $checksum = hash_file('sha256', $file->getRealPath());
        if ($workspace->assets()->where('checksum', $checksum)->exists()) {
            return back()->with('status', 'That file is already in the media library.');
        }
        $path = $file->store("workspaces/{$workspace->id}", 'public');
        [$width, $height] = str_starts_with((string) $file->getMimeType(), 'image/') ? (getimagesize($file->getRealPath()) ?: [null, null]) : [null, null];
        $workspace->assets()->create(['uploaded_by' => $request->user()->id, 'disk' => 'public', 'path' => $path, 'original_name' => $file->getClientOriginalName(), 'mime_type' => $file->getMimeType(), 'size' => $file->getSize(), 'width' => $width, 'height' => $height, 'checksum' => $checksum, 'alt_text' => $data['alt_text'] ?? null]);

        return back()->with('status', 'Media uploaded.');
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $this->authorize('delete', $asset);
        $asset->delete();

        return back()->with('status', 'Media moved to trash.');
    }

    private function authorizeWorkspace(Request $request, Workspace $workspace, array $roles): void
    {
        abort_unless($workspace->users()->whereKey($request->user()->id)->wherePivotIn('role', $roles)->exists(), 403);
    }
}
