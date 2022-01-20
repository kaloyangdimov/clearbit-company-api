<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'status'      => $this->status,
            'notified_at' => !is_null($this->notified_at) ? $this->notified_at->format('Y-m-d H:i:s') : 'No notification sent yet',
            'errors'      => !is_null($this->error) ? $this->error : 'None'
        ];
    }
}
