@php
    $user = auth()->user();
    $election = $user?->election;
    $isAdmin = $user && $user->hasRole('Administrator');
@endphp

<div class="home-modern-mode" id="homeModernRoot">
    <div class="container hm-shell">
        <div class="hm-hero hm-anim hm-hero-anim">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="hm-avatar">
                        <img src="{{ $user?->image }}" alt="{{ $user?->name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('assets/admin/images/users/user-placeholder.png') }}'" />
                        <span class="hm-avatar-ring" aria-hidden="true"></span>
                    </div>
                    <div>
                        <div class="hm-pill">
                            <i class="bi bi-stars"></i>
                            الواجهة الحديثة
                        </div>
                        <h1 class="hm-title mt-2 mb-1">مرحباً {{ $user?->name }}</h1>
                        <div class="hm-sub">
                            {{ $election ? ('انتخابات: ' . $election->name) : 'جاهز للعمل — لوحة تحكم أسرع وأوضح.' }}
                        </div>
                    </div>
                </div>
                <div class="hm-status">
                    <div class="hm-status-label">الحالة</div>
                    <div class="hm-status-value">
                        <span class="hm-dot" aria-hidden="true"></span>
                        آمن وجاهز
                    </div>
                </div>
            </div>

            @if (($user && $user->candidate()->exists()) || ($election && !$isAdmin))
                <div class="hm-hero-mini-row">
                    @if ($user && $user->candidate()->exists())
                        <div class="hm-candidate hm-candidate-mini hm-anim hm-card-anim" style="--hm-delay: 90ms;">
                            <div class="hm-candidate-title">الملف</div>
                            <div class="hm-candidate-sub">مرشح — {{ $election?->name }}</div>
                        </div>
                    @endif

                    @if ($election && !$isAdmin)
                        <div class="hm-section hm-section-compact hm-section-mini hm-anim hm-card-anim" style="--hm-delay: 120ms;">
                            <div class="hm-section-title">
                                <i class="bi bi-hourglass-split"></i>
                                الوقت المتبقي
                            </div>
                            <div class="hm-card hm-countdown hm-countdown-compact" id="hmCountdown">
                                <input type="hidden" id="hmStartDate" value="{{ \Carbon\Carbon::parse($election->start_date)->format('Y-m-d') }}">
                                <input type="hidden" id="hmStartTime" value="{{ \Carbon\Carbon::parse($election->start_time)->format('H:i:s') }}">
                                <input type="hidden" id="hmEndDate" value="{{ \Carbon\Carbon::parse($election->end_date)->format('Y-m-d') }}">
                                <input type="hidden" id="hmEndTime" value="{{ \Carbon\Carbon::parse($election->end_time)->format('H:i:s') }}">

                                <div class="hm-count-inline" aria-label="الوقت المتبقي">
                                    <div class="hm-count-item">
                                        <span class="hm-count-cap">اليوم</span>
                                        <span class="hm-count-num" id="hmDays">0</span>
                                    </div>
                                    <span class="hm-count-sep" aria-hidden="true">:</span>
                                    <div class="hm-count-item">
                                        <span class="hm-count-cap">الساعة</span>
                                        <span class="hm-count-num" id="hmHours">0</span>
                                    </div>
                                    <span class="hm-count-sep" aria-hidden="true">:</span>
                                    <div class="hm-count-item">
                                        <span class="hm-count-cap">الدقيقة</span>
                                        <span class="hm-count-num" id="hmMinutes">0</span>
                                    </div>
                                    <span class="hm-count-sep" aria-hidden="true">:</span>
                                    <div class="hm-count-item">
                                        <span class="hm-count-cap">الثانية</span>
                                        <span class="hm-count-num" id="hmSeconds">0</span>
                                    </div>
                                </div>

                                <div class="hm-sub mt-2" id="hmCountdownHint"></div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="hm-section hm-anim hm-card-anim" style="--hm-delay: 200ms;">
            <div class="hm-section-title">
                <i class="bi bi-grid-1x2"></i>
                المتعهدين والمضامين
            </div>
            <div class="hm-actions">
                @can('statement.show')
                    <a class="hm-action" href="{{ route('dashboard.statement.show') }}">
                        <span class="hm-action-icon"><i class="bi bi-table"></i></span>
                        <span class="hm-action-title">العرض الشامل</span>
                        <span class="hm-action-sub">قائمة كاملة</span>
                    </a>
                @endcan
                @can('madameen')
                    <a class="hm-action" href="{{ route('dashboard.madameen') }}">
                        <span class="hm-action-icon"><i class="bi bi-list-check"></i></span>
                        <span class="hm-action-title">المضامين</span>
                        <span class="hm-action-sub">إدارة المضامين</span>
                    </a>
                @endcan
                @can('contractors.list')
                    <a class="hm-action" href="{{ route('dashboard.contractors.index') }}">
                        <span class="hm-action-icon"><i class="bi bi-person-check"></i></span>
                        <span class="hm-action-title">المتعهدين</span>
                        <span class="hm-action-sub">قائمة المتعهدين</span>
                    </a>
                @endcan
                @can('statement')
                    <a class="hm-action" href="{{ route('dashboard.statement') }}">
                        <span class="hm-action-icon"><i class="bi bi-clipboard-data"></i></span>
                        <span class="hm-action-title">كشوف</span>
                        <span class="hm-action-sub">الكشوف الأساسية</span>
                    </a>
                @endcan
                @can('statement.search')
                    <a class="hm-action" href="{{ route('dashboard.statement.search') }}">
                        <span class="hm-action-icon"><i class="bi bi-search"></i></span>
                        <span class="hm-action-title">بحث الكشوف</span>
                        <span class="hm-action-sub">بحث سريع</span>
                    </a>
                @endcan
            </div>
        </div>

        <div class="hm-section hm-anim hm-card-anim" style="--hm-delay: 260ms;">
            <div class="hm-section-title">
                <i class="bi bi-shield-check"></i>
                تحضير وفرز (لجان الانتخابات)
            </div>
            <div class="hm-actions">
                @can('candidates.list')
                    <a class="hm-action" href="{{ route('dashboard.rep-home') }}">
                        <span class="hm-action-icon"><i class="bi bi-person-badge"></i></span>
                        <span class="hm-action-title">المندوبين</span>
                        <span class="hm-action-sub">إدارة المندوبين</span>
                    </a>
                @endcan
                @can('committee.home')
                    <a class="hm-action" href="{{ route('dashboard.committee.home') }}">
                        <span class="hm-action-icon"><i class="bi bi-receipt"></i></span>
                        <span class="hm-action-title">اللجان</span>
                        <span class="hm-action-sub">إدارة اللجان</span>
                    </a>
                @endcan
                @can('sorting')
                    <a class="hm-action" href="{{ route('dashboard.sorting') }}">
                        <span class="hm-action-icon"><i class="bi bi-ui-checks-grid"></i></span>
                        <span class="hm-action-title">الفرز</span>
                        <span class="hm-action-sub">فرز النتائج</span>
                    </a>
                @endcan
                @can('attending')
                    <a class="hm-action" href="{{ route('dashboard.attending') }}">
                        <span class="hm-action-icon"><i class="bi bi-clipboard-check"></i></span>
                        <span class="hm-action-title">التحضير</span>
                        <span class="hm-action-sub">تحضير الناخبين</span>
                    </a>
                @endcan
                @if(isset($show_all_result) && $show_all_result === true)
                    <a class="hm-action" href="{{ route('all.results') }}">
                        <span class="hm-action-icon"><i class="bi bi-bar-chart"></i></span>
                        <span class="hm-action-title">النتائج العامة</span>
                        <span class="hm-action-sub">عرض شامل</span>
                    </a>
                @endif
                @can('candidates.index')
                    <a class="hm-action" href="{{ route('dashboard.results') }}">
                        <span class="hm-action-icon"><i class="bi bi-graph-up"></i></span>
                        <span class="hm-action-title">النتائج</span>
                        <span class="hm-action-sub">نتائج المرشحين</span>
                    </a>
                @endcan
                @can('statistics')
                    <a class="hm-action" href="{{ route('dashboard.statistics') }}">
                        <span class="hm-action-icon"><i class="bi bi-clipboard2-data"></i></span>
                        <span class="hm-action-title">إحصائيات</span>
                        <span class="hm-action-sub">لوحات مؤشرات</span>
                    </a>
                @endcan
                @can('reports.create')
                    <a class="hm-action" href="{{ route('dashboard.reports.create') }}">
                        <span class="hm-action-icon"><i class="bi bi-file-earmark-text"></i></span>
                        <span class="hm-action-title">التقارير</span>
                        <span class="hm-action-sub">إنشاء تقرير</span>
                    </a>
                @endcan
            </div>
        </div>

        @if ($isAdmin)
            <div class="hm-section hm-anim hm-card-anim" style="--hm-delay: 320ms;">
                <div class="hm-section-title">
                    <i class="bi bi-gear"></i>
                    أدوات الموقع
                </div>
                <div class="hm-actions">
                    <a class="hm-action" href="{{ route('dashboard.delete') }}">
                        <span class="hm-action-icon"><i class="bi bi-trash"></i></span>
                        <span class="hm-action-title">المحذوفين</span>
                        <span class="hm-action-sub">إدارة الحذف</span>
                    </a>
                    <a class="hm-action" href="{{ route('dashboard.history') }}">
                        <span class="hm-action-icon"><i class="bi bi-clock-history"></i></span>
                        <span class="hm-action-title">سجل الموقع</span>
                        <span class="hm-action-sub">متابعة الأحداث</span>
                    </a>
                    <a class="hm-action" href="{{ route('dashboard.cards', $data=null) }}">
                        <span class="hm-action-icon"><i class="bi bi-people"></i></span>
                        <span class="hm-action-title">الكوادر واللجان</span>
                        <span class="hm-action-sub">إدارة الكوادر</span>
                    </a>
                </div>
            </div>

            <div class="hm-section hm-anim hm-card-anim" style="--hm-delay: 380ms;">
                <div class="hm-section-title">
                    <i class="bi bi-broadcast"></i>
                    المتواجدين الآن
                </div>
                <div class="hm-card p-0 hm-table">
                    <div class="hm-table-head">
                        <div>الاسم</div>
                        <div>آخر ظهور</div>
                    </div>
                    <div class="hm-table-body">
                        @php
                            $users = \App\Models\User::where('creator_id', $user->id)->take(10)->get();
                        @endphp
                        @forelse ($users as $u)
                            @if ($u->isOnline() || $u->isOffline())
                                <div class="hm-row">
                                    <div class="hm-row-name">
                                        <span class="hm-presence {{ $u->isOnline() ? 'on' : 'off' }}" aria-hidden="true"></span>
                                        <span>{{ $u->name }}</span>
                                    </div>
                                    <div class="hm-row-time">
                                        @if ($u->isOffline())
                                            {{ $u->LoginTime($u->last_active_at) }}
                                        @else
                                            الآن
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="p-3 hm-sub">لا توجد بيانات حالياً.</div>
                        @endforelse
                    </div>
                </div>
                @can('import-voters')
                    <div class="hm-section hm-anim hm-card-anim" style="--hm-delay: 430ms;">
                        <div class="hm-section-title">
                            <i class="bi bi-upload"></i>
                            استيراد الناخبين
                        </div>
                        <div data-voters-import-slot="modern"></div>
                    </div>
                @endcan
            </div>
        @else
            @can('import-voters')
                <div class="hm-section hm-anim hm-card-anim" style="--hm-delay: 320ms;">
                    <div class="hm-section-title">
                        <i class="bi bi-upload"></i>
                        استيراد الناخبين
                    </div>
                    <div data-voters-import-slot="modern"></div>
                </div>
            @endcan
        @endif
    </div>
</div>
