<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use stdClass;

interface Base
{
    public function fetchAllEntries() : Collection;
    /**
     * @return mixed Either null on failure, or stdClass of the fetched entry.
     */
    public function fetchSingleEntry(int $id) : ?stdClass;
}