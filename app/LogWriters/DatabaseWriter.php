<?php

namespace App\LogWriters;

use App\Contracts\LogWriter;
use App\Repositories\Contracts\ApiLogs as ApiLogsContract;

class DatabaseWriter implements LogWriter
{
    private $api_logs_contract;

    public function __construct(ApiLogsContract $api_logs_contract)
    {
        $this->api_logs_contract = $api_logs_contract;
    }

    public function writeLogPieces(array $pieces) : bool
    {
        $entry = $this->api_logs_contract->createEntry(['logs' => json_encode($pieces)]);
        return !!$entry;
    }
}