@extends('layouts.base')
@section('page-class', 'library-shell admin-shell')
@section('content')
@include('partials.library-header')
@include('partials.flash')
<div class="admin-content">
<section class="admin-heading" aria-labelledby="page-title">
    <div><p class="eyebrow">ADMINISTRATION</p><h1 id="page-title">Manage designs<span>.</span></h1><p class="intro-copy">Create, update, or remove items from the collection.</p></div>
    <a class="button button-primary" href="{{ route('designs.create') }}">+ Add design</a>
</section>
<section id="search-filters" class="filter-panel" aria-label="Search and filters">
    <form method="GET" action="{{ route('admin.designs') }}#search-filters" class="filters">
        <div class="search-field"><label for="title">Search designs</label><input id="title" name="title" type="search" maxlength="255" value="{{ $filters['title'] ?? '' }}" placeholder="Start typing a design title…" aria-describedby="search-hint">@error('title')<p class="error" role="alert">{{ $message }}</p>@enderror</div>
        <div><label for="category_id">Category</label><select id="category_id" name="category_id"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '') == $category->id)>{{ $category->name }}</option>@endforeach</select>@error('category_id')<p class="error" role="alert">{{ $message }}</p>@enderror</div>
        <div><label for="status">Status</label><select id="status" name="status"><option value="">All statuses</option>@foreach(\App\Enums\DesignStatus::cases() as $status)<option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ ucfirst($status->value) }}</option>@endforeach</select>@error('status')<p class="error" role="alert">{{ $message }}</p>@enderror</div>
        <button class="button button-primary" type="submit">Find designs ↗</button>
    </form>
    <div class="filter-footer"><p id="search-hint">Search matches the beginning of a title — in Arabic or English.</p><a class="text-link" href="{{ route('admin.designs') }}">Clear filters</a></div>
    @error('page')<p class="error" role="alert">{{ $message }}</p>@enderror
</section>
@if($designs->isNotEmpty())
<div class="results-bar"><h2>Results <span class="count">{{ number_format($designs->total()) }}</span></h2><span>Newest first ↓</span></div>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead><tr><th scope="col">Design</th><th scope="col">Category</th><th scope="col">Status</th><th scope="col">Created</th><th scope="col"><span class="sr-only">Actions</span></th></tr></thead>
        <tbody>
        @foreach($designs as $design)
            <tr>
                <td><div class="admin-design"><img class="admin-thumbnail" src="{{ asset($design->thumbnail) }}" alt="" width="58" height="44"><div><a class="card-title" href="{{ route('designs.show', $design) }}" dir="auto">{{ $design->title }}</a><small>#{{ $design->id }}</small></div></div></td>
                <td>{{ $design->category->name }}</td>
                <td><span class="status status-{{ $design->status->value }}">{{ ucfirst($design->status->value) }}</span></td>
                <td><time datetime="{{ $design->created_at->toIso8601String() }}">{{ $design->created_at->format('M d, Y') }}</time></td>
                <td class="admin-actions"><a class="button" href="{{ route('designs.edit', $design) }}">Edit</a><details><summary>Delete</summary><form method="POST" action="{{ route('designs.destroy', $design) }}">@csrf @method('DELETE')<button class="button button-danger" type="submit">Confirm delete</button></form></details></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@else
<section class="empty-state"><span class="empty-icon" aria-hidden="true">⌕</span><h2>No designs found</h2><p>Try another title prefix, category, or status, or add a new design.</p><a class="button" href="{{ route('admin.designs') }}">Clear filters</a></section>
@endif
@if($designs->hasPages())
<nav class="pagination" aria-label="Management pagination">
    <p>Showing {{ number_format($designs->firstItem() ?? 0) }}–{{ number_format($designs->lastItem() ?? 0) }} of {{ number_format($designs->total()) }}</p>
    <div class="page-controls">@if($designs->onFirstPage())<span class="button disabled" aria-disabled="true">← Previous</span>@else<a class="button" href="{{ $designs->previousPageUrl() }}" rel="prev">← Previous</a>@endif<span class="page-number">Page {{ number_format($designs->currentPage()) }} of {{ number_format($designs->lastPage()) }}</span>@if($designs->hasMorePages())<a class="button" href="{{ $designs->nextPageUrl() }}" rel="next">Next →</a>@else<span class="button disabled" aria-disabled="true">Next →</span>@endif</div>
</nav>
@endif
</div>
<footer class="site-footer"><span>DORRA <span aria-hidden="true">/</span> DESIGN LIBRARY</span><a class="text-link" href="{{ route('library') }}">Browse the library ↗</a></footer>
@endsection
