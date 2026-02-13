<?php

namespace App\Jobs;

use App\Exports\VotersExport;
use App\Models\User;
use App\Models\Voter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProcessStatementExportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 3600;

    public function __construct(
        private readonly int $userId,
        private readonly string $type,
        private readonly array $voterIds,
        private readonly array $columns,
    ) {
    }

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            return;
        }

        try {
            $voters = Voter::whereIn('id', $this->voterIds)->get();

            if ($voters->isEmpty()) {
                $this->sendFailureNotification($user, 'لا يوجد ناخبون صالحون لإعداد الملف.');
                return;
            }

            $directory = 'exports/statements/' . $this->userId;
            Storage::disk('public')->makeDirectory($directory);

            $timestamp = now()->format('Ymd_His');
            $type = strtoupper($this->type) === 'EXCEL' ? 'Excel' : 'PDF';

            if ($type === 'Excel') {
                $relativePath = $directory . '/statement_' . $timestamp . '.xlsx';
                Excel::store(new VotersExport($voters, $this->columns), $relativePath, 'public');
            } else {
                $relativePath = $directory . '/statement_' . $timestamp . '.pdf';
                $html = view('dashboard.exports.pdf', [
                    'voters' => $voters,
                    'mode' => 'pdf',
                    'columns' => $this->columns,
                ])->toArabicHTML();

                $pdf = Pdf::loadHTML($html)
                    ->setOption('isHtml5ParserEnabled', true)
                    ->setOption('isRemoteEnabled', true);

                $pdf->setPaper([0, 0, 841.89, 1190.55], 'portrait');
                Storage::disk('public')->put($relativePath, $pdf->output());
            }

            $encodedPath = rtrim(strtr(base64_encode($relativePath), '+/', '-_'), '=');
            $linkTtlMinutes = max(1, (int) config('statement_exports.download_link_ttl_minutes', 20));
            $fileTtlHours = max(1, (int) config('statement_exports.file_ttl_hours', 24));

            $downloadExpiresAt = now()->addMinutes($linkTtlMinutes);
            $fileExpiresAt = now()->addHours($fileTtlHours);
            $downloadToken = (string) Str::uuid();

            Cache::put(
                'statement-export:download-token:' . $this->userId . ':' . $downloadToken,
                [
                    'path' => $relativePath,
                    'created_at' => now()->toDateTimeString(),
                ],
                $downloadExpiresAt
            );

            $downloadUrl = URL::temporarySignedRoute(
                'dashboard.statement.export-download',
                $downloadExpiresAt,
                [
                    'path' => $encodedPath,
                    'dl' => $downloadToken,
                ]
            );

            send_system_notification($user, [
                'title' => 'الملف جاهز للتنزيل',
                'body' => 'اكتمل تجهيز ملف ' . $type . ' الخاص بالكشوف. يمكنك تنزيله الآن من الزر المخصص.',
                'url' => route('dashboard.notifications.page'),
                'action_label' => 'تنزيل الملف',
                'action_url' => $downloadUrl,
                'action_expires_at' => $downloadExpiresAt->toDateTimeString(),
                'file_expires_at' => $fileExpiresAt->toDateTimeString(),
                'download_token' => $downloadToken,
            ]);
        } catch (\Throwable $exception) {
            report($exception);
            $this->sendFailureNotification($user, 'حدث خطأ أثناء تجهيز ملف الكشوف. حاول مرة أخرى.');
            throw $exception;
        }
    }

    private function sendFailureNotification(User $user, string $message): void
    {
        send_system_notification($user, [
            'title' => 'فشل تجهيز ملف الكشوف',
            'body' => $message,
            'url' => route('dashboard.notifications.page'),
        ]);
    }
}
