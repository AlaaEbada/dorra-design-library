<?php

namespace App\Http\Controllers;

use App\Http\Requests\DesignListingRequest;
use App\Models\Category;
use App\Models\Design;
use Illuminate\View\View;

class DesignListingController extends Controller
{
    public function __invoke(DesignListingRequest $request): View
    {
        $filters = $request->validated();

        return view('library', [
            'filters' => $filters,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'designs' => Design::query()->filter($filters)->with('category:id,name')
                ->orderByDesc('created_at')->orderByDesc('id')->paginate(24)->withQueryString(),
        ]);
    }
}
