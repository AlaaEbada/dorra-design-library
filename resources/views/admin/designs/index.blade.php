@extends('layouts.base')
@section('page-class', 'library-shell')
@section('content')
@include('partials.library-header')
@include('partials.flash')
<section class="admin-heading" aria-labelledby="page-title">
    <div><p class="eyebrow">ADMINISTRATION</p><h1 id="page-title">Manage designs<span>.</span></h1><p class="intro-copy">Create, update, or remove items from the collection.</p></div>
    <a class="button button-primary" href="{{ route('designs.create') }}">+ Add design</a>
</section>
@if($designs->isNotEmpty())
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
<section class="empty-state"><span class="empty-icon" aria-hidden="true">+</span><h2>No designs yet</h2><p>Start the collection with its first design.</p><a class="button button-primary" href="{{ route('designs.create') }}">Add design</a></section>
@endif
@if($designs->hasPages())
<nav class="pagination" aria-label="Management pagination">
    <p>Showing {{ number_format($designs->firstItem() ?? 0) }}–{{ number_format($designs->lastItem() ?? 0) }} of {{ number_format($designs->total()) }}</p>
    <div class="page-controls">@if($designs->onFirstPage())<span class="button disabled" aria-disabled="true">← Previous</span>@else<a class="button" href="{{ $designs->previousPageUrl() }}" rel="prev">← Previous</a>@endif<span class="page-number">Page {{ number_format($designs->currentPage()) }} of {{ number_format($designs->lastPage()) }}</span>@if($designs->hasMorePages())<a class="button" href="{{ $designs->nextPageUrl() }}" rel="next">Next →</a>@else<span class="button disabled" aria-disabled="true">Next →</span>@endif</div>
</nav>
@endif
@endsection
