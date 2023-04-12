<?php

namespace App\Policies;

use App\Models\Dictionary;
use Illuminate\Auth\Access\HandlesAuthorization;
use MoonShine\Models\MoonshineUser;

class DictionaryPolicy
{
    use HandlesAuthorization;

    public function viewAny(MoonshineUser $user)
    {
        return true;
    }

    public function view(MoonshineUser $user, Dictionary $item)
    {
        return true;
    }

    public function create(MoonshineUser $user)
    {
        return true;
    }

    public function update(MoonshineUser $user, Dictionary $item)
    {
        return true;
    }

    public function delete(MoonshineUser $user, Dictionary $item)
    {
        return $user->moonshine_user_role_id === 1;
    }

    public function restore(MoonshineUser $user, Dictionary $item)
    {
        return $user->moonshine_user_role_id === 1;
    }

    public function forceDelete(MoonshineUser $user, Dictionary $item)
    {
        return $user->moonshine_user_role_id === 1;
    }

    public function massDelete(MoonshineUser $user)
    {
        return $user->moonshine_user_role_id === 1;
    }
}
