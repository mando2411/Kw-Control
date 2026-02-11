@extends('layouts.dashboard.app')

@section('content')

<section class=" rtl">
    <div class="container">
      <div class="moreSearch mt-3">
          <div role="button" class="btn btn-primary ">
             + اضافة عضو جديد
          </div>
          <form action="{{route('dashboard.users.store')}}" method="POST" class="description d-none p-2">
            @csrf
            <div class="d-flex align-items-center my-3">
              <label class="lableStyle" for="name">الاسم</label>
              <input type="text" class="form-control py-1" placeholder="Name" name="name" id="name">
            </div>
            <div class="d-flex align-items-center mb-1">
              <label class="lableStyle" for="phone">الهاتف</label>
              <input type="text" class="form-control py-1" placeholder="phone" name="phone" id="phone">

            </div>
            <div class="d-flex align-items-center mb-1">
                <label class="lableStyle" for="licence">صلاحيات التعهد</label>
                <select class="form-control py-1" name="roles[]" id="licence">
                    <option value="" hidden>-----</option>
                    <option value="{{App\Models\Role::where('name', 'متعهد رئيسي')->first()?->id}}">متعهد رئيسى</option>
                    <option value="{{App\Models\Role::where('name', 'مسئول كنترول')->first()?->id}}">مسئول الكنترول</option>
                </select>
            </div>

            <div class="d-flex align-items-center mb-1">
                <label class="lableStyle" for="services">خدمة اجتماعية</label>
                <select class="form-control py-1" name="roles[]" id="services">
                    <option value="" hidden>-----</option>
                    <option value="{{App\Models\Role::where('name', 'الدواوين')->first()?->id}}">الدواوين</option>
                </select>
            </div>

            <div class="d-flex align-items-center mb-1">
                <label class="lableStyle" for="electionCommittes">لجان الانتخابات</label>
                <select class="form-control py-1" name="roles[]" id="electionCommittes">
                    <option value="" hidden>-----</option>
                    <option value="{{App\Models\Role::where('name', 'وكيل المدرسة')->first()?->id}}">وكيل مدرسة</option>
                    <option value="{{App\Models\Role::where('name', 'كل صلاحيات اللجان')->first()?->id}}">كامل الصلاحيات</option>
                </select>
            </div>

            <div class="d-flex align-items-center mb-1">
                <label class="lableStyle" for="websitePermissions">صلاحيات الموقع</label>
                <select class="form-control py-1" name="roles[]" id="websitePermissions">
                    <option value="" hidden>-----</option>
                    <option value="{{App\Models\Role::where('name', 'التحكم بالموقع')->first()?->id}}">التحكم بالموقع</option>
                </select>
            </div>




            <button type="submit" class="btn btn-outline-primary w-50 mx-auto d-block">أضافة</button>
          </form>
      </div>
    </div>
  </section>

  <section class="py-3 rtl">
    <div class="container ">

      <div class="d-flex align-items-center flex-wrap justify-content-evenly">
        <a href=""  class="mt-2">
          <button class="btn btn-outline-dark">الكل</button>
        </a>
        <a href="{{route('dashboard.cards',App\Models\Role::where('name',"متعهد رئيسي")->first()?->id)}}"  class="mt-2">
          <button class="btn btn-info">متعهد رئيسى</button>
        </a>
        <a href="{{route('dashboard.cards', App\Models\Role::where('name', 'مسئول كنترول')->first()?->id)}}" class="mt-2">
            <button class="btn btn-primary">مسئول الكنترول</button>
        </a>

        <a href="{{route('dashboard.cards', App\Models\Role::where('name', 'الدواوين')->first()?->id)}}" class="mt-2">
            <button class="btn btn-secondary">الدواوين</button>
        </a>

        <a href="{{route('dashboard.cards', App\Models\Role::where('name', 'وكيل المدرسة')->first()?->id)}}" class="mt-2">
            <button class="btn btn-danger">وكيل المدرسة</button>
        </a>

        <a href="{{route('dashboard.cards', App\Models\Role::where('name', 'كل صلاحيات اللجان')->first()?->id)}}" class="mt-2">
            <button class="btn btn-warning">كل صلاحيات اللجان</button>
        </a>

        <a href="{{route('dashboard.cards', App\Models\Role::where('name', 'التحكم  بالموقع')->first()?->id)}}" class="mt-2">
            <button class="btn btn-dark">التحكم بالموقع</button>
        </a>


      </div>

      <x-dashboard.partials.message-alert />

      <div class="table-responsive mt-1">
        <table
          class="table rtl overflow-hidden rounded-3 text-center"
        >
          <thead
            class="table-primary border-0 border-secondary border-bottom border-2"
          >
            <tr>

              <th>id</th>
              <th class="w150"> الاسم</th>
              <th>الهاتف</th>
              <th>أدوات</th>

            </tr>
          </thead>
          <tbody>
            @foreach ($relations['users'] as $user )
                @if (auth()->user()->id != $user->id && (auth()->user()->hasRole('Administrator') || auth()->user()->id == $user->creator_id))
                <tr>
                    <td id="user_id">{{$user->id}}</td>
                    <td>{{$user->name}}</td>
                    <td>{{$user->phone}}</td>
                    <td data-bs-toggle="modal" data-bs-target="#newMemberModal">
                        <button class="btn btn-dark"><i class="fa fa-gear"></i></button>
                    </td>
                </tr>
                @endif
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Modal newMemberModal-->
      <div
        class="modal modal-md rtl"
        id="newMemberModal"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">
                 بحث بالاسم
              </h1>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div class="modal-body">

              <form action="{{route('dashboard.user-change')}}" method="POST" class="description p-2" id="edit-user">
                @csrf
                <div class="d-flex align-items-center mb-1">
                    <label class="lableStyle" for="newMemberModalName">الاسم</label>
                    <input type="text" class="form-control py-1" placeholder="name" name="name" id="newMemberModalName">
                    <input type="hidden" name="id" id="u_id" >
                </div>
            <div class="d-flex align-items-center mb-1">
              <label class="lableStyle" for="newMemberModalPhone">الهاتف</label>
              <input type="text" class="form-control py-1" placeholder="phone" name="phone" id="newMemberModalPhone">

            </div>
            <div class="d-flex align-items-center mb-1">
              <label class="lableStyle" for="licenceModal">صلاحيات التعهد</label>
              <select class="form-control py-1" name="roles[]" id="licenceModal">
              <option value="" hidden>-----</option>
              <option value="" >متعهد رئيسى</option>
              <option value="" >مسئول الكنترول</option>
            </select>

            </div>
            <div class="d-flex align-items-center mb-1">
              <label class="lableStyle" for="servicesModal">خدمة اجتماعية</label>

              <select class="form-control py-1" name="servicesModal" id="servicesModal">
                <option value="" hidden>-----</option>
                <option value="" > الدواوين</option>
              </select>

            </div>
            <div class="d-flex align-items-center mb-1">
              <label class="lableStyle" for="electionCommittesModal">لجان الانتخابات</label>

              <select class="form-control py-1" name="electionCommittesModal" id="electionCommittesModal">
                <option value="" hidden>-----</option>
                <option value="" > وكيل مدرسة</option>
                <option value="" > كامل الصلاحيات</option>
              </select>


            </div>

            <div class="d-flex align-items-center mb-1">
              <label class="lableStyle" for="licencePlaceModal">صلاحيات الموقع</label>

              <select class="form-control py-1" name="licencePlaceModal" id="licencePlaceModal">
                <option value="" hidden>-----</option>
                <option value="" > التحكم بالموقع</option>
              </select>


            </div>



            <button id="edit" class="btn btn-primary w-100">تعديل</button>

            <div class="mt-3">
              <button class="btn btn-danger">حذف المستخدم</button>
              <button class="btn btn-warning" >اعادة تعيين الرقم السرى</button>
            </div>
          </form>

            </div>
          </div>
        </div>
      </div>

       <div class="table-responsive mt-4">
        <table
          class="table rtl overflow-hidden rounded-3 text-center"
        >
          <thead
            class="table-primary border-0 border-secondary border-bottom border-2"
          >
            <tr>

              <th>id</th>
              <th class="w150"> المندوب</th>
              <th>الهاتف</th>
              <th>اللجنة</th>

            </tr>
          </thead>
          <tbody>
        @forelse ($relations['reps'] as $rep )

        <tr>

            <td>{{$rep->id}}</td>
            <td> {{$rep->user?->name}}</td>
            <td>{{$rep->user?->phone}}</td>
            <td>{{$rep->committee ? $rep->committee->name : "لا يوجد"  }}</td>

          </tr>
        @empty

        @endforelse



          </tbody>
        </table>
      </div>

      <div class="table-responsive mt-4">
        <p class="fw-bold mb-2">شرح عن الصلاحيات</p>
        <table
        class="table rtl overflow-hidden rounded-3 "
        >
        <thead
        class="table-dark border-0 border-secondary border-bottom border-2"
        >
            <tr>


              <th colspan="2"> صلاحيات التعهد</th>


            </tr>
          </thead>
          <tbody>
            <tr>

              <td>----</td>
              <td>	لا يمكنه الإطلاع على أي صفحة تابعة للمتعهدين والمضامين</td>

            </tr>
            <tr>

              <td>متعهد رئيسي</td>
              <td>يستطيع إضافة متعهين فرعيين ومشاهدة المضامين التابعين لمتعهدينه الفرعيين</td>

            </tr>
            <tr>

              <td>مسؤول الكنترول</td>
              <td>	يستطيع إضافة متعهدين فرعيين والإطلاع على جميع المتعهدين الفرعيين وجميع المضامين</td>

            </tr>




          </tbody>
        </table>
        <table
        class="table rtl overflow-hidden rounded-3 "
        >
        <thead
        class="table-dark border-0 border-secondary border-bottom border-2"
        >
            <tr>


              <th colspan="2">  لجان الانتخابات (خاص ليوم الإنتخابات)</th>


            </tr>
          </thead>
          <tbody>
            <tr>

              <td>----</td>
              <td>	لا يمكنه الإطلاع على أي صفحة تابعة لتحضير الناخبين يوم الانتخاب وفرز الأصوات</td>

            </tr>
            <tr>

              <td> مندوب لجنة</td>
              <td>يستطيع تحضير الناخبين بلجنته المحدده و إدخال نتائج فرز اللجنة</td>

            </tr>
            <tr>

              <td> وكيل مدرسة</td>
              <td>متابعة لجان التابعة لمدرسته المحددة وتوزيع المندوبين على اللجان بالمدرسة ومتابعة عملية فرز النتائج بالمدرسة</td>

            </tr>
            <tr>

              <td>  كامل الصلاحيات</td>
              <td>توزيع الوكلاء على المدارس وتوزيع المندوبين على المدارس واللجان ومتابعة جميع احصائيات اللجان والمدارس من تحضير ناخبين وفرز النتائج</td>

            </tr>




          </tbody>
        </table>
        <table
        class="table rtl overflow-hidden rounded-3 "
        >
        <thead
        class="table-dark border-0 border-secondary border-bottom border-2"
        >
            <tr>


              <th colspan="2">  صلاحيات الموقع</th>


            </tr>
          </thead>
          <tbody>
            <tr>

              <td>----</td>
              <td>	لا يمكنه الدخول إلى وحدات التحكم الرئيسية</td>

            </tr>
            <tr>

              <td>التحكم بالموقع</td>
              <td>	إضافة أعضاء وتحديد صلاحياتهم والتحكم بادوات الموقع</td>
            </tr>
          </tbody>
        </table>
      </div>

  </section>



@endsection
