<?php

namespace App\Contracts;

interface LogWriter
{
    public function writeLogPieces(array $pieces) : bool;
}