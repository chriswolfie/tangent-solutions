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

    /**
     * @return mixed Either null on failure, or stdClass of the created entry.
     */
    public function createEntry(array $attributes) : ?stdClass;

    /**
     * @return mixed Either null on failure, or stdClass of the created entry.
     */
    public function updateEntry(int $entry_id, array $attributes) : ?stdClass;

    public function removeEntry(int $entry_id) : void;
}