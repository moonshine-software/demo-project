<?php

namespace App\Policies;

use MoonShine\Models\MoonShineUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use MoonShine\Models\MoonshineUserRole;

class MoonShineUserRolePolicy
{
    use HandlesAuthorization;

    public function viewAny(MoonShineUser $user): bool
    {
        return $user->moonshine_user_role_id === 1;
    }

    public function view(MoonShineUser $user, MoonshineUserRole $role): bool
    {
        return $user->moonshine_user_role_id === 1;
    }

    public function create(MoonShineUser $user): bool
    {
        if(config('app.demo_mode', false)) {
            return false;
        }

        return $user->moonshine_user_role_id === 1;
    }

    public function update(MoonShineUser $user, MoonshineUserRole $role): bool
    {
        if(config('app.demo_mode', false)) {
            return false;
        }

        return $user->moonshine_user_role_id === 1;
    }

    public function delete(MoonShineUser $user, MoonshineUserRole $role): bool
    {
        if(config('app.demo_mode', false)) {
            return false;
        }

        return $user->moonshine_user_role_id === 1;
    }

    public function restore(MoonShineUser $user, MoonshineUserRole $role): bool
    {
        if(config('app.demo_mode', false)) {
            return false;
        }

        return $user->moonshine_user_role_id === 1;
    }

    public function forceDelete(MoonShineUser $user, MoonshineUserRole $role): bool
    {
        if(config('app.demo_mode', false)) {
            return false;
        }

        return $user->moonshine_user_role_id === 1;
    }

    public function massDelete(MoonShineUser $user): bool
    {
        if(config('app.demo_mode', false)) {
            return false;
        }

        return $user->moonshine_user_role_id === 1;
    }
}
