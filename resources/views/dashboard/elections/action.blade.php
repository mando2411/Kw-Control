<a href="{{ route('dashboard.elections.edit', $id) }}" title="تعديل" aria-label="تعديل">
    <i class="fa fa-edit"></i>
</a>

<a data-delete-url="{{ route('dashboard.elections.destroy', $id) }}" href="javascript:;"
   type="button" class="btn-delete-resource-modal" data-bs-toggle="modal" data-bs-target="#deleteResourceModal" title="حذف" aria-label="حذف">
    <i class="fa fa-trash"></i>
</a>
