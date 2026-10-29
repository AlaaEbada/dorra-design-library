<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleAccountSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Sample accounts are restricted to local/testing environments.');
        }

        foreach (['admin' => Role::Admin, 'viewer' => Role::Viewer] as $name => $role) {
            $user = User::firstOrNew(['email' => $name.'@dorra.test']);
            $user->name = ucfirst($name);
            $user->role = $role;
            $user->password = 'DorraSample123!'; // Local sample credentials only.
            $user->save();
        }
    }
}
