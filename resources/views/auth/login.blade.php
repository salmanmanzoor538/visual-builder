<x-layouts.app title="Sign in · StudioForge">
    <section class="auth-shell"><form class="card auth-card" method="POST" action="{{ route('login.store') }}">@csrf
        <p class="eyebrow">Welcome back</p><h1>Sign in to StudioForge</h1><p class="muted">Create and publish beautiful websites without code.</p>
        <label>Email<input type="email" name="email" value="{{ old('email') }}" required autofocus></label>
        <label>Password<input type="password" name="password" required></label>
        <label class="check"><input type="checkbox" name="remember" value="1"> Remember me</label>
        @if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
        <button class="button primary wide">Sign in</button><p class="center muted">New here? <a href="{{ route('register') }}">Create an account</a></p>
    </form></section>
</x-layouts.app>
