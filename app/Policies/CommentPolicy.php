<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Comment;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;
class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function store(User $user)
    {
        return $user->role === 'normal_user';
    }

    public function update(User $user, Comment $comment)
    {
        return ($user->id === $comment->user_id);
    }

    public function destroy(User $user, Comment $comment)
    {
        return ($user->id === $comment->user_id || $user->role === 'admin');
    }
}
