<?php

namespace App\Http\Requests;

use App\Enums\DesignStatus;
use App\Models\Design;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDesignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Design::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'status' => ['required', Rule::enum(DesignStatus::class)],
            'thumbnail' => ['prohibited'],
        ];
    }
}
