<?php
namespace App\Http\Helpers\ExactImplementers;

use App\Http\Helpers\Interfaces\LogHelper as LogHelperInterface;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class LogHelperImpl implements LogHelperInterface
{
    protected Activity $activity;
    protected ?string $event = null;
    protected array $properties = [];
    protected ?string $message = null;
    protected ?array $causedBy = null;
    protected ?Model $performedOn = null;

    public function withEvent(string $event): LogHelperInterface
    {
        $this->event = $event;
        return $this;
    }

    public function withProperties(array $properties): LogHelperInterface
    {
        $this->properties = $properties;
        return $this;
    }

    public function withMessage(string $message = 'No message log'): LogHelperInterface
    {
        $this->message = $message;
        return $this;
    }

    public function causedBy(array $causer): LogHelperInterface
    {
        $this->causedBy = $causer;
        return $this;
    }

    public function performedOn(Model $model): LogHelperInterface
    {
        $this->performedOn = $model;
        return $this;
    }

    public function build(): void
    {
        $activity = activity();
        if ($this->performedOn instanceof Model && $this->performedOn->exists) {
            $activity->perFormedOn($this->performedOn);
        }
        if ($this->causedBy instanceof Model && $this->causedBy->exists) {
            $activity->causedBy($this->causedBy);
        }
        if ($this->event != null) {
            $activity->event($this->event);
        }
        if (count($this->properties) > 0) {
            $activity->withProperties($this->properties);
        }
        $activity->log($this->message);
    }

    public function cleanLogs(): void
    {
        Activity::query()->delete();
        Log::info('All activity logs have been cleared.');
    }
}
