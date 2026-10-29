@extends('layouts.base')
@section('page-class', 'library-shell')
@section('content')
@include('partials.library-header')
<div class="detail-heading">
    <a class="text-link" href="{{ route('admin.designs') }}">← Back to management</a>
    <p class="eyebrow">MAKE SOMETHING WORTH COLLECTING</p>
    <h1>{{ $design->exists ? 'Edit design' : 'Add a design' }}<span>.</span></h1>
    <p class="intro-copy">A title, a category, and a little inspiration.</p>
</div>
<div class="editor-layout">
    <form class="editor-panel" method="POST" action="{{ $design->exists ? route('designs.update', $design) : route('designs.store') }}">
        @csrf
        @if($design->exists) @method('PUT') @endif
        @php($titleValue = old('title', $design->title))
        <label for="title">Design title <span class="required">*</span></label>
        <input id="title" name="title" type="text" dir="auto" maxlength="255" value="{{ is_string($titleValue) ? $titleValue : '' }}" required aria-describedby="title-help @error('title') title-error @enderror" @error('title') aria-invalid="true" @enderror>
        <p class="field-help" id="title-help">Up to 255 characters. Arabic and English are welcome.</p>
        @error('title')<p class="error" role="alert" id="title-error">{{ $message }}</p>@enderror
        <label for="category_id">Category <span class="required">*</span></label>
        <select id="category_id" name="category_id" required @error('category_id') aria-invalid="true" aria-describedby="category-error" @enderror>
            <option value="">Choose a category</option>
            @foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $design->category_id) == $category->id)>{{ $category->name }}</option>@endforeach
        </select>
        @error('category_id')<p class="error" role="alert" id="category-error">{{ $message }}</p>@enderror
        @if($categories->isEmpty())<p class="error" role="alert">No categories are available. Please contact your administrator.</p>@endif
        <label for="status">Status <span class="required">*</span></label>
        <select id="status" name="status" required @error('status') aria-invalid="true" aria-describedby="status-error" @enderror>
            @foreach(\App\Enums\DesignStatus::cases() as $status)<option value="{{ $status->value }}" @selected(old('status', $design->status?->value ?? 'draft') === $status->value)>{{ ucfirst($status->value) }}</option>@endforeach
        </select>
        <p class="field-help">Both draft and published designs are visible to library members.</p>
        @error('status')<p class="error" role="alert" id="status-error">{{ $message }}</p>@enderror
        @error('thumbnail')<p class="error" role="alert">{{ $message }}</p>@enderror
        <div class="form-actions"><button class="button button-primary" type="submit" @disabled($categories->isEmpty())>{{ $design->exists ? 'Save changes' : 'Create design' }} ↗</button><a class="button" href="{{ route('admin.designs') }}">Cancel</a></div>
    </form>
    <aside class="editor-aside"><img src="{{ asset('images/design-placeholder.svg') }}" alt="Shared placeholder thumbnail" width="640" height="480"><div><p class="eyebrow">ONE COLLECTION. ONE VISUAL.</p><h2>Keep the focus on the idea.</h2><p>Every design uses this shared thumbnail. No image upload is needed.</p></div></aside>
</div>
@endsection
