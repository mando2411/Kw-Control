<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تصدير الجدول</title>
    <style>
        html,
        body {
            direction: rtl;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 12px;
            color: #111827;
            text-align: right;
        }

        .report-header {
            border: 1px solid #0f172a;
            border-radius: 10px;
            padding: 14px;
            background: #f8fafc;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .report-header .right-col {
            float: right;
            width: 120px;
            text-align: center;
        }

        .report-header .left-col {
            margin-right: 140px;
            min-height: 104px;
            padding-right: 8px;
            direction: rtl;
            text-align: right;
            unicode-bidi: plaintext;
        }

        .candidate-avatar {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            border: 3px solid #1e293b;
            object-fit: cover;
            background: #e2e8f0;
        }

        .avatar-fallback {
            width: 96px;
            height: 96px;
            line-height: 92px;
            border-radius: 50%;
            border: 3px solid #1e293b;
            background: #cbd5e1;
            color: #0f172a;
            font-size: 32px;
            font-weight: 700;
            margin: 0 auto;
        }

        .candidate-name {
            margin: 0 0 8px;
            font-size: 25px;
            line-height: 1.25;
            color: #0f172a;
            font-weight: 700;
            text-align: right;
            direction: rtl;
        }

        .meta-line {
            margin: 4px 0;
            font-size: 15px;
            text-align: right;
            direction: rtl;
            unicode-bidi: plaintext;
        }

        .meta-label {
            font-weight: 700;
            color: #1f2937;
        }

        .meta-value {
            color: #0f172a;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 8px;
        }

        thead tr {
            background: #0f172a;
        }

        thead th {
            color: #ffffff;
            border: 1px solid #0f172a;
            padding: 8px 6px;
            font-size: 14px;
            text-align: center;
        }

        tbody td {
            border: 1px solid #cbd5e1;
            padding: 7px 6px;
            font-size: 13px;
            text-align: center;
            background: #ffffff;
        }

        tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .clearfix {
            clear: both;
        }

        @if(isset($mode) && $mode === 'pdf')
            .report-header {
                border-radius: 0;
                background: #ffffff;
            }
        @endif
    </style>
</head>

@php
    $reportUser = $reportUser ?? auth()->user();
    if ($reportUser instanceof \Illuminate\Support\Collection) {
        $reportUser = $reportUser->first();
    }

    $candidateType = $reportCandidateType ?? 'مرشح';
    $listName = $reportListName ?? null;
    $campaignName = $reportCampaignName ?? ($reportUser?->election?->name ?? 'غير محدد');

    $headers = ['#', 'name'];
    if (in_array('family', $columns ?? [])) {
        $headers[] = 'family';
    }
    if (in_array('committee', $columns ?? [])) {
        $headers[] = 'committee';
    }
    if (in_array('type', $columns ?? [])) {
        $headers[] = 'type';
    }
    if (in_array('age', $columns ?? [])) {
        $headers[] = 'age';
    }
    if (in_array('phone', $columns ?? [])) {
        $headers[] = 'phone';
    }
    if (in_array('region', $columns ?? [])) {
        $headers[] = 'region';
    }
    if (in_array('status', $columns ?? [])) {
        $headers[] = 'status';
    }
    if (in_array('madrasa', $columns ?? [])) {
        $headers[] = 'madrasa';
    }
    if (in_array('restricted', $columns ?? [])) {
        $headers[] = 'restricted';
    }
    if (in_array('created_at', $columns ?? [])) {
        $headers[] = 'created_at';
    }
    if (in_array('alsndok', $columns ?? [])) {
        $headers[] = 'alsndok';
    }

    $headers = isset($mode) && $mode === 'pdf' ? array_reverse($headers) : $headers;

    $rows = [];
    foreach ($voters as $i => $voter) {
        $row = ['#' => $i + 1, 'name' => $voter->name];

        if (in_array('family', $columns ?? [])) {
            $row['family'] = optional($voter->family)->name ?? 'لايوجد';
        }
        if (in_array('committee', $columns ?? [])) {
            $row['committee'] = optional($voter->committee)->name ?? 'لايوجد';
        }
        if (in_array('type', $columns ?? [])) {
            $row['type'] = $voter->type;
        }
        if (in_array('status', $columns ?? [])) {
            $row['status'] = $voter->status == 1
                ? 'تم التصويت في ' . optional($voter->updated_at)->format('Y/m/d')
                : 'لم يتم التصويت';
        }
        if (in_array('age', $columns ?? [])) {
            $row['age'] = $voter->age;
        }
        if (in_array('phone', $columns ?? [])) {
            $row['phone'] = $voter->phone2;
        }
        if (in_array('alsndok', $columns ?? [])) {
            $row['alsndok'] = $voter->alsndok;
        }
        if (in_array('restricted', $columns ?? [])) {
            $row['restricted'] = $voter->restricted;
        }
        if (in_array('created_at', $columns ?? [])) {
            $row['created_at'] = $voter->created_at;
        }
        if (in_array('region', $columns ?? [])) {
            $row['region'] = $voter->alktaa . ' القطعة';
        }
        if (in_array('madrasa', $columns ?? [])) {
            $row['madrasa'] = $voter->committee?->school?->name ?? 'لا يوجد';
        }

        $rows[] = $row;
    }

    $imageBase64 = null;
    $imageMime = 'image/jpeg';

    $imageCandidates = [];
    if (is_object($reportUser) && method_exists($reportUser, 'getRawOriginal')) {
        $rawImage = (string) $reportUser->getRawOriginal('image');
        if ($rawImage !== '') {
            $imageCandidates[] = $rawImage;
        }
    }

    $imageUrl = (string) ($reportUser?->image ?? '');
    if ($imageUrl !== '') {
        $imageCandidates[] = $imageUrl;
    }

    foreach (array_unique(array_filter($imageCandidates)) as $candidateImagePath) {
        $parsedPath = parse_url($candidateImagePath, PHP_URL_PATH);
        $parsedPath = is_string($parsedPath) ? $parsedPath : $candidateImagePath;
        $parsedPath = '/' . ltrim($parsedPath, '/');

        $possiblePaths = [
            public_path(ltrim($parsedPath, '/')),
        ];

        if (str_starts_with($parsedPath, '/media-file/')) {
            $relativeMediaPath = ltrim(substr($parsedPath, strlen('/media-file/')), '/');
            $possiblePaths[] = storage_path('app/public/media/' . $relativeMediaPath);
            $possiblePaths[] = public_path('storage/media/' . $relativeMediaPath);
        }

        if (str_starts_with($parsedPath, '/storage/media/')) {
            $relativeMediaPath = ltrim(substr($parsedPath, strlen('/storage/media/')), '/');
            $possiblePaths[] = storage_path('app/public/media/' . $relativeMediaPath);
        }

        foreach ($possiblePaths as $filePath) {
            if (is_string($filePath) && $filePath !== '' && file_exists($filePath) && is_file($filePath)) {
                $imageContent = @file_get_contents($filePath);
                if ($imageContent !== false) {
                    if (function_exists('mime_content_type')) {
                        $detectedMime = @mime_content_type($filePath);
                        if (is_string($detectedMime) && str_starts_with($detectedMime, 'image/')) {
                            $imageMime = $detectedMime;
                        }
                    }

                    $imageBase64 = base64_encode($imageContent);
                    break 2;
                }
            }
        }
    }

    $nameInitial = mb_substr((string) ($reportUser?->name ?? 'م'), 0, 1);
@endphp

<body>
<div class="report-header">
    <div class="right-col">
        @if($imageBase64)
            <img class="candidate-avatar" src="data:{{ $imageMime }};base64,{{ $imageBase64 }}" alt="صورة المستخدم">
        @else
            <div class="avatar-fallback">{{ $nameInitial }}</div>
        @endif
    </div>

    <div class="left-col">
        <h1 class="candidate-name">{{ $reportUser?->name ?? 'مستخدم النظام' }}</h1>
        <p class="meta-line"><span class="meta-label">الصفة:</span> <span class="meta-value">{{ $candidateType }}</span></p>
        @if(!empty($listName))
            <p class="meta-line"><span class="meta-label">اسم القائمة:</span> <span class="meta-value">{{ $listName }}</span></p>
        @endif
        <p class="meta-line"><span class="meta-label">الحملة الانتخابية:</span> <span class="meta-value">{{ $campaignName }}</span></p>
    </div>

    <div class="clearfix"></div>
</div>

<table>
    <thead>
    <tr>
        @foreach($headers as $header)
            <th>{{ __('main.' . $header) }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $row)
        <tr>
            @foreach($headers as $header)
                <td>{{ $row[$header] }}</td>
            @endforeach
        </tr>
    @empty
        <tr>
            <td colspan="{{ count($headers) }}">لا يوجد بيانات</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
