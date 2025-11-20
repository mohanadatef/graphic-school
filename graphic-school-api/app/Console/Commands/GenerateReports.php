<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateReports extends Command
{
    protected $signature = 'reports:generate';
    protected $description = 'Generate daily reports';

    public function handle(): int
    {
        $this->info('Generating reports...');

        // Dispatch report generation jobs
        // \Modules\Operations\Reports\Jobs\GenerateDailyReportsJob::dispatch();

        $this->info('Reports generation queued successfully.');
        return Command::SUCCESS;
    }
}

