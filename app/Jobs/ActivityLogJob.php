<?php

namespace App\Jobs;

use App\Enums\LogEvents;
use App\Enums\QueueBucket;
use App\Facades\SetLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class ActivityLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public LogEvents $event,
        public ?Model $causer = null,
        public ?Model $performedOn = null,
        public array $properties = [],
        public ?string $message = null
    ) {
        $this->onQueue(QueueBucket::LOGS->value);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $logBuilder = SetLog::withEvent($this->event);

        if ($this->causer) {
            $logBuilder->causedBy($this->causer);
        }

        if ($this->performedOn) {
            $logBuilder->performedOn($this->performedOn);
        }

        if (!empty($this->properties)) {
            $logBuilder->withProperties($this->properties);
        }

        if ($this->message) {
            $logBuilder->withMessage($this->message);
        }

        $logBuilder->build();
    }
}
