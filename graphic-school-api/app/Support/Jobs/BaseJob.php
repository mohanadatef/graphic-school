<?php

namespace App\Support\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Support\Jobs\JobLogger;
use Throwable;

abstract class BaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        JobLogger::dispatch(get_class($this), $this->toArray());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $jobId = $this->job->getJobId();
        JobLogger::start(get_class($this), $jobId);

        try {
            $this->process();
            JobLogger::success(get_class($this), $jobId);
        } catch (Throwable $e) {
            JobLogger::failure(get_class($this), $jobId, $e);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        $jobId = $this->job?->getJobId() ?? 'unknown';
        JobLogger::failure(get_class($this), $jobId, $exception);
    }

    /**
     * Process the job (to be implemented by child classes)
     */
    abstract protected function process(): void;

    /**
     * Convert job to array
     */
    protected function toArray(): array
    {
        return get_object_vars($this);
    }
}

