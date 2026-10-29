<?php

namespace App\Http\Requests;

class UpdateDesignRequest extends StoreDesignRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('design')) ?? false;
    }
}
