@extends('layouts.base')
@section('page-class', 'library-shell')
@section('content')
@include('partials.library-header')
@include('partials.flash')
<div class="detail-heading"><a class="text-link" href="{{ route('library') }}">← Back to library</a><p class="eyebrow">THE DETAILS BEHIND THE DESIGN</p><h1 dir="auto">{{ $design->title }}</h1></div>
<div class="detail-layout">
    <div class="detail-image"><img src="{{ asset($design->thumbnail) }}" alt="Shared placeholder thumbnail" width="640" height="480"></div>
    <section class="detail-panel" aria-label="Design details">
        <span class="detail-status status-{{ $design->status->value }}">{{ ucfirst($design->status->value) }}</span>
        <dl><dt>Category</dt><dd>{{ $design->category->name }}</dd><dt>Created</dt><dd><time datetime="{{ $design->created_at->toIso8601String() }}">{{ $design->created_at->format('M d, Y · H:i') }}</time></dd><dt>Design number</dt><dd>#{{ $design->id }}</dd></dl>
    </section>
</div>
@endsection
