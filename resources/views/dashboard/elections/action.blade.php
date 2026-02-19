<div class="d-inline-flex align-items-center gap-2">
    <a href="{{ route('dashboard.elections.edit', $id) }}" title="تعديل" aria-label="تعديل" class="text-primary">
        <i class="fa fa-edit fs-6"></i>
    </a>

    <a data-delete-url="{{ route('dashboard.elections.destroy', $id) }}" href="javascript:;"
       type="button" class="btn-delete-resource-modal text-danger" data-bs-toggle="modal" data-bs-target="#deleteResourceModal" title="حذف" aria-label="حذف">
        <i class="fa fa-trash fs-6"></i>
    </a>
</div>
