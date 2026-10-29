<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;

class LibraryPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [Role::Admin, Role::Viewer], true);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->role === Role::Admin;
    }

    public function update(User $user): bool
    {
        return $this->create($user);
    }

    public function delete(User $user): bool
    {
        return $this->create($user);
    }
}
