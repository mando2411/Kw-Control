@php
    $regionLabel = $regionLabel ?? 'المنطقة';
@endphp

<div class="modal modal-md rtl" id="elkshoofDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">بيانات الناخب</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <form action="{{ route('export') }}" method="GET" id="export">
                    <input type="hidden" name="search_id" id="search_id" value="">

                    <div class="d-flex align-items-center mb-3">
                        <label class="labelStyle pt-2 pb-1 rounded-3" for="na5ebArea">{{ $regionLabel }}</label>
                        <input type="text" class="form-control bg-secondary bg-opacity-25" value="" name="region" id="na5ebArea">
                    </div>

                    <div class="row g-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <input checked disabled type="checkbox" name="columns[]" value="name" id="na5ebName">
                                <label for="na5ebName">اسم الناخب</label>
                            </div>
                            <div class="d-flex align-items-center">
                                <input checked type="checkbox" name="columns[]" value="family" id="na5ebFamilyName">
                                <label for="na5ebFamilyName">العائلة</label>
                            </div>
                            <div class="d-flex align-items-center">
                                <input type="checkbox" class="ms-2" name="columns[]" value="age" id="na5ebAge">
                                <select name="na5ebAge" class="border-0 p-1">
                                    <option value="الميلاد / العمر">الميلاد / العمر</option>
                                    <option value=" العمر">العمر</option>
                                    <option value=" سنة الميلاد">سنة الميلاد</option>
                                    <option value="تاريخ الميلاد">تاريخ الميلاد</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <input checked type="checkbox" name="columns[]" value="alsndok" id="na5ebRejestNumber">
                                <label for="na5ebRejestNumber">رقم القيد</label>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <input type="checkbox" name="columns[]" value="phone" id="na5ebPhone">
                                <label for="na5ebPhone">الهاتف</label>
                            </div>
                            <div class="d-flex align-items-center">
                                <input type="checkbox" name="columns[]" value="type" id="na5ebType">
                                <label for="na5ebType">الجنس</label>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <input type="checkbox" class="ms-2" name="columns[]" value="created_at" id="na5ebRejesterDate">
                                <label class="labelStyle" for="na5ebRejesterDate">تاريخ القيد</label>
                            </div>
                            <div class="d-flex align-items-center">
                                <input type="checkbox" name="columns[]" value="region" id="na5ebRejon">
                                <label for="na5ebRejon">المنطقة</label>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <input type="checkbox" name="columns[]" value="committee" id="na5ebCommitte">
                                <label for="na5ebCommitte">اللجنة</label>
                            </div>
                            <div class="d-flex align-items-center">
                                <input type="checkbox" name="columns[]" value="madrasa" id="na5ebSchool">
                                <label for="na5ebSchool">مدرسة الانتخاب</label>
                            </div>
                            <div class="d-flex align-items-center">
                                <input checked type="checkbox" name="columns[]" value="restricted" id="na5ebRejesterStatus">
                                <label for="na5ebRejesterStatus">حالة القيد</label>
                            </div>
                            <div class="d-flex align-items-center">
                                <input type="checkbox" name="columns[]" value="" id="na5ebCheckedTime">
                                <label for="na5ebCheckedTime">وقت التصويت</label>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex align-items-center">
                        <label class="labelStyle" for="sorted">ترتيب</label>
                        <select name="sorted" id="sorted" class="form-control">
                            <option value="asc">أبجدي</option>
                            <option value="phone">الهاتف</option>
                            <option value="asc">الالتزام</option>
                        </select>
                    </div>

                    <input type="hidden" name="type" id="type">
                    <div class="rtl my-4 text-center">
                        <a href="#"><button type="button" class="btn btn-primary button" value="PDF">PDF</button></a>
                        <a href="#"><button type="button" class="btn btn-success button" value="Excel">Excel</button></a>
                        <a href="#"><button type="button" class="btn btn-secondary button" value="print">طباعة</button></a>
                        <a href="#"><button type="button" class="btn btn-secondary button" value="show">عرض</button></a>
                    </div>

                    <p class="text-danger">* ملاحظة لا يمكن استخراج البيانات الضخمة عبر ملف PDF</p>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pdfe">
                        <label class="labelStyle mb-0" for="sendToNa5eb">ارسال PDF عبر whatsapp</label>
                        <input type="text" inputmode="numeric" dir="ltr" autocomplete="off"
                            class="form-control bg-secondary bg-opacity-25 flex-grow-1" style="min-width: 180px;"
                            name="to" id="sendToNa5eb" placeholder="رقم الهاتف">
                        <button type="button" class="btn btn-primary button" value="Send">ارسال</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
