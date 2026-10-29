<?php

namespace App\Providers;

use App\Models\Design;
use App\Policies\DesignPolicy;
use App\Policies\LibraryPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Design::class, DesignPolicy::class);
        Gate::policy(LibraryPolicy::class, LibraryPolicy::class);
    }
}
