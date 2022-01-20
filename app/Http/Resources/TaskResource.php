<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'message' => 'Task has been successfully stored. Please use the link below to track its progress. When completed you will receive an email. Do not forget your token.',
            'progress_link' => '/api/v1/getTaskProgress?task_id='.$this->id,
        ];
    }
}
