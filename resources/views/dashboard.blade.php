<x-layouts.app title="Dashboard · StudioForge">
<div class="dashboard shell">
    <div class="page-heading"><div><p class="eyebrow">Your workspace</p><h1>Websites</h1><p class="muted">Create, edit, preview, and publish your sites.</p></div></div>
    @foreach($workspaces as $workspace)
        <section class="workspace-section">
            <div class="section-heading"><h2>{{ $workspace->name }}</h2>
                <form method="POST" action="{{ route('websites.store', $workspace) }}" class="inline-create">@csrf<input name="name" placeholder="New website name" required><button class="button primary">Create website</button></form>
            </div>
            <div class="site-grid">
            @forelse($workspace->websites as $website)
                <article class="card site-card"><div class="site-preview"><span>{{ strtoupper(substr($website->name, 0, 2)) }}</span></div>
                    <div class="site-info"><div><h3>{{ $website->name }}</h3><p class="muted">{{ $website->pages->count() }} pages · {{ ucfirst($website->status) }}</p></div>
                    <div class="site-actions">@if($website->pages->first())<a class="button" href="{{ route('editor', [$website, $website->pages->first()]) }}">Open editor</a>@endif<a class="button ghost" href="{{ route('content', $website) }}">Content & media</a>
                    @if($website->status === 'published')<a class="button ghost" target="_blank" href="{{ route('published.page', $website->slug) }}">View site</a>@endif</div></div>
                </article>
            @empty <div class="empty card"><h3>Create your first website</h3><p class="muted">Give it a name above. We will prepare a blank homepage for you.</p></div>@endforelse
            </div>
        </section>
    @endforeach
</div>
</x-layouts.app>
