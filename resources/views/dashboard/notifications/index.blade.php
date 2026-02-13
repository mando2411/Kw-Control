@extends('layouts.dashboard.app')

@section('content')
<style>
    .notifications-modern-wrap {
        direction: rtl;
        text-align: right;
        max-width: 1100px;
        margin: 0 auto;
        padding: 12px;
    }

    .notifications-modern-hero {
        border: 1px solid var(--ui-border, rgba(148, 163, 184, 0.25));
        background: var(--ui-surface, #fff);
        border-radius: 18px;
        box-shadow: var(--ui-shadow, 0 14px 36px rgba(2, 6, 23, 0.08));
        padding: 18px 20px;
        margin-bottom: 16px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .notifications-modern-title {
        font-size: 1.18rem;
        font-weight: 900;
        color: var(--ui-ink, #0f172a);
        margin: 0;
    }

    .notifications-modern-sub {
        margin: 4px 0 0;
        color: var(--ui-muted, #64748b);
        font-size: 0.92rem;
    }

    .notifications-unread-pill {
        border-radius: 999px;
        padding: 6px 12px;
        border: 1px solid rgba(14, 165, 233, 0.28);
        background: rgba(14, 165, 233, 0.08);
        color: var(--ui-accent, #0ea5e9);
        font-weight: 800;
        font-size: 0.82rem;
    }

    .notifications-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .notifications-grid {
        display: grid;
        gap: 12px;
    }

    .notification-card {
        border: 1px solid var(--ui-border, rgba(148, 163, 184, 0.25));
        background: var(--ui-surface, #fff);
        border-radius: 16px;
        box-shadow: 0 10px 28px rgba(2, 6, 23, 0.06);
        padding: 14px 14px 12px;
        transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
        animation: notifFadeUp 420ms cubic-bezier(0.16, 1, 0.3, 1) both;
        animation-delay: var(--notif-delay, 0ms);
    }

    .notification-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px rgba(2, 6, 23, 0.1);
        border-color: rgba(14, 165, 233, 0.24);
    }

    .notification-card.is-unread {
        border-color: rgba(14, 165, 233, 0.34);
        background: linear-gradient(180deg, rgba(14, 165, 233, 0.07), rgba(255, 255, 255, 0.96));
    }

    .notification-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
    }

    .notification-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 900;
        color: var(--ui-ink, #0f172a);
    }

    .notification-time {
        white-space: nowrap;
        font-size: 0.78rem;
        color: var(--ui-muted, #64748b);
        font-weight: 700;
    }

    .notification-body {
        color: var(--ui-muted, #64748b);
        margin: 0 0 10px;
        line-height: 1.75;
        font-size: 0.92rem;
    }

    .notification-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .notification-action-expired {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 800;
        color: #92400e;
        background: #fffbeb;
        border: 1px solid rgba(245, 158, 11, .35);
    }

    .notification-status {
        font-size: 0.76rem;
        font-weight: 800;
        border-radius: 999px;
        padding: 4px 9px;
        border: 1px solid var(--ui-border, rgba(148, 163, 184, 0.25));
        color: var(--ui-muted, #64748b);
        background: rgba(248, 250, 252, 0.9);
    }

    .notification-status.is-unread {
        color: var(--ui-accent, #0ea5e9);
        border-color: rgba(14, 165, 233, 0.3);
        background: rgba(14, 165, 233, 0.1);
    }

    .notification-empty {
        border: 1px dashed var(--ui-border, rgba(148, 163, 184, 0.3));
        border-radius: 16px;
        background: var(--ui-surface, #fff);
        padding: 26px 18px;
        text-align: center;
        color: var(--ui-muted, #64748b);
        font-weight: 700;
    }

    .notifications-pagination {
        margin-top: 16px;
        display: flex;
        justify-content: center;
    }

    @keyframes notifFadeUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .notification-card {
            animation: none;
            transition: none;
        }
    }
</style>

<div class="notifications-modern-wrap">
    <div class="notifications-modern-hero">
        <div>
            <h1 class="notifications-modern-title">مركز الإشعارات</h1>
            <p class="notifications-modern-sub">كل التنبيهات الحديثة تظهر هنا بشكل مرتب وسهل المتابعة.</p>
        </div>

        <div class="notifications-actions">
            <span class="notifications-unread-pill" id="notificationsUnreadPill">غير المقروء: {{ $unreadCount }}</span>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="notificationsMarkAllBtn">تعليم الكل كمقروء</button>
        </div>
    </div>

    @if ($notifications->count())
        <div class="notifications-grid" id="notificationsGrid">
            @foreach ($notifications as $index => $notification)
                @php
                    $data = is_array($notification->data ?? null) ? $notification->data : [];
                    $isUnread = is_null($notification->read_at);
                    $actionExpiresAt = !empty($data['action_expires_at']) ? \Illuminate\Support\Carbon::parse((string) $data['action_expires_at']) : null;
                    $isActionExpired = $actionExpiresAt ? now()->greaterThan($actionExpiresAt) : false;
                @endphp
                <article
                    id="notif-{{ $notification->id }}"
                    class="notification-card {{ $isUnread ? 'is-unread' : '' }}"
                    style="--notif-delay: {{ min($index, 8) * 55 }}ms"
                    data-notification-id="{{ $notification->id }}"
                    data-unread="{{ $isUnread ? '1' : '0' }}"
                >
                    <div class="notification-head">
                        <h2 class="notification-title">{{ (string) ($data['title'] ?? 'إشعار جديد') }}</h2>
                        <span class="notification-time">{{ optional($notification->created_at)->diffForHumans() }}</span>
                    </div>

                    <p class="notification-body">{{ (string) ($data['body'] ?? '') }}</p>

                    <div class="notification-foot">
                        <span class="notification-status {{ $isUnread ? 'is-unread' : '' }}" data-status>
                            {{ $isUnread ? 'غير مقروء' : 'مقروء' }}
                        </span>

                        @if (!empty($data['action_url']) && !$isActionExpired)
                            <a href="{{ (string) $data['action_url'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-success">
                                {{ (string) ($data['action_label'] ?? 'تنزيل') }}
                            </a>
                        @elseif (!empty($data['action_url']) && $isActionExpired)
                            <span class="notification-action-expired" title="انتهت صلاحية الرابط، يرجى إعادة تجهيز الملف من جديد.">
                                انتهت صلاحية رابط التنزيل — يرجى إعادة استخراج الملف.
                            </span>
                        @endif

                        @if ($isUnread)
                            <button type="button" class="btn btn-sm btn-outline-primary" data-action="read-one">تعليم كمقروء</button>
                        @else
                            <span class="text-success fw-bold" style="font-size: .8rem;">تمت القراءة</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="notifications-pagination">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="notification-empty">لا توجد إشعارات حالياً.</div>
    @endif
</div>

<script>
    (function () {
        var markAllBtn = document.getElementById('notificationsMarkAllBtn');
        var unreadPill = document.getElementById('notificationsUnreadPill');
        var grid = document.getElementById('notificationsGrid');

        function csrfToken() {
            var meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        function setUnreadCount(count) {
            if (!unreadPill) return;
            unreadPill.textContent = 'غير المقروء: ' + String(Math.max(0, Number(count || 0)));
        }

        function readOne(id, card) {
            return fetch("{{ url('dashboard/notifications') }}/" + id + "/read", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('failed');
                }
                return response.json();
            })
            .then(function (data) {
                if (card) {
                    card.classList.remove('is-unread');
                    card.dataset.unread = '0';
                    var status = card.querySelector('[data-status]');
                    if (status) {
                        status.classList.remove('is-unread');
                        status.textContent = 'مقروء';
                    }
                    var btn = card.querySelector('[data-action="read-one"]');
                    if (btn) {
                        var done = document.createElement('span');
                        done.className = 'text-success fw-bold';
                        done.style.fontSize = '.8rem';
                        done.textContent = 'تمت القراءة';
                        btn.replaceWith(done);
                    }
                }

                if (data && typeof data.unread_count !== 'undefined') {
                    setUnreadCount(data.unread_count);
                }
            })
            .catch(function () {
                // no-op
            });
        }

        if (grid) {
            grid.addEventListener('click', function (event) {
                var button = event.target.closest('[data-action="read-one"]');
                if (!button) return;

                event.preventDefault();
                var card = button.closest('[data-notification-id]');
                if (!card) return;

                var id = card.getAttribute('data-notification-id');
                if (!id) return;

                button.disabled = true;
                readOne(id, card).finally(function () {
                    button.disabled = false;
                });
            });
        }

        if (markAllBtn) {
            markAllBtn.addEventListener('click', function (event) {
                event.preventDefault();
                markAllBtn.disabled = true;

                fetch("{{ route('dashboard.notifications.read-all') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('failed');
                    }
                    return response.json();
                })
                .then(function (data) {
                    var unreadCards = document.querySelectorAll('[data-notification-id][data-unread="1"]');
                    unreadCards.forEach(function (card) {
                        card.classList.remove('is-unread');
                        card.dataset.unread = '0';

                        var status = card.querySelector('[data-status]');
                        if (status) {
                            status.classList.remove('is-unread');
                            status.textContent = 'مقروء';
                        }

                        var btn = card.querySelector('[data-action="read-one"]');
                        if (btn) {
                            var done = document.createElement('span');
                            done.className = 'text-success fw-bold';
                            done.style.fontSize = '.8rem';
                            done.textContent = 'تمت القراءة';
                            btn.replaceWith(done);
                        }
                    });

                    if (data && typeof data.unread_count !== 'undefined') {
                        setUnreadCount(data.unread_count);
                    } else {
                        setUnreadCount(0);
                    }
                })
                .catch(function () {
                    // no-op
                })
                .finally(function () {
                    markAllBtn.disabled = false;
                });
            });
        }

        var openId = new URLSearchParams(window.location.search).get('open');
        if (openId) {
            var target = document.getElementById('notif-' + openId);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                target.style.transition = 'box-shadow 260ms ease';
                target.style.boxShadow = '0 0 0 3px rgba(14, 165, 233, 0.24), 0 16px 36px rgba(2, 6, 23, 0.10)';

                if (target.getAttribute('data-unread') === '1') {
                    readOne(openId, target);
                }

                setTimeout(function () {
                    target.style.boxShadow = '';
                }, 1400);
            }
        }
    })();
</script>
@endsection
