<div class="home-modern-mode">
    <div class="container hm-shell">
        <div class="hm-hero">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="hm-pill">
                        <i class="bi bi-stars"></i>
                        الواجهة الحديثة
                    </div>
                    <h1 class="hm-title mt-3 mb-2">مرحباً {{ auth()->user()->name }}</h1>
                    <div class="hm-sub">نظرة سريعة وآمنة على لوحة التحكم — تصميم معزول بالكامل عن الشكل القديم.</div>
                </div>
                <div class="text-start" style="min-width: 220px;">
                    <div class="hm-sub">الحالة</div>
                    <div class="fw-bold" style="color: rgba(248, 250, 252, 0.92);">جاهز</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-12 col-md-4">
                <div class="hm-card p-3">
                    <div class="hm-card-title mb-2">مؤشر سريع</div>
                    <div class="hm-kpi">
                        <div class="value">—</div>
                        <div class="label">جاهز لربط بيانات الصفحة الحديثة لاحقاً</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="hm-card p-3">
                    <div class="hm-card-title mb-2">الأمان</div>
                    <div class="hm-sub">تصميم حديث مع قواعد عزل CSS لمنع تعارض Bootstrap أو الشكل القديم.</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="hm-card p-3">
                    <div class="hm-card-title mb-2">الأداء</div>
                    <div class="hm-sub">يتم تحميل CSS الحديث فقط عند اختيار "modern" عبر localStorage.</div>
                </div>
            </div>
        </div>
    </div>
</div>
