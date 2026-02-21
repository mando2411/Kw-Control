@php
    $includeSearchId = $includeSearchId ?? false;
    $searchIdValue = $searchIdValue ?? '';
    $sourceValue = $sourceValue ?? null;
    $whatsappInputId = $whatsappInputId ?? 'smExportWhatsappTo';
    $whatsappPlaceholder = $whatsappPlaceholder ?? 'رقم الهاتف لإرسال WhatsApp';
@endphp

<div class="modal fade rtl sm-export-modal" id="smExportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="sm-export-title">استخراج الكشوف</h5>
                    <p class="sm-export-sub">حدد الأعمدة ونوع الإخراج ثم صدّر النتائج المحددة.</p>
                </div>
                <button type="button" id="smExportCloseBtn" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('export') }}" method="GET" id="smExportForm">
                    @if($includeSearchId)
                        <input type="hidden" name="search_id" id="smExportSearchId" value="{{ $searchIdValue }}">
                    @endif
                    @if(!is_null($sourceValue))
                        <input type="hidden" name="source" value="{{ $sourceValue }}">
                    @endif

                    <div class="sm-export-section">
                        <h6 class="sm-export-section-title">أعمدة الكشف</h6>
                        <div class="row g-2">
                            <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" checked disabled type="checkbox" value="name"><span class="sm-chip-pill">اسم الناخب</span></label></div>
                            <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" checked type="checkbox" name="columns[]" value="family"><span class="sm-chip-pill">العائلة</span></label></div>
                            <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" type="checkbox" name="columns[]" value="age"><span class="sm-chip-pill">العمر</span></label></div>
                            <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" type="checkbox" name="columns[]" value="phone"><span class="sm-chip-pill">الهاتف</span></label></div>
                            <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" type="checkbox" name="columns[]" value="type"><span class="sm-chip-pill">الجنس</span></label></div>
                            <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" type="checkbox" name="columns[]" value="madrasa"><span class="sm-chip-pill">مدرسة الانتخاب</span></label></div>
                            <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" checked type="checkbox" name="columns[]" value="restricted"><span class="sm-chip-pill">حالة القيد</span></label></div>
                            <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" type="checkbox" name="columns[]" value="created_at"><span class="sm-chip-pill">تاريخ القيد</span></label></div>
                            <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" type="checkbox" name="columns[]" value="checked_time"><span class="sm-chip-pill">وقت التصويت</span></label></div>
                        </div>
                    </div>

                    <div class="sm-export-section">
                        <h6 class="sm-export-section-title">ترتيب النتائج</h6>
                        <select name="sorted" class="form-select">
                            <option value="asc">أبجدي</option>
                            <option value="phone">الهاتف</option>
                            <option value="commitment">الالتزام</option>
                        </select>
                    </div>

                    <input type="hidden" name="type" id="smExportType">

                    <div class="sm-export-section">
                        <h6 class="sm-export-section-title">إجراء الإخراج</h6>
                        <div class="sm-export-actions">
                            <button type="button" class="btn btn-primary sm-export-action" value="PDF">PDF</button>
                            <button type="button" class="btn btn-success sm-export-action" value="Excel">Excel</button>
                            <button type="button" class="btn btn-secondary sm-export-action" value="print">طباعة</button>
                            <button type="button" class="btn btn-secondary sm-export-action" value="show">عرض</button>
                        </div>
                    </div>

                    <p class="text-danger small mb-2">* ملاحظة لا يمكن استخراج البيانات الضخمة عبر ملف PDF</p>

                    <div class="sm-export-section mb-0">
                        <h6 class="sm-export-section-title">إرسال PDF عبر WhatsApp</h6>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" inputmode="numeric" dir="ltr" class="form-control" id="{{ $whatsappInputId }}" name="to" placeholder="{{ $whatsappPlaceholder }}">
                            <button type="button" class="btn btn-outline-primary sm-export-action" value="Send">إرسال</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
