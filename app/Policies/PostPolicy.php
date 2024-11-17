<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Post;
use MoonShine\Models\MoonshineUser;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny(MoonshineUser $user): bool
    {
        return true;
    }

    public function view(MoonshineUser $user, Post $item): bool
    {
        return true;
    }

    public function create(MoonshineUser $user): bool
    {
        return true;
    }

    public function update(MoonshineUser $user, Post $item): bool
    {
        return $user->isSuperUser();
    }

    public function delete(MoonshineUser $user, Post $item): bool
    {
        return $user->isSuperUser();
    }

    public function restore(MoonshineUser $user, Post $item): bool
    {
        return true;
    }

    public function forceDelete(MoonshineUser $user, Post $item): bool
    {
        return true;
    }

    public function massDelete(MoonshineUser $user): bool
    {
        return true;
    }
}
