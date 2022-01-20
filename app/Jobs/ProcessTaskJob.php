<?php

namespace App\Jobs;

use App\Http\Services\ClearbitService;
use App\Models\Task;
use App\Notifications\DomainTaskCompletedNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $service;
    private $task;
    public $tries = 3;

    const STATUS_SUCCESS = 200;
    const STATUS_QUEUE = 202;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INVALID_DOMAIN = 422;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->service = new ClearbitService();
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = $this->service->domainLookup(json_decode($this->task->criteria, true));

        switch ($response->status()) {
            case self::STATUS_QUEUE:
                $this->task->status = Task::STATUS_IN_PROGRESS;
                $this->fail(); // fail job if target api requires timeout
                $this->release(30); // wait 30 seconds and run again
            break;
            case self::STATUS_SUCCESS:
                $this->task->status = Task::STATUS_COMPLETED;
                $this->task->company_data = $response->json();
                $this->task->user->notify(new DomainTaskCompletedNotification());
                $this->task->notified_at = now();
            break;
            case self::STATUS_NOT_FOUND:
            case self::STATUS_INVALID_DOMAIN:
                $this->task->status = Task::STATUS_FAILED;
                $this->task->user->notify(new DomainTaskCompletedNotification());
                $this->task->notified_at = now();
                $this->task->error = $response->json();
            break;
        }

        $this->task->save();
    }
}
