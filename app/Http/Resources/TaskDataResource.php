<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskDataResource extends JsonResource
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
            'notified_at'  => !is_null($this->notified_at) ? $this->notified_at->format('Y-m-d H:i:s') : 'No notification sent yet',
            'company_data' => !is_null($this->company_data) ? $this->company_data : 'None',
            'error'        => !is_null($this->error) ? $this->error : 'None'
        ];
    }
}
