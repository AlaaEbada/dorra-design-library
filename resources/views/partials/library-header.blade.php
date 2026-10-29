<header class="topbar">
    <a class="brand" href="{{ route('library') }}" aria-label="Dorra library home"><span class="brand-symbol" aria-hidden="true">d.</span><span>DORRA<small>DESIGN LIBRARY</small></span></a>
    <div class="account"><span class="account-name">{{ auth()->user()->name }}</span><span class="role" @can('create', \App\Policies\LibraryPolicy::class) title="Administrator access enabled." @endcan>{{ ucfirst(auth()->user()->role->value) }}</span>@can('create', \App\Models\Design::class)<a class="button button-quiet" href="{{ route('admin.designs') }}">Manage designs</a><span class="sr-only">Administrator access enabled.</span>@endcan
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-quiet" type="submit">Sign out ↗</button></form>
    </div>
</header>
