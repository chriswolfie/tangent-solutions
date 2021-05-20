<?php

namespace App\Repositories\Eloquent;

use App\Models\Comments as CommentsModel;
use App\Models\Posts as PostsModel;
use App\Repositories\Contracts\Comments as CommentsContract;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use stdClass;

class Comments extends Base implements CommentsContract
{
    private $posts_model;
    public function __construct(PostsModel $posts_model, CommentsModel $comments_model)
    {
        $this->posts_model = $posts_model;
    }

    public function fetchAllCommentsFromPost(int $post_id) : Collection
    {
        $post = $this->posts_model->find($post_id);
        if ($post) {
            $comments = $post->comments()->with('users')->get();
            return $comments;
        }
        return new Collection([]);
    }

    public function createComment(int $post_id, int $user_id, string $content) : ?stdClass
    {
        $entry = false;
        try {
            $entry = $this->model->create([
                'content' => $content,
                'user_id' => $user_id,
                'post_id' => $post_id
            ]);
        }
        catch (QueryException $e) {
            return null;
        }
        return json_decode( json_encode( $entry->toArray() ) );
    }
}