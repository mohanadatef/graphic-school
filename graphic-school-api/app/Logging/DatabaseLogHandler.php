<?php

namespace App\Logging;

use App\Models\ApplicationLog;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;

class DatabaseLogHandler
{
    /**
     * Customize the given logger instance.
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('database');
        $logger->pushHandler(new class extends AbstractProcessingHandler {
            protected function write(LogRecord $record): void
            {
                try {
                    $context = $record->context;
                    $user = auth()->user();
                    
                    ApplicationLog::create([
                        'level' => $record->level->getName(),
                        'message' => $record->message,
                        'context' => !empty($context) ? $context : null,
                        'channel' => $record->channel,
                        'user_id' => $user?->id,
                        'ip_address' => request()?->ip(),
                        'user_agent' => request()?->userAgent(),
                        'url' => request()?->fullUrl(),
                        'method' => request()?->method(),
                    ]);
                } catch (\Exception $e) {
                    // Fail silently to prevent logging errors from breaking the app
                }
            }
        });

        return $logger;
    }
}

