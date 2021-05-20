<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\Base as BaseContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use stdClass;

class Base implements BaseContract
{
    protected $model;
    protected $legal_create_update_keys;
    public function __construct(Model $model, array $legal_create_update_keys = [])
    {
        $this->model = $model;
        $this->legal_create_update_keys = $legal_create_update_keys;
    }

    protected function filterAttributeKeys($attributes)
    {
        $attribute_keys = array_keys($attributes);
        foreach ($attribute_keys as $key) {
            if (!in_array($key, $this->legal_create_update_keys)) {
                unset($attributes[$key]);
            }
        }
        return $attributes;
    }

    public function fetchAllEntries() : Collection
    {
        return $this->model->all();
    }

    public function fetchSingleEntry(int $id) : ?stdClass
    {
        $entry = $this->model->find($id);
        if (!$entry) {
            return null;
        }
        return json_decode( json_encode( $entry->toArray() ) );
    }

    public function createEntry(array $attributes) : ?stdClass
    {
        $entry = false;
        try {
            $attributes = $this->filterAttributeKeys($attributes);
            $entry = $this->model->create($attributes);
        }
        catch (QueryException $e) {
            return null;
        }
        return json_decode( json_encode( $entry->toArray() ) );
    }

    public function updateEntry(int $entry_id, array $attributes) : ?stdClass
    {
        $entry = $this->model->find($entry_id);
        if (!$entry) {
            return null;
        }

        try {
            $attributes = $this->filterAttributeKeys($attributes);
            foreach ($attributes as $attribute_key => $attribute_value) {
                $entry->{$attribute_key} = $attribute_value;
            }
            $entry->save();
        }
        catch (QueryException $e) {
            return null;
        }

        return json_decode( json_encode( $entry->toArray() ) );
    }

    public function removeEntry(int $entry_id) : void
    {
        $entry = $this->model->find($entry_id);
        if ($entry) {
            $entry->delete();
        }
    }

    public function valueIsUnique(string $value, string $column, int $ignore_id = 0) : bool
    {
        $row_count = $this->model
            ->where($column, $value)
            ->where('id', '!=', $ignore_id)
            ->count();
        return $row_count == 0;
    }

    public function valueExists(string $value, string $column) : bool
    {
        $row_count = $this->model
            ->where($column, $value)
            ->count();
        return $row_count > 0;
    }
}