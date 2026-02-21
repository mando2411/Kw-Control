<!-- Unified Contractor Data Modal -->
<style>
#mota3ahdeenDataModern.modal.show {
    display: flex !important;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

#mota3ahdeenDataModern .modal-dialog {
    width: 100%;
    max-width: min(1080px, calc(100vw - 2rem));
    margin: 1.75rem auto !important;
}

@media (max-width: 991px) {
    #mota3ahdeenDataModern.modal.show {
        padding: .5rem;
    }

    #mota3ahdeenDataModern .modal-dialog {
        max-width: none;
        margin: .5rem auto !important;
    }
}
</style>
<div class="modal modal-lg rtl" id="mota3ahdeenDataModern" tabindex="-1" aria-labelledby="mota3ahdeenDataModernLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="mota3ahdeenDataModernLabel">
                    بيانات المتعهد
                </h1>
                <div id="contractorModalStatus" class="cm-modal-status" aria-live="polite"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <label class="labelStyle">تعيين المتعهد</label>
                <form class="d-flex align-items-center justify-content-around flex-wrap">

                    <div class="checkMota3ahed mt-2 d-flex justify-content-between flex-wrap">
                        <label class="mx-1 mb-2 btn btn-outline-success " id="moltazem-l"
                            for="moltazem">ملتزم

                            <input type="radio" data-bs-target="edit-mot" class="visually-hidden"
                                name="status" id="moltazem" value="1">
                        </label>

                        <label class="mx-1 mb-2 btn  btn-outline-warning" id="follow-l" for="follow">قيد
                            المتابعة

                            <input type="radio" data-bs-target="edit-mot" class="visually-hidden"
                                name="status" id="follow" value="0">
                        </label>


                    </div>

                    <div class="checkMota3ahed mt-2 d-flex justify-content-between flex-wrap">

                        <label class="mx-1 mb-2 btn btn-outline-secondary" for="general">عام

                            <input type="radio" class="visually-hidden" name="mota3ahdeenType"
                                id="general" value="general">
                        </label>

                        <label class="mx-1 mb-2 btn btn-outline-danger" for="private">خاص

                            <input type="radio" class="visually-hidden" name="mota3ahdeenType"
                                id="private" value="private">
                        </label>

                    </div>

                    <div class="moreSearch my-2 w-100 text-center">
                        <div role="button" class="w-100 btn btn-secondary px-5 fw-semibold">
                            اعدادات
                        </div>
                        <div class="description d-none p-2">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center w-100">
                                    <input type="checkbox" name="settingMota3ahed" id="settingCanSearch"
                                        checked>
                                    <label class="w-100 me-2" for="settingCanSearch">تفعيل امكانية
                                        البحث</label>
                                </div>
                                <div class=" d-flex align-items-center w-100">
                                    <input type="checkbox" name="settingMota3ahed" id="settingCanDelet"
                                        checked>
                                    <label class="w-100 me-2" for="settingCanDelet">يستطيع حذف مضامينة</label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <label class="labelStyle" for="changparent_id">تغيير المتعهد الرئيسى</label>
                                <select name="parent_id" data-bs-target="edit-mot" id="changparent_id"
                                    class="form-control py-1">

                                    <option value="الكل" disabled>الكل</option>
                                    @foreach ($parents as $item)
                                        <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-primary w-100">حفظ الأعدادات</button>
                            <hr>
                        </div>
                    </div>
                </form>

                <button class="btn btn-primary d-block me-auto" id="delete-con" value="">حذف
                    المتعهد</button>

                <div class="d-flex align-items-center mt-3 border">
                    <label class="labelStyle" for="trustingRate">المصداقيه</label>
                    <input data-bs-target="edit-mot" type="range" name="trust" id="trustingRate"
                        value="0" min="0" class="w-100">
                    <span class="bg-body-secondary p-2 px-3 d-flex">% <span id="trustText"
                            class="fw-semibold">0</span></span>
                </div>

                <div class="mt-3">
                    <div class="d-flex align-items-center mb-1">
                        <label class="labelStyle" class="" for="nameMota3ahed">الاسم </label>
                        <input data-bs-target="edit-mot" type="text" class="form-control py-1"
                            name="name" id="nameMota3ahed" value="" min="" />
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <label class="labelStyle" class="" for="phoneMota3ahed"> الهاتف </label>
                        <input data-bs-target="edit-mot" type="text" class="form-control py-1"
                            name="phone" id="phoneMota3ahed" value="144254435" min="0" />
                        <a href=""
                            class="d-inline-block px-2 py-1 mx-1 text-white bg-primary rounded-circle"><i
                                class="pt-1 fs-5 fa fa-phone"></i></a>
                        <a href=""
                            class="d-inline-block px-2 py-1 mx-1 text-white bg-success rounded-circle"><i
                                class="pt-1 fs-5 fa-brands fa-whatsapp"></i></a>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <label class="labelStyle" class="" for="noteMota3ahed">ملاحظات </label>
                        <textarea data-bs-target="edit-mot" class="form-control py-1" name="note" id="noteMota3ahed" value=""
                            min="">
              </textarea>
                    </div>
                </div>
                <hr>

                <div class="text-center">
                    <span>رابط المتعهد</span>
                </div>
                <div class="text-center">
                    <a href="#" id="RedirectLink" class="btn btn-secondary mb-1" target="_blank" rel="noopener noreferrer">
                        <i class="fa fa-book"></i>
                        <span>الدخول للرابط</span>
                    </a>

                    <button type="button" class="btn btn-secondary mb-1" id="copyConUrlBtn">
                        <i class="fa fa-book"></i>
                        <span id="copyConUrlText">نسخ الرابط</span>
                    </button>
                </div>
                <div class="d-flex justify-content-center align-items-center mt-3">
                    <div class="form-group text-center">
                        <input type="number" id="phone_wa" class="form-control py-1 d-inline-block bg-secondary bg-opacity-25"
                            style="width: auto; margin-right: 10px;" placeholder="رقم الهاتف">
                            <input type="hidden" value="{{message()}}" id="message">
                        <a id="whatsapp-link" target="_blank">
                            <button class="btn"
                                style="background-color: #25D366; color: white; border: none; padding: 10px 20px; cursor: pointer;">
                                <i class="fab fa-whatsapp"></i>
                                <span>ارسال الرابط</span>
                            </button>
                        </a>
                    </div>
                </div>
                <div class="text-white text-center rtl rounded-3">
                    <div class="cm-modal-panels text-end">
                        <div class="Delete_names d-none">
                            <h5 class="cm-panel-toggle cPointer">
                                <span class="cm-label"><i class="fa fa-trash"></i> أسماء محذوفة</span>
                                <span class="cm-count" id="deletes_count">3</span>
                            </h5>

                            <table class="table rtl overflow-hidden rounded-3 text-center d-none">
                                <thead class="table-secondary border-0 border-secondary border-bottom border-2">
                                    <tr>
                                        <th>#</th>
                                        <th class="w150">الأسم</th>
                                        <th>الهاتف</th>
                                    </tr>
                                </thead>
                                <tbody id="deletes_data">

                                </tbody>
                            </table>

                        </div>

                        <div class="Search_logs d-none">
                            <h5 class="cm-panel-toggle cPointer">
                                <span class="cm-label"><i class="fa fa-clock-rotate-left"></i> سجل عمليات البحث</span>
                                <span class="cm-count" id="log_count">0</span>
                            </h5>
                            <table class="table rtl overflow-hidden rounded-3 text-center d-none">
                                <thead class="table-secondary border-0 border-secondary border-bottom border-2">
                                    <tr>
                                        <th class="w150">البحث</th>
                                        <th>الوقت</th>
                                    </tr>
                                </thead>
                                <tbody id="log_data">

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <h3 class="bg-secondary py-1">
                        كشف المتعهد
                        <span class="bg-dark rounded-3 fs-6 px-2 py-1 me-2">العدد<span
                                class="tableNumber me-2" id="voters_numberss">2</span></span>
                    </h3>

                    <form action="" method="POST" id="form-attach">
                        @csrf
                        <div class="d-flex justify-content-between">
                            <label class="labelStyle" class="text-black">التطبيق على المحدد</label>
                            <input type="hidden" id="last_id" name="id" value="sda">
                            <select name="addMota3ahed" id="addMota3ahed" class="form-control py-1">
                                <option value="" disabled selected>--نقل المحددين الى متعهد اخر--
                                </option>
                                <option value="delete" class="btn btn-danger">حذف المحددين</option>
                                <option value="" hidden></option>
                                @foreach ($children as $i)
                                    <option value="{{ $i->id }}">{{ $i->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary px-3" id="all_voters_modern">تنفيذ</button>

                        </div>
                    </form>

                    <button type="button" id="smOpenExport" class="my-2 btn btn-dark"> استخراج الكشوف</button>


                    <div class="table-responsive mt-2">
                        <table class="table rtl overflow-hidden rounded-3 text-center">
                            <thead class="table-secondary border-0 border-secondary border-bottom border-2">
                                <tr>
                                    <th>
                                        <button class="btn btn-secondary all">الكل</button>
                                    </th>
                                    <th>#</th>
                                    <th class="w150">الأسم</th>
                                    <th>النسبة</th>
                                    <th>الهاتف</th>
                                    <th>أدوات</th>
                                </tr>
                            </thead>
                            <tbody id="voters_con">

                            </tbody>
                        </table>
                    </div>

                </div>


            </div>
        </div>
    </div>
</div>

<div class="modal fade rtl sm-export-modal" id="smExportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                        <div class="modal-header">
                                <div>
                                        <h5 class="sm-export-title">استخراج الكشوف</h5>
                                        <p class="sm-export-sub">حدد الأعمدة ونوع الإخراج ثم صدّر النتائج المحددة.</p>
                                </div>
                                <button type="button" id="smExportCloseBtn" class="btn-close" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                                <form action="{{ route('export') }}" method="GET" id="smExportForm">
                                    <input type="hidden" name="search_id" id="smExportSearchId" value="">
                                        <input type="hidden" name="source" value="contractors">

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

                                        <p class="text-danger small mb-2">* ملاحظة: لا يمكن استخراج البيانات الضخمة عبر ملف PDF</p>

                                        <div class="sm-export-section mb-0">
                                            <h6 class="sm-export-section-title">إرسال PDF عبر WhatsApp</h6>
                                                <div class="d-flex gap-2 align-items-center">
                                                        <input type="text" inputmode="numeric" dir="ltr" class="form-control" id="smExportWhatsappTo" name="to" placeholder="رقم الهاتف لإرسال WhatsApp">
                                                <button type="button" class="btn btn-outline-primary sm-export-action" value="Send">إرسال</button>
                                                </div>
                                        </div>
                                </form>
                        </div>
                </div>
        </div>
</div>
