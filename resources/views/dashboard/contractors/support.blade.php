<!DOCTYPE html>
<html lang="ar" dir="rtl" class="ui-modern ui-light" data-ui-mode="modern" data-ui-color-mode="light" data-bs-theme="light">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>الدعم الفني | {{ $contractor->name }}</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/all.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/modern-theme-system.css') }}?v={{ filemtime(public_path('assets/css/modern-theme-system.css')) }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/dashboard-modern-fallback.css') }}?v={{ filemtime(public_path('assets/css/dashboard-modern-fallback.css')) }}">

    <style>
      :root {
        --support-radius: 1rem;
      }

      body {
        background: var(--ui-bg-secondary, #f8fafc);
        color: var(--ui-text-primary, #0f172a);
        min-height: 100vh;
      }

      .support-wrap {
        width: min(1180px, 95vw);
        margin: 1.2rem auto 1.5rem;
      }

      .support-hero {
        border-radius: calc(var(--support-radius) + 0.2rem);
        border: 1px solid color-mix(in srgb, var(--ui-border, #dbe3ef) 88%, transparent);
        background: linear-gradient(130deg,
          color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 16%, #ffffff),
          color-mix(in srgb, var(--ui-btn-secondary, #6366f1) 14%, #ffffff),
          color-mix(in srgb, var(--ui-bg-primary, #ffffff) 96%, transparent));
        box-shadow: 0 16px 35px rgba(2, 6, 23, 0.12);
        padding: clamp(1rem, 2vw, 1.5rem);
      }

      .support-hero__badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.76rem;
        font-weight: 800;
        color: var(--ui-btn-primary, #0ea5e9);
        background: color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 12%, transparent);
        border: 1px solid color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 36%, transparent);
        border-radius: 999px;
        padding: 0.24rem 0.62rem;
      }

      .support-hero__title {
        margin: 0.62rem 0 0;
        font-size: clamp(1.2rem, 2.6vw, 1.7rem);
        font-weight: 900;
      }

      .support-hero__desc {
        margin: 0.52rem 0 0;
        color: var(--ui-text-secondary, #475569);
        font-size: 0.95rem;
        font-weight: 600;
        line-height: 1.85;
      }

      .support-hero__actions {
        margin-top: 0.92rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
      }

      .support-hero__actions .btn {
        min-height: 2.45rem;
        padding-inline: 1rem;
        font-weight: 800;
        border-radius: 0.72rem;
      }

      .support-grid {
        margin-top: 0.9rem;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.72rem;
      }

      .support-card {
        border-radius: var(--support-radius);
        border: 1px solid color-mix(in srgb, var(--ui-border, #dbe3ef) 90%, transparent);
        background: var(--ui-bg-primary, #fff);
        box-shadow: 0 12px 28px rgba(2, 6, 23, 0.08);
        padding: 0.95rem;
      }

      .support-card__icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.62rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--ui-btn-primary, #0ea5e9);
        background: color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 12%, transparent);
        border: 1px solid color-mix(in srgb, var(--ui-btn-primary, #0ea5e9) 30%, transparent);
      }

      .support-card h6 {
        margin: 0.62rem 0 0;
        font-size: 1rem;
        font-weight: 900;
      }

      .support-card p {
        margin: 0.38rem 0 0;
        font-size: 0.86rem;
        color: var(--ui-text-secondary, #475569);
        line-height: 1.75;
      }

      .support-card a {
        margin-top: 0.62rem;
      }

      .support-section {
        margin-top: 0.9rem;
        border-radius: var(--support-radius);
        border: 1px solid color-mix(in srgb, var(--ui-border, #dbe3ef) 90%, transparent);
        background: var(--ui-bg-primary, #fff);
        box-shadow: 0 12px 28px rgba(2, 6, 23, 0.08);
        padding: 0.95rem;
      }

      .support-section__title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 900;
      }

      .support-steps {
        margin: 0.72rem 0 0;
        padding: 0;
        list-style: none;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.65rem;
      }

      .support-steps li {
        border-radius: 0.8rem;
        border: 1px solid color-mix(in srgb, var(--ui-border, #dbe3ef) 92%, transparent);
        background: color-mix(in srgb, var(--ui-bg-secondary, #f8fafc) 88%, transparent);
        padding: 0.72rem;
      }

      .support-steps li strong {
        display: block;
        font-size: 0.86rem;
        font-weight: 900;
        margin-bottom: 0.2rem;
      }

      .support-steps li span {
        font-size: 0.8rem;
        color: var(--ui-text-secondary, #475569);
      }

      .support-footer-note {
        margin-top: 0.72rem;
        text-align: center;
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--ui-text-secondary, #475569);
      }

      @media (max-width: 992px) {
        .support-grid,
        .support-steps {
          grid-template-columns: 1fr;
        }
      }
    </style>
  </head>
  <body>
    <div class="support-wrap">
      <div class="support-hero">
        <span class="support-hero__badge"><i class="fa fa-headset"></i> مركز الدعم الفني</span>
        <h1 class="support-hero__title">دعم احترافي سريع لمتعهد {{ $contractor->name }}</h1>
        <p class="support-hero__desc">
          فريق كنترول جاهز لمساعدتك في أي استفسار أو مشكلة تشغيلية، مع متابعة مباشرة لضمان استمرار العمل بكفاءة أعلى خلال الحملة.
        </p>
        <div class="support-hero__actions">
          <a href="{{ route('con-profile', ['token' => $contractor->token]) }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-right ms-1"></i>
            العودة لصفحة المتعهد
          </a>
          <a href="https://kw-control.com/about-control" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
            <i class="fa fa-rocket ms-1"></i>
            تعرّف أكثر على كنترول
          </a>
        </div>
      </div>

      <div class="support-grid">
        <article class="support-card">
          <span class="support-card__icon"><i class="fa fa-phone"></i></span>
          <h6>دعم هاتفي مباشر</h6>
          <p>للمشكلات العاجلة، تواصل معنا مباشرة لتحصل على حل سريع أثناء ساعات العمل.</p>
          <a href="tel:+96500000000" class="btn btn-sm btn-outline-primary">اتصال الآن</a>
        </article>

        <article class="support-card">
          <span class="support-card__icon"><i class="fa fa-whatsapp"></i></span>
          <h6>دعم واتساب</h6>
          <p>أرسل سكرين أو فيديو قصير للمشكلة، وسيقوم الفريق بالرد خطوة بخطوة.</p>
          <a href="https://wa.me/96500000000" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success">محادثة واتساب</a>
        </article>

        <article class="support-card">
          <span class="support-card__icon"><i class="fa fa-envelope"></i></span>
          <h6>دعم البريد الإلكتروني</h6>
          <p>للطلبات التفصيلية أو المتابعة الفنية، راسلنا وسيتم فتح تذكرة دعم رسمية.</p>
          <a href="mailto:support@kw-control.com" class="btn btn-sm btn-outline-dark">إرسال بريد</a>
        </article>
      </div>

      <section class="support-section" aria-label="خطوات الدعم">
        <h2 class="support-section__title">كيف نخدمك بسرعة؟</h2>
        <ul class="support-steps">
          <li>
            <strong>1) وصف واضح للمشكلة</strong>
            <span>اكتب المشكلة أو أرسل صورة للشاشة المتأثرة.</span>
          </li>
          <li>
            <strong>2) تشخيص فني فوري</strong>
            <span>الفريق يراجع الحالة ويحدد أفضل حل تقني.</span>
          </li>
          <li>
            <strong>3) متابعة حتى الإغلاق</strong>
            <span>نستمر معك حتى التأكد من عودة العمل بشكل كامل.</span>
          </li>
        </ul>
      </section>

      <p class="support-footer-note">كنترول — دعم فني موثوق لنجاح حملتك الانتخابية.</p>
    </div>

    <script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js') }}"></script>
  </body>
</html>
