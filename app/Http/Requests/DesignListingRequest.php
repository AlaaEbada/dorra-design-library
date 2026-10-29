<?php

namespace App\Http\Requests;

use App\Enums\DesignStatus;
use App\Policies\LibraryPolicy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DesignListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', LibraryPolicy::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['nullable', Rule::enum(DesignStatus::class)],
            'page' => ['nullable', 'integer', 'min:1', 'max:2147483647'],
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('library');
    }
}
