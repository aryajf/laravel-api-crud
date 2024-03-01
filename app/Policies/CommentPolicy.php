<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function AccessComment(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id;
    }
}
