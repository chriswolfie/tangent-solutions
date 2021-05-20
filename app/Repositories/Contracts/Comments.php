<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use stdClass;

interface Comments extends Base 
{
    public function fetchAllCommentsFromPost(int $post_id) : Collection;
    public function createComment(int $post_id, int $user_id, string $content) : ?stdClass;
    public function updateComment(int $comment_id, string $content) : ?stdClass;
}