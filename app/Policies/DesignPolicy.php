<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Design;
use App\Models\User;

class DesignPolicy
{
    public function view(User $user, Design $design): bool
    {
        return in_array($user->role, [Role::Admin, Role::Viewer], true);
    }

    public function create(User $user): bool
    {
        return $user->role === Role::Admin;
    }

    public function update(User $user, Design $design): bool
    {
        return $user->role === Role::Admin;
    }

    public function delete(User $user, Design $design): bool
    {
        return $user->role === Role::Admin;
    }
}
