<?php

namespace App\Http\Requests;

use App\Models\Design;

class AdminDesignListingRequest extends DesignListingRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Design::class) ?? false;
    }

    protected function getRedirectUrl(): string
    {
        return route('admin.designs');
    }
}
