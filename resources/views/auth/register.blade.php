<x-layouts.app title="Create account · StudioForge">
    <section class="auth-shell"><form class="card auth-card" method="POST" action="{{ route('register.store') }}">@csrf
        <p class="eyebrow">Start building</p><h1>Create your workspace</h1>
        <label>Your name<input name="name" value="{{ old('name') }}" required autofocus></label>
        <label>Work or studio name<input name="workspace_name" value="{{ old('workspace_name') }}" required></label>
        <label>Email<input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Password<input type="password" name="password" minlength="10" required></label>
        <label>Confirm password<input type="password" name="password_confirmation" required></label>
        @if($errors->any())<div class="error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <button class="button primary wide">Create account</button><p class="center muted">Already registered? <a href="{{ route('login') }}">Sign in</a></p>
    </form></section>
</x-layouts.app>
