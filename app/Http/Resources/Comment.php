<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Comment extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $return_array = [
            'comment_id' => $this->id,
            'content' => $this->content
        ];

        if ($this->users) {
            $return_array['user'] = new User($this->users);
        }

        return $return_array;
    }
}
