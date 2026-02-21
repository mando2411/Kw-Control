@php
    $regionLabel = $regionLabel ?? 'المنطقة';
@endphp

@include('dashboard.partials.sm-export-modal', [
    'modalId' => 'elkshoofDetails',
    'closeBtnId' => 'elkshoofCloseBtn',
    'formId' => 'export',
    'typeInputId' => 'type',
    'includeSearchId' => true,
    'searchIdInputId' => 'search_id',
    'searchIdInputName' => 'search_id',
    'showRegionField' => true,
    'regionLabel' => $regionLabel,
    'regionInputId' => 'na5ebArea',
    'regionInputName' => 'region',
    'sortedSelectName' => 'sorted',
    'actionButtonClass' => 'button',
    'whatsappInputId' => 'sendToNa5eb',
    'whatsappLabel' => 'ارسال PDF عبر whatsapp',
    'whatsappPlaceholder' => 'مثال: 5XXXXXXXX أو 9655XXXXXXXX',
    'columns' => [
        ['id' => 'na5ebName', 'value' => 'name', 'label' => 'اسم الناخب', 'checked' => true, 'disabled' => true],
        ['id' => 'na5ebFamilyName', 'value' => 'family', 'label' => 'العائلة', 'checked' => true],
        [
            'id' => 'na5ebAge',
            'value' => 'age',
            'label' => 'العمر',
            'select' => [
                'name' => 'na5ebAge',
                'class' => 'form-select mt-1',
                'options' => [
                    ['value' => 'الميلاد / العمر', 'label' => 'الميلاد / العمر'],
                    ['value' => 'العمر', 'label' => 'العمر'],
                    ['value' => 'سنة الميلاد', 'label' => 'سنة الميلاد'],
                    ['value' => 'تاريخ الميلاد', 'label' => 'تاريخ الميلاد'],
                ],
            ],
        ],
        ['id' => 'na5ebRejestNumber', 'value' => 'alsndok', 'label' => 'رقم القيد', 'checked' => true],
        ['id' => 'na5ebPhone', 'value' => 'phone', 'label' => 'الهاتف'],
        ['id' => 'na5ebType', 'value' => 'type', 'label' => 'الجنس'],
        ['id' => 'na5ebRejesterDate', 'value' => 'created_at', 'label' => 'تاريخ القيد'],
        ['id' => 'na5ebRejon', 'value' => 'region', 'label' => 'المنطقة'],
        ['id' => 'na5ebCommitte', 'value' => 'committee', 'label' => 'اللجنة'],
        ['id' => 'na5ebSchool', 'value' => 'madrasa', 'label' => 'مدرسة الانتخاب'],
        ['id' => 'na5ebRejesterStatus', 'value' => 'restricted', 'label' => 'حالة القيد', 'checked' => true],
        ['id' => 'na5ebCheckedTime', 'value' => 'checked_time', 'label' => 'وقت التصويت'],
    ],
    'sortOptions' => [
        ['value' => 'asc', 'label' => 'أبجدي'],
        ['value' => 'phone', 'label' => 'الهاتف'],
        ['value' => 'commitment', 'label' => 'الالتزام'],
    ],
    'actionButtons' => [
        ['value' => 'PDF', 'label' => 'PDF', 'class' => 'btn btn-primary'],
        ['value' => 'Excel', 'label' => 'Excel', 'class' => 'btn btn-success'],
        ['value' => 'print', 'label' => 'طباعة', 'class' => 'btn btn-secondary'],
        ['value' => 'show', 'label' => 'عرض', 'class' => 'btn btn-secondary'],
    ],
])
