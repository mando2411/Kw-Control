<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $candidate->user?->name ?? 'المرشح' }}</title>
    <link rel="stylesheet" href="{{ asset('assets/site/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/site/css/all.min.css') }}">
</head>
<body>
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

<div class="candidate-public-page" dir="rtl">
    <header class="social-topbar">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="brand">الملف العام</div>
            <div class="meta">
                <span><i class="fa fa-calendar"></i> {{ optional($candidate->created_at)->format('Y/m/d') ?? '—' }}</span>
            </div>
        </div>
    </header>

    <section class="profile-cover-wrap container">
        <div class="profile-cover" style="background-image: url('{{ $banner }}')"></div>

        <div class="profile-head card">
            <div class="profile-head__main">
                <div class="profile-avatar" style="background-image: url('{{ $avatar }}')"></div>
                <div class="profile-identity">
                    <h1>{{ $name }}</h1>
                    <div class="campaign-pill">
                        <i class="fa fa-check-circle"></i>
                        <span>{{ $candidate->election?->name ?? 'حملة غير محددة' }}</span>
                    </div>
                    <div class="profile-actions">
                        <button type="button" class="btn btn-primary"><i class="fa fa-user-plus me-1"></i>طلب الانضمام كمتعهد</button>
                        <button type="button" class="btn btn-light"><i class="fa fa-envelope me-1"></i>مراسلة</button>
                    </div>
                </div>
            </div>
        </div>

        <nav class="profile-tabs card">
            <a href="javascript:;" class="active">المنشورات</a>
            <a href="javascript:;">المعلومات</a>
            <a href="javascript:;">الصور</a>
            <a href="javascript:;">الفريق</a>
        </nav>
    </section>

    <section class="container social-body">
        <aside class="left-col">
            <div class="card social-card intro-card">
                <h4>نبذة</h4>
                <p>هذه صفحة عامة للمرشح تعرض بيانات الحملة والمؤشرات الأساسية بشكل مبسط.</p>
                <ul>
                    <li><i class="fa fa-flag"></i> {{ $candidate->election?->name ?? 'حملة غير محددة' }}</li>
                    <li><i class="fa fa-id-badge"></i> رقم المرشح: #{{ $candidate->id }}</li>
                </ul>
            </div>

            <div class="card social-card metrics-card">
                <h4>المؤشرات</h4>
                <div class="metric-row">
                    <div class="metric-head">
                        <span>المتعهدين</span>
                        <strong>{{ $contractorsCount }}/{{ $maxContractors }}</strong>
                    </div>
                    <div class="metric-bar"><span style="width: {{ $contractorsPercent }}%"></span></div>
                </div>

                <div class="metric-row mb-0">
                    <div class="metric-head">
                        <span>المناديب</span>
                        <strong>{{ $representativesCount }}/{{ $maxRepresentatives }}</strong>
                    </div>
                    <div class="metric-bar"><span style="width: {{ $representativesPercent }}%"></span></div>
                </div>
            </div>
        </aside>

        <main class="right-col">
            <article class="card social-card post-card">
                <div class="post-head">
                    <div class="mini-avatar" style="background-image: url('{{ $avatar }}')"></div>
                    <div>
                        <strong>{{ $name }}</strong>
                        <span>تحديث عام • {{ optional($candidate->created_at)->diffForHumans() }}</span>
                    </div>
                </div>
                <p class="post-text">الملف العام متاح الآن للعرض. يمكن متابعة المؤشرات الأساسية للحملة من هذه الصفحة.</p>
                <div class="post-media" style="background-image: url('{{ $banner }}')"></div>
            </article>

            <article class="card social-card post-card">
                <div class="post-head">
                    <div class="mini-avatar" style="background-image: url('{{ $avatar }}')"></div>
                    <div>
                        <strong>{{ $name }}</strong>
                        <span>حالة الفريق</span>
                    </div>
                </div>
                <div class="team-status-grid">
                    <div><small>المتعهدين الحاليين</small><strong>{{ $contractorsCount }}</strong></div>
                    <div><small>المناديب الحاليين</small><strong>{{ $representativesCount }}</strong></div>
                    <div><small>الحد الأقصى للمتعهدين</small><strong>{{ $maxContractors }}</strong></div>
                    <div><small>الحد الأقصى للمناديب</small><strong>{{ $maxRepresentatives }}</strong></div>
                </div>
            </article>
        </main>
    </section>
</div>

<style>
    body { background: #f0f2f5; }
    .candidate-public-page { min-height: 100vh; color: #1c1e21; }

    .social-topbar {
        background: #fff;
        border-bottom: 1px solid #dddfe2;
        height: 58px;
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .social-topbar .brand { font-size: 1.02rem; font-weight: 900; color: #1877f2; }
    .social-topbar .meta { font-size: .84rem; color: #65676b; }

    .profile-cover-wrap { max-width: 980px; }

    .profile-cover {
        height: 340px;
        background-size: cover;
        background-position: center;
        border-radius: 10px;
        border: 1px solid #dddfe2;
    }

    .profile-head {
        margin-top: -26px;
        border: 1px solid #dddfe2;
        border-radius: 0 0 10px 10px;
        background: #fff;
        padding: .8rem 1rem;
        display: block;
    }

    .profile-head__main {
        display: flex;
        flex-direction: row;
        align-items: flex-end;
        justify-content: flex-end;
        gap: .8rem;
        text-align: right;
        width: max-content;
        margin-left: auto;
    }

    .profile-avatar {
        width: 168px;
        height: 168px;
        border-radius: 50%;
        border: 5px solid #fff;
        background-size: cover;
        background-position: center;
        box-shadow: 0 1px 2px rgba(0,0,0,.2);
        margin-top: -70px;
    }

    .profile-identity h1 { margin: 0 0 .35rem; font-size: 1.75rem; font-weight: 800; color: #050505; }

    .campaign-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        background: #e7f3ff;
        color: #1b74e4;
        border-radius: 999px;
        padding: .2rem .6rem;
        font-size: .83rem;
        font-weight: 700;
    }

    .profile-identity {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .profile-actions {
        display: flex;
        justify-content: flex-start;
        gap: .45rem;
        margin-top: .65rem;
    }
    .profile-actions .btn { border-radius: 6px; font-weight: 700; font-size: .86rem; min-height: 36px; }

    .profile-tabs {
        margin-top: .55rem;
        border: 1px solid #dddfe2;
        border-radius: 10px;
        padding: .25rem .45rem;
        display: flex;
        gap: .25rem;
        overflow-x: auto;
    }

    .profile-tabs a {
        text-decoration: none;
        color: #65676b;
        padding: .55rem .8rem;
        border-radius: 8px;
        font-weight: 700;
        white-space: nowrap;
    }

    .profile-tabs a.active {
        background: #e7f3ff;
        color: #1b74e4;
    }

    .social-body {
        max-width: 980px;
        margin-top: .85rem;
        display: grid;
        grid-template-columns: 340px minmax(0,1fr);
        gap: .9rem;
        align-items: start;
        padding-bottom: 20px;
    }

    .social-card {
        border: 1px solid #dddfe2;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 1px 2px rgba(0,0,0,.06);
        padding: .9rem;
    }

    .social-card h4 { margin: 0 0 .6rem; font-size: 1rem; font-weight: 800; }
    .intro-card p { margin: 0 0 .7rem; color: #65676b; font-size: .9rem; }
    .intro-card ul { list-style: none; padding: 0; margin: 0; display: grid; gap: .5rem; }
    .intro-card li { color: #1c1e21; font-size: .9rem; display: flex; align-items: center; gap: .45rem; }

    .metric-row { margin-bottom: .85rem; }
    .metric-head { display: flex; justify-content: space-between; margin-bottom: .32rem; font-weight: 700; }
    .metric-head span { color: #65676b; }
    .metric-bar { height: 8px; border-radius: 99px; background: #e4e6eb; overflow: hidden; }
    .metric-bar span { display: block; height: 100%; background: #1877f2; }

    .post-card + .post-card { margin-top: .9rem; }
    .post-head { display: flex; align-items: center; gap: .55rem; margin-bottom: .6rem; }
    .mini-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background-size: cover;
        background-position: center;
        border: 1px solid #dddfe2;
    }

    .post-head strong { display: block; font-size: .95rem; }
    .post-head span { color: #65676b; font-size: .78rem; }
    .post-text { margin: 0 0 .6rem; color: #1c1e21; }

    .post-media {
        height: 270px;
        border-radius: 8px;
        background-size: cover;
        background-position: center;
        border: 1px solid #dddfe2;
    }

    .team-status-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .55rem;
    }

    .team-status-grid div {
        border: 1px solid #dddfe2;
        border-radius: 8px;
        padding: .55rem;
        background: #f7f8fa;
    }

    .team-status-grid small {
        display: block;
        color: #65676b;
        font-size: .74rem;
        margin-bottom: .2rem;
    }

    .team-status-grid strong { font-size: 1rem; color: #050505; }

    @media (max-width: 991px) {
        .social-body { grid-template-columns: 1fr; }
        .profile-avatar { width: 138px; height: 138px; margin-top: -56px; }
        .profile-identity h1 { font-size: 1.45rem; }
    }

    @media (max-width: 640px) {
        .profile-cover { height: 230px; }
        .profile-head__main { width: 100%; }
        .profile-actions { width: 100%; }
        .profile-actions .btn { flex: 1; }
        .post-media { height: 200px; }
    }
</style>
</body>
</html>
