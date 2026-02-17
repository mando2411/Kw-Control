<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $candidate->user?->name ?? 'المرشح' }}</title>
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/vendors/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/vendors/font-awesome.css') }}">
</head>
<body>
<div class="candidate-public-page" dir="rtl">
    @php
        $name = $candidate->user?->name ?? 'مرشح';
        $avatar = $candidate->user?->image ?: ('https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=0ea5e9&color=fff&size=512');
        $banner = $candidate->banner ?: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1600&q=80';
        $contractorsCount = (int) ($candidate->user?->contractors_count ?? 0);
        $representativesCount = (int) ($candidate->user?->representatives_count ?? 0);
        $maxContractors = max(0, (int) ($candidate->max_contractor ?? 0));
        $maxRepresentatives = max(0, (int) ($candidate->max_represent ?? 0));
        $contractorsPercent = $maxContractors > 0 ? min(100, (int) round(($contractorsCount / $maxContractors) * 100)) : 0;
        $representativesPercent = $maxRepresentatives > 0 ? min(100, (int) round(($representativesCount / $maxRepresentatives) * 100)) : 0;
    @endphp

    <section class="candidate-public-hero" style="background-image: url('{{ $banner }}')">
        <div class="overlay"></div>
        <div class="container candidate-public-hero__inner">
            <div class="candidate-public-avatar" style="background-image: url('{{ $avatar }}')"></div>
            <h1 class="candidate-public-name">{{ $name }}</h1>
            <div class="candidate-public-campaign">
                <i class="fa fa-check-circle"></i>
                <span>{{ $candidate->election?->name ?? 'حملة غير محددة' }}</span>
            </div>
        </div>
    </section>

    <section class="container candidate-public-body">
        <div class="public-metrics-card">
            <h4 class="title">ملف المرشح</h4>
            <p class="subtitle">صفحة عامة للعرض فقط</p>

            <div class="metric-row">
                <div class="metric-head">
                    <span>المتعهدين</span>
                    <strong>{{ $contractorsCount }}/{{ $maxContractors }}</strong>
                </div>
                <div class="metric-bar"><span style="width: {{ $contractorsPercent }}%"></span></div>
            </div>

            <div class="metric-row">
                <div class="metric-head">
                    <span>المناديب</span>
                    <strong>{{ $representativesCount }}/{{ $maxRepresentatives }}</strong>
                </div>
                <div class="metric-bar"><span style="width: {{ $representativesPercent }}%"></span></div>
            </div>

            <div class="meta-grid">
                <div class="meta-item">
                    <span>تاريخ الإضافة</span>
                    <strong>{{ optional($candidate->created_at)->format('Y/m/d') ?? '—' }}</strong>
                </div>
                <div class="meta-item">
                    <span>المعرّف</span>
                    <strong>#{{ $candidate->id }}</strong>
                </div>
            </div>
        </div>
    </section>
</div>
<style>
    .candidate-public-page { background: #f8fafc; min-height: 100vh; }
    .candidate-public-hero {
        position: relative; min-height: 340px; background-size: cover; background-position: center;
    }
    .candidate-public-hero .overlay {
        position: absolute; inset: 0; background: linear-gradient(to top, rgba(2,6,23,.82), rgba(2,6,23,.38));
    }
    .candidate-public-hero__inner {
        position: relative; z-index: 2; min-height: 340px; display: flex; flex-direction: column;
        align-items: center; justify-content: center; gap: .7rem; color: #fff; text-align: center;
    }
    .candidate-public-avatar {
        width: 150px; height: 150px; border-radius: 50%; border: 5px solid #fff;
        background-size: cover; background-position: center;
        box-shadow: 0 12px 28px rgba(2,6,23,.35);
    }
    .candidate-public-name { font-size: 1.7rem; font-weight: 900; margin: 0; }
    .candidate-public-campaign {
        display: inline-flex; align-items: center; gap: .4rem; padding: .3rem .7rem; border-radius: 999px;
        background: rgba(37,99,235,.9); border: 1px solid rgba(255,255,255,.28); font-weight: 800;
    }
    .candidate-public-body { margin-top: -30px; padding-bottom: 30px; }
    .public-metrics-card {
        background: #fff; border: 1px solid rgba(148,163,184,.26); border-radius: 16px;
        box-shadow: 0 14px 30px rgba(15,23,42,.1); padding: 1rem;
    }
    .public-metrics-card .title { margin: 0; font-weight: 900; color: #0f172a; }
    .public-metrics-card .subtitle { margin: .2rem 0 1rem; color: #64748b; font-size: .9rem; }
    .metric-row { margin-bottom: .8rem; }
    .metric-head { display: flex; justify-content: space-between; font-weight: 800; margin-bottom: .35rem; }
    .metric-bar { height: 8px; border-radius: 999px; background: #e2e8f0; overflow: hidden; }
    .metric-bar span { display: block; height: 100%; background: linear-gradient(90deg, #22d3ee, #6366f1); }
    .meta-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: .7rem; margin-top: .8rem; }
    .meta-item { border: 1px solid rgba(148,163,184,.25); border-radius: 12px; padding: .6rem; background: #f8fafc; }
    .meta-item span { display: block; color: #64748b; font-size: .78rem; margin-bottom: .2rem; }
    .meta-item strong { color: #0f172a; font-weight: 900; }
    @media (max-width: 640px) {
        .candidate-public-avatar { width: 124px; height: 124px; }
        .candidate-public-name { font-size: 1.35rem; }
        .meta-grid { grid-template-columns: 1fr; }
    }
</style>
</body>
</html>
