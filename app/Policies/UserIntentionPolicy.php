<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserIntention;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserIntentionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserIntention  $userIntention
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, UserIntention $userIntention)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return UserIntention::where('user_id', $user->id)->count() < UserIntention::MAX_SIZE
                ? Response::allow()
                : Response::deny('已达上限，无法添加');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserIntention  $userIntention
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, UserIntention $userIntention)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserIntention  $userIntention
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, UserIntention $userIntention)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserIntention  $userIntention
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, UserIntention $userIntention)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserIntention  $userIntention
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, UserIntention $userIntention)
    {
        //
    }
}
