<?php

namespace App\LogWriters;

use App\Contracts\LogWriter;

class FileWriter implements LogWriter
{
    public function writeLogPieces(array $pieces) : bool
    {
        $file_path = base_path() . '/storage/logs/api-logs.txt';
        $result = file_put_contents($file_path, implode(' :: ' , $pieces) . "\n", FILE_APPEND);
        return $result !== false;
    }
}