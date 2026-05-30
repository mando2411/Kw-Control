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

    private int $userId;
    private string $type;
    private array $voterIds;
    private array $columns;
    private string $source;
    private array $filters;

    public function __construct(
        int $userId,
        string $type,
        array $voterIds,
        array $columns,
        string $source = 'statement_search',
        array $filters = [],
    ) {
        $this->userId = $userId;
        $this->type = $type;
        $this->voterIds = $voterIds;
        $this->columns = $columns;
        $this->source = $source;
        $this->filters = $filters;
    }

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            return;
        }

        try {
            $query = Voter::whereIn('id', $this->voterIds);
            $gender = isset($this->filters['gender']) ? (string) $this->filters['gender'] : 'all';
            $attendance = isset($this->filters['attendance']) ? (string) $this->filters['attendance'] : 'all';

            if ($gender !== 'all') {
                $query->where('type', $gender);
            }

            if ($attendance === 'present') {
                $query->where('status', 1);
            } elseif ($attendance === 'absent') {
                $query->where('status', 0);
            }

            $voters = $query->get();

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
                $reportUser = $user->loadMissing(['candidate.listLeader', 'election']);
                $reportHeaderData = $this->buildReportHeaderData($reportUser);

                $html = view('dashboard.exports.pdf', [
                    'voters' => $voters,
                    'mode' => 'pdf',
                    'columns' => $this->columns,
                    ...$reportHeaderData,
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

            $isContractorsExport = $this->source === 'contractors';
            $title = $isContractorsExport
                ? 'ملف كشوف المتعهدين جاهز للتنزيل'
                : 'الملف جاهز للتنزيل';

            $body = $isContractorsExport
                ? 'اكتمل تجهيز ملف ' . $type . ' الخاص بكشوف المتعهدين. يمكنك تنزيله الآن من الزر المخصص.'
                : 'اكتمل تجهيز ملف ' . $type . ' الخاص بالكشوف. يمكنك تنزيله الآن من الزر المخصص.';

            send_system_notification($user, [
                'title' => $title,
                'body' => $body,
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

    private function buildReportHeaderData($reportUser): array
    {
        if ($reportUser instanceof \Illuminate\Support\Collection) {
            $reportUser = $reportUser->first();
        }

        $candidate = null;
        if (is_object($reportUser) && isset($reportUser->candidate)) {
            $candidateRelation = $reportUser->candidate;

            if ($candidateRelation instanceof \Illuminate\Support\Collection) {
                $candidate = $candidateRelation->firstWhere('election_id', $reportUser?->election_id)
                    ?? $candidateRelation->first();
            } else {
                $candidate = $candidateRelation;
            }
        }

        if ($candidate instanceof \Illuminate\Support\Collection) {
            $candidate = $candidate->firstWhere('election_id', $reportUser?->election_id)
                ?? $candidate->first();
        }

        $candidateTypeValue = is_object($candidate) ? ($candidate->candidate_type ?? null) : null;
        $listLeaderCandidateId = is_object($candidate) ? ($candidate->list_leader_candidate_id ?? null) : null;

        $candidateType = 'مرشح';
        if ($candidateTypeValue === 'list_leader') {
            $candidateType = 'مرشح رئيس قائمة';
        } elseif (!is_null($listLeaderCandidateId)) {
            $candidateType = 'مرشح عضو قائمة';
        } elseif (!$candidate && $reportUser && method_exists($reportUser, 'hasRole') && $reportUser->hasRole('متعهد')) {
            $candidateType = 'متعهد';
        }

        $listName = null;
        if ($candidateTypeValue === 'list_leader') {
            $listName = $candidate->list_name ?? null;
        } elseif (!is_null($listLeaderCandidateId)) {
            $listName = optional($candidate?->listLeader)->list_name;
        }

        $campaignName = $reportUser?->election?->name
            ?? $candidate?->election?->name
            ?? 'غير محدد';

        return [
            'reportUser' => $reportUser,
            'reportCandidateType' => $candidateType,
            'reportListName' => $listName,
            'reportCampaignName' => $campaignName,
        ];
    }

    private function sendFailureNotification(User $user, string $message): void
    {
        $title = $this->source === 'contractors'
            ? 'فشل تجهيز كشوف المتعهدين'
            : 'فشل تجهيز ملف الكشوف';

        send_system_notification($user, [
            'title' => $title,
            'body' => $message,
            'url' => route('dashboard.notifications.page'),
        ]);
    }
}
