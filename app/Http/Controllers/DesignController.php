<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDesignRequest;
use App\Http\Requests\UpdateDesignRequest;
use App\Models\Category;
use App\Models\Design;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DesignController extends Controller
{
    private const THUMBNAIL = 'images/design-placeholder.svg';

    public function index(): View
    {
        Gate::authorize('create', Design::class);

        return view('admin.designs.index', [
            'designs' => Design::query()
                ->with('category:id,name')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(24),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Design::class);

        return view('designs.form', [
            'design' => new Design,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreDesignRequest $request): RedirectResponse
    {
        $design = Design::create([
            ...$request->safe()->only(['title', 'category_id', 'status']),
            'thumbnail' => self::THUMBNAIL,
        ]);

        return redirect()->route('admin.designs')->with('success', 'Design created successfully.');
    }

    public function show(Design $design): View
    {
        Gate::authorize('view', $design);

        return view('designs.show', ['design' => $design->load('category:id,name')]);
    }

    public function edit(Design $design): View
    {
        Gate::authorize('update', $design);

        return view('designs.form', [
            'design' => $design,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateDesignRequest $request, Design $design): RedirectResponse
    {
        $design->update([
            ...$request->safe()->only(['title', 'category_id', 'status']),
            'thumbnail' => self::THUMBNAIL,
        ]);

        return redirect()->route('admin.designs')->with('success', 'Design updated successfully.');
    }

    public function destroy(Design $design): RedirectResponse
    {
        Gate::authorize('delete', $design);
        $design->delete();

        return redirect()->route('admin.designs')->with('success', 'Design deleted successfully.');
    }
}
