<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="كنترول: منصة احترافية لإدارة الحملات الانتخابية، متابعة المرشحين، النتائج، فرق العمل، والتواصل الميداني من لوحة واحدة.">
    <meta name="keywords" content="كنترول, حملات انتخابية, إدارة انتخابات, متابعة المرشحين, لوحة تحكم">
    <title>{{ config('app.name', 'كنترول') }} | إدارة حملتك الانتخابية باحتراف</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap">

    <style>
        :root {
            --ink: #0f172a;
            --muted: #475569;
            --accent: #0ea5e9;
            --accent-warm: #f59e0b;
            --surface: rgba(255, 255, 255, 0.86);
            --border: rgba(15, 23, 42, 0.12);
            --shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
        }

        body {
            font-family: "Cairo", "Segoe UI", Tahoma, Arial, sans-serif;
            color: var(--ink);
            background: radial-gradient(circle at 15% 15%, #e0f2fe 0%, #f8fafc 45%, #fff7ed 100%);
            min-height: 100vh;
        }

        .glass-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 1.2rem;
            box-shadow: var(--shadow);
            backdrop-filter: blur(8px);
        }

        .hero {
            padding: 5rem 0 3rem;
        }

        .hero-badge {
            display: inline-flex;
            gap: 0.5rem;
            align-items: center;
            border-radius: 999px;
            padding: 0.45rem 0.9rem;
            background: rgba(14, 165, 233, 0.12);
            color: #075985;
            font-weight: 700;
            font-size: 0.92rem;
        }

        .hero h1 {
            font-size: clamp(1.8rem, 3.5vw, 3rem);
            line-height: 1.35;
            font-weight: 900;
        }

        .hero p {
            color: var(--muted);
            font-size: 1.03rem;
        }

        .cta-btn {
            border: none;
            border-radius: 999px;
            padding: 0.7rem 1.4rem;
            font-weight: 800;
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .cta-btn.primary {
            background: linear-gradient(120deg, #f59e0b, #f97316);
            color: #1f2937;
            box-shadow: 0 16px 28px rgba(249, 115, 22, 0.28);
        }

        .cta-btn.secondary {
            background: rgba(14, 165, 233, 0.12);
            color: #0c4a6e;
        }

        .cta-btn:hover {
            transform: translateY(-2px);
        }

        .metrics {
            margin-top: 1.2rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .metric-item {
            text-align: center;
            padding: 0.8rem;
            border-radius: 0.9rem;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid var(--border);
        }

        .metric-item strong {
            display: block;
            font-size: 1.2rem;
            font-weight: 900;
        }

        .section-title {
            font-size: clamp(1.4rem, 2.4vw, 2rem);
            font-weight: 900;
            margin-bottom: 0.4rem;
        }

        .section-subtitle {
            color: var(--muted);
            margin-bottom: 1.2rem;
        }

        .feature-card {
            padding: 1.2rem;
            height: 100%;
            transition: transform 220ms ease, box-shadow 220ms ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 24px 44px rgba(15, 23, 42, 0.14);
        }

        .feature-icon {
            width: 2.6rem;
            height: 2.6rem;
            border-radius: 0.8rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(14, 165, 233, 0.14);
            color: #0369a1;
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
        }

        .shot-grid {
            display: grid;
            grid-template-columns: 1.35fr 1fr;
            gap: 1rem;
        }

        .shot-mobile-stack {
            display: grid;
            grid-template-rows: 1fr 1fr;
            gap: 0.8rem;
        }

        .shot-card {
            padding: 0.8rem;
        }

        .shot-frame {
            border-radius: 0.95rem;
            border: 1px solid var(--border);
            overflow: hidden;
            background: #fff;
        }

        .shot-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .shot-desktop {
            aspect-ratio: 16/10;
        }

        .shot-mobile {
            aspect-ratio: 9/16;
            max-height: 420px;
        }

        .shot-mobile-small {
            aspect-ratio: 9/16;
            max-height: 205px;
        }

        .contact-strip {
            margin-top: 3rem;
            padding: 1rem 1.2rem;
        }

        .contact-phone {
            direction: ltr;
            unicode-bidi: plaintext;
            font-weight: 900;
            color: #0c4a6e;
            text-decoration: none;
        }

        .contact-actions {
            margin-top: 0.6rem;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .contact-action-icon {
            width: 2.35rem;
            height: 2.35rem;
            border-radius: 999px;
            border: 1px solid rgba(15, 23, 42, 0.12);
            background: rgba(255, 255, 255, 0.92);
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: transform 160ms ease, box-shadow 160ms ease;
        }

        .contact-action-icon i {
            font-size: 1rem;
        }

        .contact-action-icon:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(15, 23, 42, 0.12);
            color: #0f172a;
        }

        .contact-action-icon.whatsapp {
            color: #128C7E;
            border-color: rgba(18, 140, 126, 0.35);
            background: rgba(37, 211, 102, 0.12);
        }

        .reveal {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity 500ms ease, transform 500ms ease;
        }

        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 991px) {
            .hero {
                padding-top: 3.8rem;
            }

            .metrics {
                grid-template-columns: 1fr;
            }

            .shot-grid {
                grid-template-columns: 1fr;
            }

            .shot-mobile {
                max-height: 340px;
            }

            .shot-mobile-stack {
                grid-template-columns: 1fr 1fr;
                grid-template-rows: auto;
            }

            .shot-mobile-small {
                max-height: 260px;
            }
        }
    </style>
</head>

<body>
    @php
        $supportCountryCode = preg_replace('/\D+/', '', (string) config('app.support_country_code', '965')) ?: '965';
        $supportPhoneDigits = preg_replace('/\D+/', '', (string) config('app.support_phone', '55150551')) ?: '55150551';
        $supportDisplayPhone = '+' . $supportCountryCode . ' ' . $supportPhoneDigits;
        $supportCallLink = 'tel:+' . $supportCountryCode . $supportPhoneDigits;
        $supportWhatsappLink = 'https://wa.me/' . $supportCountryCode . $supportPhoneDigits . '?text=' . rawurlencode('ممكن استفسر عن https://kw-control.com/');
    @endphp

    <div class="container">
        <section class="hero">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-lg-7 reveal">
                    <span class="hero-badge"><i class="bi bi-stars"></i> منصة انتخابية احترافية</span>
                    <h1 class="mt-3">شغّل حملتك بثقة كاملة، وخلّ القرار الانتخابي أسرع، أدق، وأكثر تأثيرًا.</h1>
                    <p class="mt-3 mb-4">
                        كنترول يجمع كل ما تحتاجه في منصة واحدة: متابعة الميدان لحظيًا، إدارة اللجان والفرق، قراءة مؤشرات الأداء فورًا،
                        وتحويل البيانات إلى قرارات عملية ترفع كفاءة الحملة وتزيد فرص الفوز.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('login') }}" class="cta-btn primary text-decoration-none">ابدأ الآن</a>
                        <a href="#features" class="cta-btn secondary text-decoration-none">استكشف المميزات</a>
                    </div>

                    <div class="metrics">
                        <div class="metric-item glass-card">
                            <strong>+90%</strong>
                            وضوح أعلى للمتابعة اليومية
                        </div>
                        <div class="metric-item glass-card">
                            <strong>24/7</strong>
                            وصول مباشر من أي جهاز
                        </div>
                        <div class="metric-item glass-card">
                            <strong>لحظي</strong>
                            تحديثات فورية للبيانات
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-5 reveal">
                    <div class="glass-card shot-card">
                        <h5 class="fw-bold mb-2">نظرة احترافية على النظام</h5>
                        <p class="text-muted small mb-3">عرض حقيقي للواجهة على سطح المكتب والموبايل، مع تجربة دخول آمنة تعزز الثقة من أول لحظة.</p>
                        <div class="shot-grid">
                            <div class="shot-frame shot-desktop">
                                <img src="{{ asset('about-sec/Screenshot 2026-02-12 141830.png') }}" alt="واجهة كنترول على سطح المكتب">
                            </div>
                            <div class="shot-mobile-stack">
                                <div class="shot-frame shot-mobile-small">
                                    <img src="{{ asset('about-sec/WhatsApp Image 2026-02-12 at 2.16.47 PM.jpeg') }}" alt="واجهة كنترول على الموبايل">
                                </div>
                                <div class="shot-frame shot-mobile-small">
                                    <img src="{{ asset('about-sec/WhatsApp Image 2026-02-12 at 2.17.36 PM.jpeg') }}" alt="شاشة تسجيل دخول آمنة في كنترول">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="pb-4">
            <h2 class="section-title reveal">لماذا كنترول لحملتك الانتخابية؟</h2>
            <p class="section-subtitle reveal">مميزات عملية مصممة للواقع الميداني، مع تجربة استخدام سلسة وواضحة.</p>

            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-4 reveal">
                    <div class="glass-card feature-card">
                        <span class="feature-icon"><i class="bi bi-diagram-3"></i></span>
                        <h6 class="fw-bold">إدارة مركزية للفريق والمهام</h6>
                        <p class="text-muted mb-0">تنظيم الأدوار، متابعة التنفيذ، وتقليل العشوائية عبر لوحة واحدة لكل أعضاء الحملة.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 reveal">
                    <div class="glass-card feature-card">
                        <span class="feature-icon"><i class="bi bi-graph-up-arrow"></i></span>
                        <h6 class="fw-bold">تحليلات ونتائج قابلة للقرار</h6>
                        <p class="text-muted mb-0">قراءة سريعة للمؤشرات تساعدك تحدد أولوياتك وتتحرك في الوقت المناسب.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 reveal">
                    <div class="glass-card feature-card">
                        <span class="feature-icon"><i class="bi bi-phone"></i></span>
                        <h6 class="fw-bold">تجربة متكاملة على الموبايل</h6>
                        <p class="text-muted mb-0">واجهة مرنة للفِرق الميدانية، سهلة الاستخدام في أي وقت ومن أي مكان.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 reveal">
                    <div class="glass-card feature-card">
                        <span class="feature-icon"><i class="bi bi-shield-lock"></i></span>
                        <h6 class="fw-bold">أمان وصلاحيات دقيقة</h6>
                        <p class="text-muted mb-0">تحكم واضح بمن يشاهد أو يعدّل البيانات، لحماية معلومات الحملة وسلامة سير العمل.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 reveal">
                    <div class="glass-card feature-card">
                        <span class="feature-icon"><i class="bi bi-lightning-charge"></i></span>
                        <h6 class="fw-bold">أداء سريع بدون تعقيد</h6>
                        <p class="text-muted mb-0">تصميم نظيف وأنيميشن ناعم يوفّر تجربة احترافية دون إبطاء خطواتك اليومية.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 reveal">
                    <div class="glass-card feature-card">
                        <span class="feature-icon"><i class="bi bi-chat-dots"></i></span>
                        <h6 class="fw-bold">دعم واستفسار مباشر</h6>
                        <p class="text-muted mb-0">قناة تواصل واضحة للاستفسارات الفنية والتشغيلية أثناء تجهيز وإدارة حملتك.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact-strip glass-card reveal mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h6 class="fw-bold mb-1">هذا الموقع برعاية أحمد خلف</h6>
                    <div class="text-muted">للاستفسار : <a class="contact-phone" href="{{ $supportCallLink }}">{{ $supportDisplayPhone }}</a></div>
                    <div class="contact-actions" aria-label="أزرار التواصل السريع">
                        <a href="{{ $supportCallLink }}" class="contact-action-icon" aria-label="اتصال مباشر" title="اتصال مباشر">
                            <i class="bi bi-telephone-fill"></i>
                        </a>
                        <a href="{{ $supportWhatsappLink }}" target="_blank" rel="noopener" class="contact-action-icon whatsapp" aria-label="تواصل واتساب" title="تواصل واتساب">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>
                <a href="{{ route('login') }}" class="cta-btn primary text-decoration-none">دخول النظام</a>
            </div>
        </section>
    </div>

    <script>
        (function () {
            var items = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) {
                items.forEach(function (item) {
                    item.classList.add('is-visible');
                });
                return;
            }

            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            items.forEach(function (item) {
                observer.observe(item);
            });
        })();
    </script>
</body>

</html>
