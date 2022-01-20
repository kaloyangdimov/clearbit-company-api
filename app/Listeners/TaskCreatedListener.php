<?php

namespace App\Listeners;

use App\Events\TaskCreatedEvent;
use App\Jobs\ProcessTaskJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TaskCreatedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(TaskCreatedEvent $event)
    {
        ProcessTaskJob::dispatch($event->task);
    }
}
