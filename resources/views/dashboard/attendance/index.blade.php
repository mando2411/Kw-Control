@extends('layouts.dashboard.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .attendance-pro-page {
            --ap-font: "Changa", "Cairo", sans-serif;
            --ap-ink: #12324e;
            --ap-muted: #5d748f;
            --ap-border: #d7e5f4;
            --ap-primary: #0d9488;
            --ap-primary-dark: #0c7f74;
            --ap-accent: #f59e0b;
            --ap-soft: #eff8ff;
            --ap-shadow: 0 14px 34px rgba(18, 50, 78, 0.12);
            --ap-shadow-soft: 0 9px 22px rgba(18, 50, 78, 0.09);

            position: relative;
            font-family: var(--ap-font);
            color: var(--ap-ink);
            min-height: 100%;
            padding: 1rem 0 2rem;
            overflow: hidden;
        }

        .attendance-pro-page::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(circle at 10% 14%, rgba(13, 148, 136, 0.15), transparent 38%),
                radial-gradient(circle at 92% 10%, rgba(245, 158, 11, 0.14), transparent 34%),
                linear-gradient(180deg, #f9fcff 0%, #eef4fb 100%);
        }

        .attendance-shell {
            position: relative;
            z-index: 1;
        }

        .attendance-hero {
            border: 1px solid var(--ap-border);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: var(--ap-shadow);
            padding: 1rem 1.1rem;
        }

        .attendance-title {
            margin: 0;
            font-size: clamp(1.06rem, 2.2vw, 1.55rem);
            font-weight: 800;
            color: #103357;
            letter-spacing: 0.01em;
        }

        .attendance-subtitle {
            margin: 0.35rem 0 0;
            color: var(--ap-muted);
            font-weight: 600;
            font-size: 0.91rem;
        }

        .attendance-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.38rem;
            padding: 0.4rem 0.82rem;
            border-radius: 999px;
            background: #def7f4;
            color: #0b6a62;
            font-size: 0.82rem;
            font-weight: 800;
        }

        .attendance-panel {
            margin-top: 0.95rem;
            border: 1px solid var(--ap-border);
            border-radius: 18px;
            background: #fff;
            box-shadow: var(--ap-shadow-soft);
            padding: 0.9rem;
        }

        .attendance-field-label {
            font-size: 0.82rem;
            font-weight: 800;
            color: #1a4468;
            margin-bottom: 0.38rem;
        }

        .attendance-field,
        .attendance-search {
            border: 1px solid #ccddf0;
            border-radius: 12px;
            min-height: 44px;
            font-size: 0.9rem;
            font-weight: 700;
            color: #184066;
        }

        .attendance-field:focus,
        .attendance-search:focus {
            border-color: #4ea9d6;
            box-shadow: 0 0 0 0.2rem rgba(78, 169, 214, 0.16);
        }

        .stats-grid {
            margin-top: 0.9rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.65rem;
        }

        .stats-card {
            border: 1px solid #d9e8f7;
            border-radius: 14px;
            background: linear-gradient(160deg, #ffffff 0%, #f6fbff 100%);
            padding: 0.8rem 0.85rem;
            text-align: center;
        }

        .stats-label {
            display: block;
            color: #4d6886;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .stats-value {
            display: block;
            margin-top: 0.22rem;
            font-size: 1.12rem;
            font-weight: 900;
            color: #143a5e;
        }

        .stats-card--attended {
            border-color: rgba(10, 135, 123, 0.35);
            background: linear-gradient(160deg, #ecfffc 0%, #dcfaf5 100%);
        }

        .stats-card--attended .stats-value {
            color: #0b6f67;
        }

        .stats-card--remaining {
            border-color: rgba(180, 89, 26, 0.34);
            background: linear-gradient(160deg, #fff8ef 0%, #ffeeda 100%);
        }

        .stats-card--remaining .stats-value {
            color: #9a4d17;
        }

        .attendance-table-wrap {
            margin-top: 0.9rem;
            border: 1px solid var(--ap-border);
            border-radius: 16px;
            background: #fff;
            box-shadow: var(--ap-shadow-soft);
            overflow: hidden;
        }

        .attendance-table {
            margin-bottom: 0;
            font-size: 0.88rem;
        }

        .attendance-table thead th {
            background: #103153;
            color: #fff;
            font-weight: 800;
            border-color: #274768;
            padding: 0.62rem 0.5rem;
            white-space: nowrap;
        }

        .attendance-table tbody td {
            border-color: #deebf8;
            padding: 0.55rem 0.46rem;
            vertical-align: middle;
            font-weight: 700;
            color: #183f63;
        }

        .attendance-table tbody tr:hover td {
            background: #f7fbff;
        }

        .attendance-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 82px;
            border-radius: 999px;
            padding: 0.22rem 0.55rem;
            font-size: 0.77rem;
            font-weight: 900;
        }

        .attendance-status--yes {
            color: #0c6d60;
            background: rgba(16, 185, 129, 0.16);
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .attendance-status--no {
            color: #8f4d19;
            background: rgba(245, 158, 11, 0.16);
            border: 1px solid rgba(245, 158, 11, 0.24);
        }

        .att-btn {
            border: 0;
            border-radius: 10px;
            min-height: 34px;
            font-size: 0.78rem;
            font-weight: 800;
            padding: 0.28rem 0.58rem;
            transition: transform 0.17s ease, filter 0.17s ease;
        }

        .att-btn:hover:not(:disabled) {
            transform: translateY(-1px);
            filter: brightness(0.97);
        }

        .att-btn:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .att-btn--yes {
            color: #fff;
            background: linear-gradient(135deg, #0a8f80 0%, #0bb19e 100%);
            box-shadow: 0 8px 16px rgba(10, 143, 128, 0.24);
        }

        .att-btn--no {
            color: #fff;
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            box-shadow: 0 8px 16px rgba(217, 119, 6, 0.24);
        }

        .att-loading {
            display: none;
            text-align: center;
            padding: 1.3rem;
            color: #2f577f;
            font-weight: 800;
            font-size: 0.88rem;
        }

        .att-loading i {
            margin-inline-end: 0.32rem;
            animation: att-spin 0.9s linear infinite;
        }

        @keyframes att-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .att-empty {
            text-align: center;
            color: #6a819b;
            font-weight: 700;
            padding: 1.3rem 0.4rem;
        }

        @media (max-width: 991.98px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .attendance-panel,
            .attendance-hero {
                border-radius: 14px;
            }
        }
    </style>

    <section class="attendance-pro-page rtl">
        <div class="container-fluid attendance-shell">
            <div class="attendance-hero d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1 class="attendance-title">إدارة الحضور الذكية</h1>
                    <p class="attendance-subtitle">واجهة احترافية لتسجيل حضور الناخبين بسرعة واستقرار مع دعم المرشح العادي ومرشحي القوائم.</p>
                </div>
                <span class="attendance-badge">
                    <i class="fa-solid fa-user-check"></i>
                    Live Attendance
                </span>
            </div>

            <div class="attendance-panel">
                @if ($committees->isEmpty())
                    <div class="alert alert-warning mb-0">لا توجد لجان متاحة لهذا الحساب حاليا.</div>
                @else
                    <div class="row g-2 align-items-end">
                        <div class="col-xl-4 col-lg-5 col-md-6">
                            <label class="attendance-field-label" for="committee">اللجنة</label>
                            <select name="committee" id="committee" class="form-control attendance-field" @disabled(!($canChangeCommittee ?? true))>
                                <option value="">اختر اللجنة</option>
                                @foreach ($committees as $i => $committee)
                                    <option value="{{ $committee->id }}" @selected((int) ($defaultCommitteeId ?? 0) === (int) $committee->id)>
                                        {{ $i + 1 }} - {{ $committee->name }} ({{ $committee->type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-8 col-lg-7 col-md-6">
                            <label class="attendance-field-label" for="searchBox">بحث سريع</label>
                            <input type="text" id="searchBox" class="form-control attendance-search" placeholder="ابحث بالاسم أو الصندوق" autocomplete="off">
                        </div>
                    </div>

                    <div class="stats-grid" id="statsGrid" style="display:none;">
                        <div class="stats-card">
                            <span class="stats-label">إجمالي الناخبين</span>
                            <span class="stats-value" id="voter_count">0</span>
                        </div>
                        <div class="stats-card stats-card--attended">
                            <span class="stats-label">عدد الحضور</span>
                            <span class="stats-value" id="attend_count">0</span>
                        </div>
                        <div class="stats-card stats-card--remaining">
                            <span class="stats-label">المتبقي</span>
                            <span class="stats-value" id="remaining_count">0</span>
                        </div>
                    </div>
                @endif
            </div>

            @if ($committees->isNotEmpty())
                <div class="attendance-table-wrap" id="resultWrap" style="display:none;">
                    <div class="att-loading" id="loadingIndicator">
                        <i class="fa-solid fa-spinner"></i>
                        جاري تحميل البيانات...
                    </div>

                    <div class="table-responsive">
                        <table class="table attendance-table text-center align-middle" id="voter_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>الصندوق</th>
                                    <th>الحالة</th>
                                    <th>تغيير الحالة</th>
                                </tr>
                            </thead>
                            <tbody id="voter-list">
                                <tr><td colspan="5" class="att-empty">اختر اللجنة لعرض الناخبين.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" style="color:#173b60">تأكيد العملية</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-2">
                        <i class="fas fa-question-circle text-warning" style="font-size: 2.7rem;"></i>
                    </div>
                    <h6 id="confirmMessage" class="mb-0"></h6>
                    <input type="hidden" id="voterIdInput">
                    <input type="hidden" id="statusInput">
                </div>
                <div class="modal-footer justify-content-center border-top-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-success px-4" id="confirmButton">تأكيد</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" id="statusModalHeader">
                    <h5 class="modal-title">نتيجة العملية</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-2">
                        <i class="fas fa-circle-check" id="statusModalIcon" style="font-size: 2.7rem;"></i>
                    </div>
                    <h6 id="statusMessage" class="mb-0"></h6>
                </div>
                <div class="modal-footer justify-content-center border-top-0">
                    <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">موافق</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        (function () {
            var votersUrlTemplate = @json(route('get-voters', ['committee_id' => '__ID__']));
            var countsUrlTemplate = @json(route('get_attending_counts', ['committee_id' => '__ID__']));
            var updateStatusUrl = @json(route('dashboard.voters.change-status', 0));
            var defaultCommitteeId = parseInt(@json((int) ($defaultCommitteeId ?? 0)), 10) || 0;
            var canChangeCommittee = @json((bool) ($canChangeCommittee ?? true));

            var committeeSelect = $('#committee');
            var searchBox = $('#searchBox');
            var voterList = $('#voter-list');
            var resultWrap = $('#resultWrap');
            var loadingIndicator = $('#loadingIndicator');
            var statsGrid = $('#statsGrid');
            var currentSearch = '';
            var inFlightVoters = false;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function endpointFromTemplate(template, committeeId) {
                return String(template).replace('__ID__', committeeId);
            }

            function getCommitteeId() {
                return parseInt(committeeSelect.val(), 10) || 0;
            }

            function setLoading(active) {
                if (active) {
                    resultWrap.show();
                    loadingIndicator.show();
                } else {
                    loadingIndicator.hide();
                }
            }

            function showStatusMessage(message, isSuccess) {
                var header = $('#statusModalHeader');
                var icon = $('#statusModalIcon');

                $('#statusMessage').text(message || 'تم تنفيذ العملية.');

                if (isSuccess) {
                    header.removeClass('bg-danger').addClass('bg-success');
                    icon.removeClass('text-danger fa-circle-xmark').addClass('text-success fa-circle-check');
                } else {
                    header.removeClass('bg-success').addClass('bg-danger');
                    icon.removeClass('text-success fa-circle-check').addClass('text-danger fa-circle-xmark');
                }

                $('#statusModal').modal('show');
            }

            function setStats(voterCount, attendCount) {
                var total = parseInt(voterCount, 10) || 0;
                var attended = parseInt(attendCount, 10) || 0;
                var remaining = Math.max(total - attended, 0);

                $('#voter_count').text(total);
                $('#attend_count').text(attended);
                $('#remaining_count').text(remaining);
                statsGrid.show();
            }

            function statusBadge(status) {
                if (parseInt(status, 10) === 1) {
                    return '<span class="attendance-status attendance-status--yes">حضر</span>';
                }

                return '<span class="attendance-status attendance-status--no">لم يحضر</span>';
            }

            function actionButtons(voterId, status) {
                var attended = parseInt(status, 10) === 1;

                return ''
                    + '<button class="att-btn att-btn--yes approve-btn me-1" data-id="' + voterId + '" ' + (attended ? 'disabled' : '') + '>حضر</button>'
                    + '<button class="att-btn att-btn--no reject-btn" data-id="' + voterId + '" ' + (!attended ? 'disabled' : '') + '>لم يحضر</button>';
            }

            function escapeHtml(value) {
                return String(value || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function renderVoters(voters) {
                if (!Array.isArray(voters) || voters.length === 0) {
                    voterList.html('<tr><td colspan="5" class="att-empty">لا توجد نتائج مطابقة.</td></tr>');
                    return;
                }

                var rows = '';
                voters.forEach(function (voter, index) {
                    rows += ''
                        + '<tr>'
                        + '<td>' + (index + 1) + '</td>'
                        + '<td>' + escapeHtml(voter.name) + '</td>'
                        + '<td>' + escapeHtml(voter.alsndok) + '</td>'
                        + '<td id="voter_status_' + voter.id + '">' + statusBadge(voter.status ? 1 : 0) + '</td>'
                        + '<td id="voter_buttons_' + voter.id + '">' + actionButtons(voter.id, voter.status ? 1 : 0) + '</td>'
                        + '</tr>';
                });

                voterList.html(rows);
                bindActionButtons();
            }

            function fetchCounts(committeeId) {
                return $.ajax({
                    url: endpointFromTemplate(countsUrlTemplate, committeeId),
                    type: 'GET',
                    dataType: 'json'
                }).done(function (response) {
                    setStats(response.voter_count, response.attend_count);
                });
            }

            function fetchVoters(committeeId, searchValue) {
                if (!committeeId) {
                    voterList.html('<tr><td colspan="5" class="att-empty">اختر اللجنة لعرض الناخبين.</td></tr>');
                    return;
                }

                if (inFlightVoters) {
                    return;
                }

                inFlightVoters = true;
                setLoading(true);

                $.ajax({
                    url: endpointFromTemplate(votersUrlTemplate, committeeId),
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        searchValue: searchValue || ''
                    }
                }).done(function (response) {
                    renderVoters((response && response.voters) ? response.voters : []);
                    resultWrap.show();
                }).fail(function (xhr) {
                    var errorMessage = 'حدث خطأ أثناء تحميل الناخبين.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    voterList.html('<tr><td colspan="5" class="att-empty">' + escapeHtml(errorMessage) + '</td></tr>');
                }).always(function () {
                    setLoading(false);
                    inFlightVoters = false;
                });
            }

            function refreshCommitteeData() {
                var committeeId = getCommitteeId();
                if (!committeeId) {
                    resultWrap.hide();
                    statsGrid.hide();
                    return;
                }

                fetchCounts(committeeId).fail(function (xhr) {
                    var message = (xhr && xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'تعذر تحميل إحصاءات الحضور.';
                    showStatusMessage(message, false);
                });

                fetchVoters(committeeId, currentSearch);
            }

            function showConfirmModal(message, status, voterId) {
                $('#confirmMessage').text(message);
                $('#voterIdInput').val(voterId);
                $('#statusInput').val(status);
                $('#confirmModal').modal('show');
            }

            function bindActionButtons() {
                $('.approve-btn').off('click').on('click', function () {
                    var voterId = parseInt($(this).data('id'), 10) || 0;
                    if (!voterId) {
                        return;
                    }
                    showConfirmModal('هل أنت متأكد من تسجيل حضور الناخب؟', 1, voterId);
                });

                $('.reject-btn').off('click').on('click', function () {
                    var voterId = parseInt($(this).data('id'), 10) || 0;
                    if (!voterId) {
                        return;
                    }
                    showConfirmModal('هل أنت متأكد من إلغاء حضور الناخب؟', 0, voterId);
                });
            }

            function updateStatus(status, voterId) {
                var committeeId = getCommitteeId();
                if (!committeeId || !voterId) {
                    showStatusMessage('يرجى اختيار لجنة وناخب صحيحين.', false);
                    return;
                }

                setLoading(true);

                $.ajax({
                    url: updateStatusUrl,
                    type: 'POST',
                    data: JSON.stringify({
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        status: parseInt(status, 10) || 0,
                        voterId: parseInt(voterId, 10) || 0,
                        committee: committeeId
                    }),
                    contentType: 'application/json',
                    dataType: 'json'
                }).done(function (response) {
                    if (response && response.success) {
                        showStatusMessage(response.message || 'تم تحديث الحالة بنجاح.', true);
                        refreshCommitteeData();
                        return;
                    }

                    showStatusMessage((response && response.message) ? response.message : 'تعذر تحديث الحالة.', false);
                }).fail(function (xhr) {
                    var message = (xhr && xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'حدث خطأ أثناء تحديث الحالة.';
                    showStatusMessage(message, false);
                }).always(function () {
                    setLoading(false);
                });
            }

            committeeSelect.on('change', function () {
                currentSearch = '';
                searchBox.val('');
                refreshCommitteeData();
            });

            var searchTimer = null;
            searchBox.on('keyup', function () {
                clearTimeout(searchTimer);

                searchTimer = setTimeout(function () {
                    currentSearch = String(searchBox.val() || '').trim();
                    fetchVoters(getCommitteeId(), currentSearch);
                }, 350);
            });

            $('#confirmButton').on('click', function () {
                var status = $('#statusInput').val();
                var voterId = $('#voterIdInput').val();
                $('#confirmModal').modal('hide');
                updateStatus(status, voterId);
            });

            if (defaultCommitteeId > 0 && committeeSelect.length) {
                committeeSelect.val(String(defaultCommitteeId));
                if (!canChangeCommittee) {
                    committeeSelect.prop('disabled', true);
                }
                refreshCommitteeData();
            }
        })();
    </script>
@endpush
