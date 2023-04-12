<?php

namespace App\Providers;

use App\Policies\MoonShineUserPolicy;
use App\Policies\MoonShineUserRolePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use MoonShine\Models\MoonshineUser;
use MoonShine\Models\MoonshineUserRole;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        MoonshineUser::class => MoonShineUserPolicy::class,
        MoonshineUserRole::class => MoonShineUserRolePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
