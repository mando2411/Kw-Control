@php
    $elections = \App\Models\Election::select('id', 'name')->get();
@endphp

<div class="container import-section" data-voters-import-root dir="rtl">
    <div class="import-card p-0">
        <div class="import-header p-4">
            <h2 class="h4 mb-2">استيراد الناخبين</h2>
            <p class="import-help mb-0">استخدم هذا النموذج لرفع ملف الناخبين حسب القالب المعتمد. جميع الحقول موضحة بالأسفل.</p>
        </div>

        <div class="p-4">
            <x-dashboard.partials.message-alert />

            <div class="import-warning mb-4" role="alert">
                <strong>تنبيه:</strong> خيار <strong>استبدال البيانات</strong> يحذف البيانات القديمة قبل الاستيراد. استخدمه فقط عند الحاجة.
            </div>

            {{-- Desktop import form (hidden on small screens) --}}
            <form action="{{ route('dashboard.import-voters') }}"
                  class="row g-4 voters-import-form import-form-desktop"
                  enctype="multipart/form-data"
                  method="POST"
                  novalidate
                  data-voters-import-form="desktop">
                @csrf

                <div class="col-12 col-lg-6">
                    <label class="form-label">الانتخابات</label>
                    <select name="election" class="form-select import-field" required aria-invalid="false" data-field="election">
                        <option value="" selected disabled>اختر الانتخابات</option>
                        @foreach ($elections as $election)
                            <option value="{{ $election->id }}">{{ $election->name . "(" . $election->id . ")" }}</option>
                        @endforeach
                    </select>

                    <div class="import-help">اختر الانتخابات المرتبطة بالملف الذي سترفعه.</div>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <a href="#"
                           class="btn btn-outline-secondary btn-sm import-view-data disabled"
                           data-view-data
                           data-base-url="{{ route('dashboard.import-voters-data') }}"
                           aria-disabled="true">عرض الداتا</a>
                    </div>
                    <div class="import-error import-error-election d-none">يرجى اختيار الانتخابات.</div>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">ملف الاستيراد</label>
                    <input type="file" class="form-control import-field" name="import" accept=".xlsx,.xls,.csv" required aria-invalid="false" data-field="file">
                    <div class="import-help">الصيغ المقبولة: .xlsx, .xls, .csv</div>
                    <div class="import-error import-error-file d-none">يرجى اختيار ملف صالح.</div>
                </div>

                <div class="col-12">
                    <label class="form-label">طريقة الاستيراد</label>
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="import-option">
                                <div class="import-option-wrapper">
                                    <input type="radio" name="check" value="dublicate" class="import-option-input" checked data-field="mode">
                                    <div class="import-option-content">
                                        <div class="option-title">إضافة</div>
                                        <div class="option-desc">يضيف السجلات الجديدة دون حذف البيانات الحالية.</div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="import-option option-danger">
                                <div class="import-option-wrapper">
                                    <input type="radio" name="check" value="replace" class="import-option-input" data-field="mode" data-replace-option>
                                    <div class="import-option-content">
                                        <div class="option-title">استبدال</div>
                                        <div class="option-desc">يحذف البيانات القديمة أولاً ثم يستورد الملف الجديد.</div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="import-option">
                                <div class="import-option-wrapper">
                                    <input type="radio" name="check" value="status" class="import-option-input" data-field="mode">
                                    <div class="import-option-content">
                                        <div class="option-title">تحديث الحالة</div>
                                        <div class="option-desc">يحدّث حالة الحضور حسب الملف دون استيراد كامل البيانات.</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="import-help mt-2">اختر طريقة الاستيراد المناسبة لملفك.</div>
                    <div class="import-help mt-1">تحذير: خيار الاستبدال يمسح البيانات الحالية.</div>
                    <div class="import-error import-error-mode d-none">يرجى اختيار طريقة الاستيراد.</div>
                </div>

                <div class="col-12 d-flex flex-column flex-md-row gap-3 justify-content-between align-items-md-center">
                    <div class="import-help">تأكد من توافق الملف مع القالب قبل الإرسال.</div>
                    <button type="submit" class="btn btn-custom px-4 import-submit w-100 w-md-auto" data-action="submit">
                        <span class="submit-text">بدء الاستيراد</span>
                        <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>

                <div class="col-12">
                    <div class="import-progress d-none" data-progress>
                        <div class="import-progress-bar" data-progress-bar></div>
                    </div>
                    <div class="import-help import-progress-text mt-2 d-none" data-progress-text>جاري رفع الملف...</div>
                    <div class="import-error import-error-submit d-none" data-submit-error>حدث خطأ أثناء الاستيراد. يرجى المحاولة مرة أخرى.</div>
                </div>
            </form>

            {{-- Mobile import form (shown on small screens) --}}
            <form action="{{ route('dashboard.import-voters') }}"
                  class="voters-import-form import-form-mobile"
                  enctype="multipart/form-data"
                  method="POST"
                  novalidate
                  data-voters-import-form="mobile">
                @csrf

                <div class="import-stack">
                    <div class="import-stack-item">
                        <label class="form-label">الانتخابات</label>
                        <select name="election" class="form-select import-field" required aria-invalid="false" data-field="election">
                            <option value="" selected disabled>اختر الانتخابات</option>
                            @foreach ($elections as $election)
                                <option value="{{ $election->id }}">{{ $election->name . "(" . $election->id . ")" }}</option>
                            @endforeach
                        </select>
                        <div class="import-help">اختر الانتخابات المرتبطة بالملف الذي سترفعه.</div>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <a href="#"
                               class="btn btn-outline-secondary btn-sm import-view-data disabled"
                               data-view-data
                               data-base-url="{{ route('dashboard.import-voters-data') }}"
                               aria-disabled="true">عرض الداتا</a>
                        </div>
                        <div class="import-error import-error-election d-none">يرجى اختيار الانتخابات.</div>
                    </div>

                    <div class="import-stack-item">
                        <label class="form-label">ملف الاستيراد</label>
                        <input type="file" class="form-control import-field" name="import" accept=".xlsx,.xls,.csv" required aria-invalid="false" data-field="file">
                        <div class="import-help">الصيغ المقبولة: .xlsx, .xls, .csv</div>
                        <div class="import-error import-error-file d-none">يرجى اختيار ملف صالح.</div>
                    </div>

                    <div class="import-stack-item">
                        <label class="form-label">طريقة الاستيراد</label>
                        <div class="import-options-stack">
                            <label class="import-option-mobile">
                                <input type="radio" name="check" value="dublicate" class="import-option-input" checked data-field="mode">
                                <div class="import-option-content">
                                    <div class="option-title">إضافة</div>
                                    <div class="option-desc">يضيف السجلات الجديدة دون حذف البيانات الحالية.</div>
                                </div>
                            </label>

                            <label class="import-option-mobile option-danger">
                                <input type="radio" name="check" value="replace" class="import-option-input" data-field="mode" data-replace-option>
                                <div class="import-option-content">
                                    <div class="option-title">استبدال</div>
                                    <div class="option-desc">يحذف البيانات القديمة أولاً ثم يستورد الملف الجديد.</div>
                                </div>
                            </label>

                            <label class="import-option-mobile">
                                <input type="radio" name="check" value="status" class="import-option-input" data-field="mode">
                                <div class="import-option-content">
                                    <div class="option-title">تحديث الحالة</div>
                                    <div class="option-desc">يحدّث حالة الحضور حسب الملف دون استيراد كامل البيانات.</div>
                                </div>
                            </label>
                        </div>

                        <div class="import-help mt-2">اختر طريقة الاستيراد المناسبة لملفك.</div>
                        <div class="import-help mt-1">تحذير: خيار الاستبدال يمسح البيانات الحالية.</div>
                        <div class="import-error import-error-mode d-none">يرجى اختيار طريقة الاستيراد.</div>
                    </div>

                    <div class="import-stack-item">
                        <div class="import-help">تأكد من توافق الملف مع القالب قبل الإرسال.</div>
                        <button type="submit" class="btn btn-custom px-4 import-submit w-100" data-action="submit">
                            <span class="submit-text">بدء الاستيراد</span>
                            <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>

                    <div class="import-stack-item">
                        <div class="import-progress d-none" data-progress>
                            <div class="import-progress-bar" data-progress-bar></div>
                        </div>
                        <div class="import-help import-progress-text mt-2 d-none" data-progress-text>جاري رفع الملف...</div>
                        <div class="import-error import-error-submit d-none" data-submit-error>حدث خطأ أثناء الاستيراد. يرجى المحاولة مرة أخرى.</div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Replace confirmation modal (scoped) --}}
    <div class="modal fade" tabindex="-1" aria-hidden="true" data-modal="replaceConfirm">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تأكيد استبدال البيانات</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    هذا الخيار سيحذف البيانات القديمة قبل استيراد الملف الجديد. هل تريد المتابعة؟
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-danger" data-action="confirm-replace">نعم، استبدل البيانات</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary modal (scoped) --}}
    <div class="modal fade" tabindex="-1" aria-hidden="true" data-modal="importSummary">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">نتيجة الاستيراد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body" data-summary-body>
                    تم استلام نتيجة الاستيراد.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        function tryInit(attempt) {
            attempt = attempt || 0;
            // Wait until Bootstrap is available (scripts load at end of layout)
            if (!window.bootstrap || !window.bootstrap.Modal) {
                if (attempt < 60) {
                    setTimeout(function () { tryInit(attempt + 1); }, 50);
                }
                return;
            }

            var root = document.querySelector('[data-voters-import-root]');
            if (!root) return;
            if (root.dataset.votersImportInit === '1') return;
            root.dataset.votersImportInit = '1';

            var forms = Array.prototype.slice.call(root.querySelectorAll('[data-voters-import-form]'));
            if (!forms.length) return;

            var replaceModalEl = root.querySelector('[data-modal="replaceConfirm"]');
            var summaryModalEl = root.querySelector('[data-modal="importSummary"]');

            if (replaceModalEl && replaceModalEl.parentNode !== document.body) {
                document.body.appendChild(replaceModalEl);
            }

            if (summaryModalEl && summaryModalEl.parentNode !== document.body) {
                document.body.appendChild(summaryModalEl);
            }

            var replaceModal = replaceModalEl ? new bootstrap.Modal(replaceModalEl) : null;
            var summaryModal = summaryModalEl ? new bootstrap.Modal(summaryModalEl) : null;
            var summaryBody = root.querySelector('[data-summary-body]');

            var pendingConfirm = null;

        function toggleError(node, show) {
            if (!node) return;
            node.classList.toggle('d-none', !show);
        }

        function setFieldState(field, isValid) {
            if (!field) return;
            field.classList.remove('is-valid', 'is-invalid');
            field.classList.add(isValid ? 'is-valid' : 'is-invalid');
            field.setAttribute('aria-invalid', String(!isValid));
        }

        function setLoadingState(form, isLoading) {
            var submitButton = form.querySelector('[data-action="submit"]');
            if (!submitButton) return;
            var submitText = submitButton.querySelector('.submit-text');
            var submitSpinner = submitButton.querySelector('.spinner-border');
            submitButton.disabled = !!isLoading;
            submitButton.classList.toggle('is-loading', !!isLoading);
            if (submitSpinner) submitSpinner.classList.toggle('d-none', !isLoading);
            if (submitText) submitText.textContent = isLoading ? 'جاري الاستيراد...' : 'بدء الاستيراد';
        }

        function showProgress(form, percent) {
            var wrap = form.querySelector('[data-progress]');
            var bar = form.querySelector('[data-progress-bar]');
            var text = form.querySelector('[data-progress-text]');
            if (wrap) wrap.classList.remove('d-none');
            if (text) {
                text.classList.remove('d-none');
                text.textContent = percent < 100 ? ('جاري رفع الملف... ' + percent + '%') : 'تم رفع الملف، جاري المعالجة...';
            }
            if (bar) bar.style.width = percent + '%';
        }

        function resetProgress(form) {
            var wrap = form.querySelector('[data-progress]');
            var bar = form.querySelector('[data-progress-bar]');
            var text = form.querySelector('[data-progress-text]');
            if (wrap) wrap.classList.add('d-none');
            if (text) text.classList.add('d-none');
            if (bar) bar.style.width = '0%';
        }

        function renderSummary(summary) {
            if (!summary || !summaryBody) return;
            var lines = [];
            if (summary.total !== undefined && summary.total !== null) lines.push('إجمالي الصفوف: ' + (summary.total ?? 0));
            lines.push('تمت المعالجة بنجاح: ' + (summary.success ?? 0));
            if (summary.created !== undefined && summary.created !== null) lines.push('سجلات جديدة: ' + (summary.created ?? 0));
            if (summary.existing !== undefined && summary.existing !== null) lines.push('سجلات موجودة: ' + (summary.existing ?? 0));
            if (summary.updated !== undefined && summary.updated !== null) lines.push('تم تحديثها: ' + (summary.updated ?? 0));
            if (summary.duplicate_skipped !== undefined && summary.duplicate_skipped !== null) lines.push('مكررات تم تخطيها: ' + (summary.duplicate_skipped ?? 0));
            lines.push('تم تخطيها: ' + (summary.skipped ?? 0));
            lines.push('فشلت: ' + (summary.failed ?? 0));

            summaryBody.innerHTML = '<div class="alert alert-info" role="alert">'
                + '<div class="fw-bold mb-1">ملخص الاستيراد</div>'
                + lines.map(function (l) { return '<div>' + l + '</div>'; }).join('')
                + '</div>';

            if (summaryModal) summaryModal.show();
        }

        function validateForm(form) {
            var electionField = form.querySelector('[data-field="election"]');
            var fileField = form.querySelector('[data-field="file"]');
            var modeFields = form.querySelectorAll('[data-field="mode"]');

            var electionError = form.querySelector('.import-error-election');
            var fileError = form.querySelector('.import-error-file');
            var modeError = form.querySelector('.import-error-mode');

            var valid = true;

            if (!electionField || !electionField.value) {
                setFieldState(electionField, false);
                toggleError(electionError, true);
                valid = false;
            } else {
                setFieldState(electionField, true);
                toggleError(electionError, false);
            }

            if (!fileField || !fileField.files || !fileField.files.length) {
                setFieldState(fileField, false);
                toggleError(fileError, true);
                valid = false;
            } else {
                var name = fileField.files[0].name || '';
                var ok = /\.(xlsx|xls|csv)$/i.test(name);
                setFieldState(fileField, ok);
                toggleError(fileError, !ok);
                valid = valid && ok;
            }

            var modeSelected = false;
            for (var i = 0; i < modeFields.length; i++) {
                if (modeFields[i].checked) {
                    modeSelected = true;
                    break;
                }
            }
            toggleError(modeError, !modeSelected);
            if (!modeSelected) valid = false;

            return valid;
        }

        function updateViewDataLink(form) {
            var electionField = form.querySelector('[data-field="election"]');
            var viewBtn = form.querySelector('[data-view-data]');
            if (!viewBtn) return;

            var baseUrl = viewBtn.dataset.baseUrl || '';
            if (!electionField || !electionField.value) {
                viewBtn.classList.add('disabled');
                viewBtn.setAttribute('aria-disabled', 'true');
                viewBtn.setAttribute('href', '#');
                return;
            }

            viewBtn.classList.remove('disabled');
            viewBtn.setAttribute('aria-disabled', 'false');
            viewBtn.setAttribute('href', baseUrl + '?election=' + encodeURIComponent(electionField.value));
        }

            // Replace confirmation handler (single instance)
            var confirmReplaceBtn = root.querySelector('[data-action="confirm-replace"]');

            if (replaceModalEl) {
                replaceModalEl.addEventListener('hidden.bs.modal', function () {
                    pendingConfirm = null;

                    if (document.querySelector('.modal.show')) {
                        return;
                    }

                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('padding-right');
                    Array.prototype.slice.call(document.querySelectorAll('.modal-backdrop')).forEach(function (node) {
                        node.parentNode && node.parentNode.removeChild(node);
                    });
                });
            }

            if (confirmReplaceBtn) {
                confirmReplaceBtn.addEventListener('click', function (event) {
                    event.preventDefault();
                    if (!pendingConfirm) return;
                    var action = pendingConfirm;
                    pendingConfirm = null;
                    if (replaceModal) replaceModal.hide();
                    action();
                });
            }

            forms.forEach(function (form) {
            var isSubmitting = false;
            var replaceConfirmed = false;

            var electionField = form.querySelector('[data-field="election"]');
            var fileField = form.querySelector('[data-field="file"]');
            var modeFields = form.querySelectorAll('[data-field="mode"]');
            var replaceField = form.querySelector('[data-replace-option]');

            var submitError = form.querySelector('[data-submit-error]');

            function submitWithProgress() {
                if (isSubmitting) return;
                isSubmitting = true;
                toggleError(submitError, false);
                setLoadingState(form, true);
                showProgress(form, 0);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', form.action, true);
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.upload.addEventListener('progress', function (event) {
                    if (event.lengthComputable) {
                        var percent = Math.round((event.loaded / event.total) * 100);
                        showProgress(form, percent);
                    }
                });

                xhr.onreadystatechange = function () {
                    if (xhr.readyState !== XMLHttpRequest.DONE) return;

                    var responseJson = null;
                    try {
                        responseJson = xhr.responseText ? JSON.parse(xhr.responseText) : null;
                    } catch (e) {
                        responseJson = null;
                    }

                    if (xhr.status >= 200 && xhr.status < 400) {
                        if (responseJson && responseJson.summary) {
                            renderSummary(responseJson.summary);
                        }
                        setLoadingState(form, false);
                        resetProgress(form);
                        isSubmitting = false;
                        if (fileField) fileField.value = '';
                        replaceConfirmed = false;
                        return;
                    }

                    if (xhr.status === 422 && responseJson && responseJson.errors) {
                        var errors = responseJson.errors;
                        var electionError = form.querySelector('.import-error-election');
                        var fileError = form.querySelector('.import-error-file');
                        var modeError = form.querySelector('.import-error-mode');

                        if (errors.election && electionField) {
                            setFieldState(electionField, false);
                            if (electionError) electionError.textContent = errors.election[0];
                            toggleError(electionError, true);
                        }
                        if (errors.import && fileField) {
                            setFieldState(fileField, false);
                            if (fileError) fileError.textContent = errors.import[0];
                            toggleError(fileError, true);
                        }
                        if (errors.check && modeError) {
                            modeError.textContent = errors.check[0];
                            toggleError(modeError, true);
                        }

                        setLoadingState(form, false);
                        resetProgress(form);
                        isSubmitting = false;
                        return;
                    }

                    if (responseJson && responseJson.message && submitError) {
                        submitError.textContent = responseJson.message;
                    }
                    toggleError(submitError, true);
                    setLoadingState(form, false);
                    resetProgress(form);
                    isSubmitting = false;
                };

                xhr.onerror = function () {
                    toggleError(submitError, true);
                    setLoadingState(form, false);
                    resetProgress(form);
                    isSubmitting = false;
                };

                xhr.send(new FormData(form));
            }

            form.addEventListener('submit', function (event) {
                if (!validateForm(form)) {
                    event.preventDefault();
                    return;
                }

                // Replace requires confirmation
                if (replaceField && replaceField.checked && !replaceConfirmed && replaceModal) {
                    event.preventDefault();
                    pendingConfirm = function () {
                        replaceConfirmed = true;
                        submitWithProgress();
                    };
                    replaceModal.show();
                    return;
                }

                event.preventDefault();
                submitWithProgress();
            });

            if (electionField) {
                electionField.addEventListener('change', function () {
                    validateForm(form);
                    updateViewDataLink(form);
                });
            }
            if (fileField) {
                fileField.addEventListener('change', function () {
                    validateForm(form);
                });
            }

            Array.prototype.forEach.call(modeFields, function (field) {
                field.addEventListener('change', function () {
                    if (!replaceField || !replaceField.checked) {
                        replaceConfirmed = false;
                    }
                    validateForm(form);

                    // Selected-state styling (scoped, no IDs)
                    var labels = form.querySelectorAll('.import-option, .import-option-mobile');
                    Array.prototype.forEach.call(labels, function (lb) {
                        lb.classList.remove('is-selected');
                    });
                    var checked = form.querySelector('[data-field="mode"]:checked');
                    if (checked) {
                        var parentLabel = checked.closest('label');
                        if (parentLabel) parentLabel.classList.add('is-selected');
                    }
                });
            });

            // Initial selected-state
            (function initSelected() {
                var labels = form.querySelectorAll('.import-option, .import-option-mobile');
                Array.prototype.forEach.call(labels, function (lb) {
                    lb.classList.remove('is-selected');
                });
                var checked = form.querySelector('[data-field="mode"]:checked');
                if (checked) {
                    var parentLabel = checked.closest('label');
                    if (parentLabel) parentLabel.classList.add('is-selected');
                }
            })();

            updateViewDataLink(form);
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                tryInit(0);
            }, { once: true });
        } else {
            tryInit(0);
        }
    })();
</script>
