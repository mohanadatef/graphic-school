<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Backup the database';

    public function handle(): int
    {
        $this->info('Starting database backup...');

        $filename = 'backup_' . date('Y-m-d_His') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        // Create backups directory if it doesn't exist
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.host'),
            config('database.connections.mysql.database'),
            $path
        );

        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("Database backed up successfully: {$filename}");
            return Command::SUCCESS;
        }

        $this->error('Database backup failed!');
        return Command::FAILURE;
    }
}

