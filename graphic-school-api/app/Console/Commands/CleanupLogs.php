<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupLogs extends Command
{
    protected $signature = 'logs:cleanup {--days=14}';
    protected $description = 'Clean up old log files';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days);
        $logPath = storage_path('logs');
        $deleted = 0;

        $files = File::glob($logPath . '/*.log');

        foreach ($files as $file) {
            if (File::lastModified($file) < $cutoffDate->timestamp) {
                File::delete($file);
                $deleted++;
            }
        }

        $this->info("Cleaned up {$deleted} log file(s) older than {$days} days.");
        return Command::SUCCESS;
    }
}

