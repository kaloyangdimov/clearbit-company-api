<?php

namespace App\Http\Controllers;

use App\Events\TaskCreatedEvent;
use App\Http\Requests\CompanyDataRequest;
use App\Http\Requests\TaskDataRequest;
use App\Http\Requests\TaskProgressRequest;
use App\Http\Resources\TaskDataResource;
use App\Http\Resources\TaskProgressResource;
use App\Http\Resources\TaskResource;
use App\Http\Services\TaskService;
use Exception;

class TaskController extends Controller
{
    private $taskService;

    public function __construct(TaskService $service)
    {
        $this->taskService = $service;
    }

    public function requestCompanyData(CompanyDataRequest $request)
    {
        $task = $this->taskService->store($request->validated());
        event(new TaskCreatedEvent($task));

        return new TaskResource($task);
    }

    public function getTaskData(TaskDataRequest $request)
    {
        $task = $this->taskService->getTaskData($request->validated());

        return new TaskDataResource($task);
    }

    public function getTaskProgress(TaskProgressRequest $request)
    {
        $task = $this->taskService->getTaskData($request->validated());

        return new TaskProgressResource($task);
    }
}
