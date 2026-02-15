<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredStatementExports extends Command
{
    protected $signature = 'statement-exports:cleanup';

    protected $description = 'Delete expired generated statement export files from storage.';

    public function handle(): int
    {
        $ttlHours = max(1, (int) config('statement_exports.file_ttl_hours', 24));
        $threshold = now()->subHours($ttlHours)->timestamp;

        $disk = Storage::disk('public');
        $files = $disk->allFiles('exports/statements');

        $deleted = 0;

        foreach ($files as $file) {
            try {
                $lastModified = (int) $disk->lastModified($file);
                if ($lastModified > 0 && $lastModified < $threshold) {
                    $disk->delete($file);
                    $deleted++;
                }
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        $this->info('Expired statement export files deleted: ' . $deleted);

        return self::SUCCESS;
    }
}
