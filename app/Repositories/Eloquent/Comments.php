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
        parent::__construct($comments_model);
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

    // custom implementation to include the user relation...
    public function fetchSingleEntry(int $id) : ?stdClass
    {
        $entry = $this->model->where('id', $id)->with('users')->first();
        if (!$entry) {
            return null;
        }
        return json_decode( json_encode( $entry->toArray() ) );
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

        $entry = $this->model->where('id', $entry->id)->with('users')->first();
        return json_decode( json_encode( $entry->toArray() ) );
    }

    public function updateComment(int $comment_id, string $content) : ?stdClass
    {
        $entry = $this->model->find($comment_id);
        if (!$entry) {
            return null;
        }

        try {
            $entry->content = $content;
            $entry->save();
        }
        catch (QueryException $e) {
            return null;
        }
        
        $entry = $this->model->where('id', $entry->id)->with('users')->first();
        return json_decode( json_encode( $entry->toArray() ) );
    }
}