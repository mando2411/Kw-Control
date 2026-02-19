<?php

namespace App\Jobs;

use App\Imports\VoterCheck;
use App\Imports\VotersImport;
use App\Models\Election;
use App\Models\Family;
use App\Models\Group;
use App\Models\Selection;
use App\Models\User;
use App\Models\Voter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessVotersImportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 3600;

    public function __construct(
        private readonly int $userId,
        private readonly string $filePath,
        private readonly string $mode,
        private readonly int $electionId
    ) {
    }

    public function handle(): void
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);

        try {
            if ($this->mode === 'replace') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');

                try {
                    DB::table('contractor_voter')->truncate();
                    DB::table('group_voter')->truncate();
                    DB::table('election_voter')->truncate();
                    Voter::truncate();
                    Selection::truncate();
                    Family::truncate();
                    Group::truncate();
                } finally {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                }
            }

            $absolutePath = storage_path('app/' . ltrim($this->filePath, '/'));

            if ($this->mode === 'status') {
                $import = new VoterCheck($this->electionId);
                Excel::import($import, $absolutePath);
            } else {
                $import = new VotersImport($this->electionId);
                Excel::import($import, $absolutePath);
            }

            $summary = [
                'mode' => $this->mode,
                'total' => $import->getTotalRows(),
                'success' => $import->getSuccessCount(),
                'skipped' => $import->getSkippedCount(),
                'failed' => $import->getFailedCount(),
                'created' => $import->getCreatedCount(),
                'existing' => $import->getExistingCount(),
                'updated' => $import->getUpdatedCount(),
                'duplicate_skipped' => $import->getDuplicateSkippedCount(),
            ];

            $this->sendSuccessNotification($summary);
        } catch (\Throwable $exception) {
            report($exception);
            Log::error('ProcessVotersImportJob failed', [
                'user_id' => $this->userId,
                'election_id' => $this->electionId,
                'mode' => $this->mode,
                'file_path' => $this->filePath,
                'message' => $exception->getMessage(),
            ]);
            $this->sendFailureNotification();

            throw $exception;
        } finally {
            Storage::disk('local')->delete($this->filePath);
        }
    }

    private function sendSuccessNotification(array $summary): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            return;
        }

        $electionName = Election::query()->whereKey($this->electionId)->value('name');

        $body = sprintf(
            'اكتملت معالجة استيراد الناخبين%s. الإجمالي: %d، نجاح: %d، تحديث: %d، مكررات متخطاة: %d، فشل: %d.',
            $electionName ? ' لانتخابات ' . $electionName : '',
            (int) ($summary['total'] ?? 0),
            (int) ($summary['success'] ?? 0),
            (int) ($summary['updated'] ?? 0),
            (int) ($summary['duplicate_skipped'] ?? 0),
            (int) ($summary['failed'] ?? 0),
        );

        send_system_notification($user, [
            'title' => 'اكتملت معالجة الاستيراد',
            'body' => $body,
            'url' => url('/dashboard/import/voters/data?election=' . $this->electionId),
        ]);
    }

    private function sendFailureNotification(): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            return;
        }

        send_system_notification($user, [
            'title' => 'فشل معالجة الاستيراد',
            'body' => 'حدث خطأ أثناء معالجة ملف الاستيراد. يرجى مراجعة الملف والمحاولة مرة أخرى.',
            'url' => url('/dashboard'),
        ]);
    }
}
