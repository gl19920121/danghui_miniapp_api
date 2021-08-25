<?php

namespace App\Policies;

use App\Models\User;
use App\Models\resume;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ResumePolicy
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
     * @param  \App\Models\resume  $resume
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, resume $resume)
    {
        return $resume->upload_uid === $user->openid
                ? Response::allow()
                : Response::deny('无权查看');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\resume  $resume
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, resume $resume)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\resume  $resume
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, resume $resume)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\resume  $resume
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, resume $resume)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\resume  $resume
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, resume $resume)
    {
        //
    }
}
