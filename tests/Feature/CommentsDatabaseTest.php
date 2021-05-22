<?php

namespace Tests\Feature;

use App\Repositories\Contracts\Comments as CommentsContract;
use App\Repositories\Eloquent\Comments as CommentsEloquent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\PersonalAssistant;
use Tests\TestCase;

class CommentsDatabaseTest extends TestCase
{
    use RefreshDatabase,
    PersonalAssistant;

    protected function setUp(): void
    {
        parent::setUp();
        app()->bind(CommentsContract::class, CommentsEloquent::class);
    }

    public function test_fetching_comments_from_a_post()
    {
        // seed the DB for this test...
        $this->seedEntireDatabase();

        $comments_model = app()->make(CommentsContract::class);
        $comments = $comments_model->fetchAllCommentsFromPost(1);
        $this->assertEquals(count($comments), '1', 'There should be 1 linked comment for this post');
    }

    public function test_creating_a_comment()
    {
        $comments_model = app()->make(CommentsContract::class);
        $comment = $comments_model->createComment(1, 1, 'This is a test comment.');
        $this->assertEquals($comment, null, 'The comment should not have been added');

        // seed the DB now and try again...
        $this->seedEntireDatabase();
        $comment = $comments_model->createComment(1, 1, 'This is a test comment.');
        $this->assertNotEquals($comment, null, 'The comment should have been added');
    }

    public function test_updating_a_comment()
    {
        // seed the database...
        $this->seedEntireDatabase();

        $comments_model = app()->make(CommentsContract::class);
        $comment = $comments_model->updateComment(1, 'This is a new comment.');
        $this->assertNotEquals($comment, null, 'The comment should have been updated');
        $this->assertEquals($comment->content, 'This is a new comment.', 'The comment content has not been updated');
    }
}
