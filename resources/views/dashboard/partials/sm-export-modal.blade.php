@php
    $includeSearchId = $includeSearchId ?? false;
    $searchIdValue = $searchIdValue ?? '';
    $searchIdInputId = $searchIdInputId ?? 'smExportSearchId';
    $searchIdInputName = $searchIdInputName ?? 'search_id';
    $sourceValue = $sourceValue ?? null;
    $modalId = $modalId ?? 'smExportModal';
    $closeBtnId = $closeBtnId ?? 'smExportCloseBtn';
    $formId = $formId ?? 'smExportForm';
    $typeInputId = $typeInputId ?? 'smExportType';
    $sortedSelectName = $sortedSelectName ?? 'sorted';
    $whatsappInputId = $whatsappInputId ?? 'smExportWhatsappTo';
    $whatsappLabel = $whatsappLabel ?? 'إرسال PDF عبر WhatsApp';
    $whatsappPlaceholder = $whatsappPlaceholder ?? 'رقم الهاتف لإرسال WhatsApp';
    $noteText = $noteText ?? '* ملاحظة لا يمكن استخراج البيانات الضخمة عبر ملف PDF';
    $actionButtonClass = $actionButtonClass ?? 'sm-export-action';
    $showRegionField = $showRegionField ?? false;
    $regionLabel = $regionLabel ?? 'المنطقة';
    $regionInputId = $regionInputId ?? 'na5ebArea';
    $regionInputName = $regionInputName ?? 'region';
    $regionInputValue = $regionInputValue ?? '';

    $columns = $columns ?? [
        ['value' => 'name', 'label' => 'اسم الناخب', 'checked' => true, 'disabled' => true],
        ['value' => 'family', 'label' => 'العائلة', 'checked' => true],
        ['value' => 'age', 'label' => 'العمر'],
        ['value' => 'phone', 'label' => 'الهاتف'],
        ['value' => 'type', 'label' => 'الجنس'],
        ['value' => 'madrasa', 'label' => 'مدرسة الانتخاب'],
        ['value' => 'restricted', 'label' => 'حالة القيد', 'checked' => true],
        ['value' => 'created_at', 'label' => 'تاريخ القيد'],
        ['value' => 'checked_time', 'label' => 'وقت التصويت'],
    ];

    $sortOptions = $sortOptions ?? [
        ['value' => 'asc', 'label' => 'أبجدي'],
        ['value' => 'phone', 'label' => 'الهاتف'],
        ['value' => 'commitment', 'label' => 'الالتزام'],
    ];

    $actionButtons = $actionButtons ?? [
        ['value' => 'PDF', 'label' => 'PDF', 'class' => 'btn btn-primary'],
        ['value' => 'Excel', 'label' => 'Excel', 'class' => 'btn btn-success'],
        ['value' => 'print', 'label' => 'طباعة', 'class' => 'btn btn-secondary'],
        ['value' => 'show', 'label' => 'عرض', 'class' => 'btn btn-secondary'],
    ];
@endphp

<div class="modal fade rtl sm-export-modal" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="sm-export-title">استخراج الكشوف</h5>
                    <p class="sm-export-sub">حدد الأعمدة ونوع الإخراج ثم صدّر النتائج المحددة.</p>
                </div>
                <button type="button" id="{{ $closeBtnId }}" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('export') }}" method="GET" id="{{ $formId }}">
                    @if($includeSearchId)
                        <input type="hidden" name="{{ $searchIdInputName }}" id="{{ $searchIdInputId }}" value="{{ $searchIdValue }}">
                    @endif
                    @if(!is_null($sourceValue))
                        <input type="hidden" name="source" value="{{ $sourceValue }}">
                    @endif

                    @if($showRegionField)
                        <div class="d-flex align-items-center mb-3">
                            <label class="labelStyle pt-2 pb-1 rounded-3" for="{{ $regionInputId }}">{{ $regionLabel }}</label>
                            <input type="text" class="form-control bg-secondary bg-opacity-25" value="{{ $regionInputValue }}" name="{{ $regionInputName }}" id="{{ $regionInputId }}">
                        </div>
                    @endif

                    <div class="sm-export-section">
                        <h6 class="sm-export-section-title">أعمدة الكشف</h6>
                        <div class="row g-2">
                            @foreach($columns as $index => $column)
                                @php
                                    $columnId = $column['id'] ?? ('smCol' . $index);
                                    $columnName = $column['name'] ?? 'columns[]';
                                    $columnValue = $column['value'] ?? '';
                                    $columnLabel = $column['label'] ?? '';
                                    $columnClass = $column['class'] ?? 'sm-chip-input';
                                    $columnColClass = $column['colClass'] ?? 'col-6';
                                    $columnChecked = !empty($column['checked']);
                                    $columnDisabled = !empty($column['disabled']);
                                @endphp
                                <div class="{{ $columnColClass }}">
                                    <label class="sm-chip-option" for="{{ $columnId }}">
                                        <input class="{{ $columnClass }}"
                                            id="{{ $columnId }}"
                                            type="checkbox"
                                            name="{{ $columnName }}"
                                            value="{{ $columnValue }}"
                                            @checked($columnChecked)
                                            @disabled($columnDisabled)>
                                        <span class="sm-chip-pill">{{ $columnLabel }}</span>
                                    </label>
                                    @if(!empty($column['select']) && is_array($column['select']))
                                        @php
                                            $select = $column['select'];
                                            $selectName = $select['name'] ?? '';
                                            $selectClass = $select['class'] ?? 'form-select mt-1';
                                        @endphp
                                        <select name="{{ $selectName }}" class="{{ $selectClass }}">
                                            @foreach(($select['options'] ?? []) as $option)
                                                <option value="{{ $option['value'] ?? '' }}">{{ $option['label'] ?? '' }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="sm-export-section">
                        <h6 class="sm-export-section-title">ترتيب النتائج</h6>
                        <select name="{{ $sortedSelectName }}" class="form-select">
                            @foreach($sortOptions as $option)
                                <option value="{{ $option['value'] ?? '' }}">{{ $option['label'] ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="type" id="{{ $typeInputId }}">

                    <div class="sm-export-section">
                        <h6 class="sm-export-section-title">إجراء الإخراج</h6>
                        <div class="sm-export-actions">
                            @foreach($actionButtons as $button)
                                <button type="button" class="{{ $button['class'] ?? 'btn btn-secondary' }} {{ $actionButtonClass }}" value="{{ $button['value'] ?? '' }}">{{ $button['label'] ?? '' }}</button>
                            @endforeach
                        </div>
                    </div>

                    <p class="text-danger small mb-2">{{ $noteText }}</p>

                    <div class="sm-export-section mb-0">
                        <h6 class="sm-export-section-title">{{ $whatsappLabel }}</h6>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" inputmode="numeric" dir="ltr" class="form-control" id="{{ $whatsappInputId }}" name="to" placeholder="{{ $whatsappPlaceholder }}">
                            <button type="button" class="btn btn-outline-primary {{ $actionButtonClass }}" value="Send">إرسال</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
