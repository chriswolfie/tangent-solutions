<?php

namespace Tests\Feature;

use App\Contracts\LogWriter as LogWriterContract;
use App\LogWriters\DatabaseWriter;
use App\Repositories\Contracts\ApiLogs as ApiLogsContract;
use App\Repositories\Eloquent\ApiLogs as ApiLogsEloquent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\PersonalAssistant;
use Tests\TestCase;

class LoggerTest extends TestCase
{
    use RefreshDatabase,
    PersonalAssistant;

    protected function setUp(): void
    {
        parent::setUp();
        app()->bind(LogWriterContract::class, DatabaseWriter::class);
        app()->bind(ApiLogsContract::class, ApiLogsEloquent::class);
    }

    public function test_inspect_logs_writing()
    {
        $this->seedEntireDatabase();

        $this->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])->get('/api/v1/category');
        $this->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])->get('/api/v1/post/1/comment');
        $this->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])->get('/api/v1/post');
        $this->withHeaders([ 'accept' => 'application/json', 'content-type' => 'application/json' ])->get('/api/v1/user');

        $logger_model = app()->make(ApiLogsContract::class);
        $logs = $logger_model->fetchAllEntries();

        $this->assertEquals(count($logs), 4, 'The logger does not seem to be recording the logs correctly, there should be 4 log entries');
    }
}
