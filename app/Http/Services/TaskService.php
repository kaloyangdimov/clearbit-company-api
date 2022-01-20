<?php

namespace App\Http\Services;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\Task;

class TaskService extends Controller
{
    public function store(array $attributes)
    {
        $task = new Task();
        $task->criteria = json_encode($attributes);
        $task->user_id = auth()->id();

        if (!$task->save()) {
            throw new ApiException(__('custom.error_saving_task'), 500);
        }

        return $task;
    }

    public function getTaskData(array $attributes)
    {
        $task = Task::select('*')->where('user_id', auth()->id());

        if (isset($attributes['task_id'])) {
            $task->where('id', $attributes['task_id']);
        }

        if (isset($attributes['company_domain'])) {
            $task->whereJsonContains('criteria->company_domain', $attributes['company_domain']);
        }

        return $task->first();
    }
}
