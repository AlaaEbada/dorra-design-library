@extends('layouts.base')
@section('page-class', 'library-shell')
@section('content')
@include('partials.library-header')
@include('partials.flash')
<section class="intro" aria-labelledby="page-title">
    <div><p class="eyebrow">A SPACE FOR CREATIVE WORK</p><h1 id="page-title">The design collection<span>.</span></h1><p class="intro-copy">Explore ideas, find inspiration, and make room for your next great design.</p></div>
    <div class="collection-note"><span class="note-mark" aria-hidden="true">✳</span><p>Thoughtfully collected.<br>Ready to inspire.</p></div>
</section>
<section class="filter-panel" aria-label="Search and filters">
    <form method="GET" action="{{ route('library') }}" class="filters">
        <div class="search-field"><label for="title">Search the collection</label><input id="title" name="title" type="search" maxlength="255" value="{{ $filters['title'] ?? '' }}" placeholder="Start typing a design title…" aria-describedby="search-hint @error('title') title-error @enderror">@error('title')<p id="title-error" class="error" role="alert">{{ $message }}</p>@enderror</div>
        <div><label for="category_id">Category</label><select id="category_id" name="category_id"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '') == $category->id)>{{ $category->name }}</option>@endforeach</select>@error('category_id')<p class="error" role="alert">{{ $message }}</p>@enderror</div>
        <div><label for="status">Status</label><select id="status" name="status"><option value="">All statuses</option>@foreach(\App\Enums\DesignStatus::cases() as $status)<option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ ucfirst($status->value) }}</option>@endforeach</select>@error('status')<p class="error" role="alert">{{ $message }}</p>@enderror</div>
        <button class="button button-primary" type="submit">Find designs <span aria-hidden="true">↗</span></button>
    </form>
    <div class="filter-footer"><p id="search-hint">Search matches the beginning of a title — in Arabic or English.</p><a class="text-link" href="{{ route('library') }}">Clear filters</a></div>
    @error('page')<p class="error" role="alert">{{ $message }}</p>@enderror
</section>
<div class="results-bar"><h2>All designs <span class="count">{{ number_format($designs->total()) }}</span></h2><div class="result-actions"><span>Newest first <span aria-hidden="true">↓</span></span></div></div>
@if($designs->isNotEmpty())
<section class="design-grid" aria-label="Designs">
    @foreach($designs as $design)
    <article class="design-card">
        <div class="card-image"><img src="{{ asset($design->thumbnail) }}" alt="Shared placeholder thumbnail" width="640" height="480" loading="lazy"><span class="status status-{{ $design->status->value }}">{{ ucfirst($design->status->value) }}</span></div>
        <div class="card-content"><p class="category">{{ $design->category->name }}</p><h3 dir="auto"><a class="card-title" href="{{ route('designs.show', $design) }}">{{ $design->title }}</a></h3><div class="card-meta"><time datetime="{{ $design->created_at->toDateString() }}">{{ $design->created_at->format('M d, Y') }}</time><span>#{{ $design->id }}</span></div></div>
    </article>
    @endforeach
</section>
@else
<section class="empty-state"><span class="empty-icon" aria-hidden="true">⌕</span><h2>No designs found</h2><p>Try a different title prefix, category, or status.</p><a class="button button-primary" href="{{ route('library') }}">View all designs</a></section>
@endif
@if($designs->hasPages())
<nav class="pagination" aria-label="Pagination">
    <p>Showing {{ number_format($designs->firstItem() ?? 0) }}–{{ number_format($designs->lastItem() ?? 0) }} of {{ number_format($designs->total()) }}</p>
    <div class="page-controls">@if($designs->onFirstPage())<span class="button disabled" aria-disabled="true">← Previous</span>@else<a class="button" href="{{ $designs->previousPageUrl() }}" rel="prev">← Previous</a>@endif<span class="page-number">Page {{ number_format($designs->currentPage()) }} of {{ number_format($designs->lastPage()) }}</span>@if($designs->hasMorePages())<a class="button" href="{{ $designs->nextPageUrl() }}" rel="next">Next →</a>@else<span class="button disabled" aria-disabled="true">Next →</span>@endif</div>
</nav>
@endif
<footer class="site-footer"><span>DORRA <span aria-hidden="true">/</span> DESIGN LIBRARY</span><span>A little inspiration goes a long way.</span></footer>
@endsection
