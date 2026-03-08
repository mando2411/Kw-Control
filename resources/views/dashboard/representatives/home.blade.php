@extends('layouts.dashboard.app')

@section('content')
  <div class="page-body rep-home-page" dir="rtl" style="text-align: right;">
    <x-dashboard.partials.breadcrumb title="المندوبون">
      <li class="breadcrumb-item active">إدارة المندوبين حسب المدرسة</li>
    </x-dashboard.partials.breadcrumb>

    <div class="container-fluid">
      <x-dashboard.partials.message-alert />

      <div class="card rep-home-shell-card">
        <div class="card-body rep-home-header">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0">توزيع المندوبين</h5>
            @if(admin()->can('representatives.create'))
              <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#quickAddRepresentativeForm" aria-expanded="false" aria-controls="quickAddRepresentativeForm">
                إضافة مندوب جديد
              </button>
            @endif
          </div>

          <form action="{{ route('dashboard.rep-home') }}" method="get" id="school-form" class="row g-2 align-items-end mt-2">
            <div class="col-lg-6 col-md-8">
              <label class="form-label mb-1" for="school">المدرسة</label>
              <select name="id" id="school" class="form-select">
                <option value="all" @selected(($selectedSchoolId ?? 'all') === 'all')>كل المدارس</option>
                @foreach ($relations['schools'] as $sch)
                  <option value="{{ $sch->id }}" @selected((string) ($selectedSchoolId ?? 'all') === (string) $sch->id)>
                    {{ $sch->name }}
                  </option>
                @endforeach
              </select>
            </div>
          </form>

          @if(admin()->can('representatives.create'))
            <div class="collapse mt-3" id="quickAddRepresentativeForm">
              <div class="rep-home-quick-add p-3">
                <form action="{{ route('dashboard.representatives.store') }}" method="POST" class="row g-3">
                  @csrf

                  <input type="hidden" name="election_id" id="quick_add_election_id" value="{{ old('election_id') }}">

                  <div class="col-lg-4">
                    <label class="form-label" for="quick_name">الاسم</label>
                    <input type="text" class="form-control" id="quick_name" name="name" value="{{ old('name') }}" placeholder="اسم المندوب" required>
                  </div>

                  <div class="col-lg-4">
                    <label class="form-label" for="quick_phone">الهاتف</label>
                    <input type="text" class="form-control" id="quick_phone" name="phone" value="{{ old('phone') }}" placeholder="رقم الهاتف" required>
                  </div>

                  <div class="col-lg-4">
                    <label class="form-label" for="quick_committee_id">اللجنة</label>
                    <select name="committee_id" id="quick_committee_id" class="form-select">
                      <option value="">بدون لجنة</option>
                      @foreach ($relations['committees'] as $com)
                        <option
                          value="{{ $com->id }}"
                          data-election-id="{{ $com->election_id ?? '' }}"
                          @selected((string) old('committee_id') === (string) $com->id)
                        >
                          {{ $com->name }} ({{ $com->type }})
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-12 d-flex gap-2">
                    <button class="btn btn-success" type="submit">حفظ</button>
                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#quickAddRepresentativeForm">إغلاق</button>
                  </div>
                </form>
              </div>
            </div>
          @endif
        </div>

        <div class="card-body table-responsive">
          <table class="table align-middle text-center rep-home-table">
            <thead>
              <tr>
                <th>اللجنة</th>
                <th>المندوب</th>
                <th>الهاتف</th>
                <th>الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($schools as $school)
                <tr class="table-group-row">
                  <td colspan="4" class="text-end fw-semibold">
                    {{ $school->name }}
                  </td>
                </tr>

                @if($school->committees->isEmpty())
                  <tr>
                    <td colspan="4" class="text-muted">لا توجد لجان في هذه المدرسة.</td>
                  </tr>
                  @continue
                @endif

                @foreach ($school->committees as $com)
                  @php
                    $committeeUsers = $com->users();
                  @endphp

                  @if($committeeUsers->isEmpty())
                    <tr>
                      <td>{{ $com->name }} ({{ $com->type }})</td>
                      <td colspan="3" class="text-muted">لا يوجد مندوبون في هذه اللجنة.</td>
                    </tr>
                    @continue
                  @endif

                  @foreach ($committeeUsers as $representative)
                    <tr>
                      <td>{{ $com->name }} ({{ $com->type }})</td>
                      <td>{{ $representative['name'] }}</td>
                      <td>{{ $representative['phone'] }}</td>
                      <td>
                        <button
                          type="button"
                          class="btn btn-sm btn-outline-primary js-open-edit-modal"
                          data-rep-id="{{ $representative['id'] }}"
                          data-name="{{ $representative['name'] }}"
                          data-phone="{{ $representative['phone'] }}"
                          data-committee-id="{{ $representative['committee_id'] ?? $com->id }}"
                        >
                          تعديل
                        </button>
                      </td>
                    </tr>
                  @endforeach
                @endforeach
              @empty
                <tr>
                  <td colspan="4" class="text-muted">لا توجد مدارس متاحة للعرض.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="repEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">تعديل بيانات المندوب</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="rep_edit_id">
          <div class="mb-2">
            <label for="rep_edit_name" class="form-label">الاسم</label>
            <input type="text" id="rep_edit_name" class="form-control">
          </div>
          <div class="mb-2">
            <label for="rep_edit_phone" class="form-label">الهاتف</label>
            <input type="text" id="rep_edit_phone" class="form-control">
          </div>
          <div class="mb-2">
            <label for="rep_edit_committee" class="form-label">اللجنة</label>
            <select id="rep_edit_committee" class="form-select">
              <option value="">بدون لجنة</option>
              @foreach ($relations['committees'] as $com)
                <option value="{{ $com->id }}">{{ $com->name }} ({{ $com->type }})</option>
              @endforeach
            </select>
          </div>
          <div id="rep_edit_error" class="text-danger small d-none"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إغلاق</button>
          <button type="button" class="btn btn-primary" id="rep_edit_submit">حفظ التعديل</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('css')
  <style>
    .rep-home-shell-card {
      border: 0;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 20px 46px rgba(12, 36, 64, 0.12);
    }

    .rep-home-header {
      border-bottom: 1px solid #e1e8f5;
      background: linear-gradient(160deg, #f8fbff 0%, #ffffff 100%);
    }

    .rep-home-quick-add {
      border: 1px solid #dce6f5;
      border-radius: 12px;
      background: #f9fcff;
    }

    .rep-home-table thead th {
      background: #f3f7ff;
      color: #20456a;
      border-bottom: 1px solid #dbe6f4;
      font-weight: 700;
    }

    .rep-home-table .table-group-row td {
      background: #eef4ff;
      color: #1f4b76;
    }
  </style>
@endpush

@push('js')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var schoolSelect = document.getElementById('school');
      var schoolForm = document.getElementById('school-form');
      var quickCommitteeSelect = document.getElementById('quick_committee_id');
      var quickElectionInput = document.getElementById('quick_add_election_id');

      if (schoolSelect && schoolForm) {
        schoolSelect.addEventListener('change', function () {
          schoolForm.submit();
        });
      }

      var syncQuickElection = function () {
        if (!quickCommitteeSelect || !quickElectionInput) {
          return;
        }

        var selectedOption = quickCommitteeSelect.options[quickCommitteeSelect.selectedIndex];
        quickElectionInput.value = selectedOption ? (selectedOption.dataset.electionId || '') : '';
      };

      if (quickCommitteeSelect) {
        quickCommitteeSelect.addEventListener('change', syncQuickElection);
        syncQuickElection();
      }

      var modalElement = document.getElementById('repEditModal');
      var editModal = modalElement ? new bootstrap.Modal(modalElement) : null;

      var repIdInput = document.getElementById('rep_edit_id');
      var repNameInput = document.getElementById('rep_edit_name');
      var repPhoneInput = document.getElementById('rep_edit_phone');
      var repCommitteeSelect = document.getElementById('rep_edit_committee');
      var repError = document.getElementById('rep_edit_error');
      var submitEditButton = document.getElementById('rep_edit_submit');

      document.querySelectorAll('.js-open-edit-modal').forEach(function (button) {
        button.addEventListener('click', function () {
          if (!editModal) {
            return;
          }

          repIdInput.value = this.dataset.repId || '';
          repNameInput.value = this.dataset.name || '';
          repPhoneInput.value = this.dataset.phone || '';
          repCommitteeSelect.value = this.dataset.committeeId || '';
          repError.classList.add('d-none');
          repError.textContent = '';

          editModal.show();
        });
      });

      var updateUrlTemplate = @json(route('dashboard.rep.change', ['id' => '__ID__']));

      if (submitEditButton) {
        submitEditButton.addEventListener('click', function () {
          var repId = repIdInput.value;
          if (!repId) {
            return;
          }

          var payload = {
            name: repNameInput.value,
            phone: repPhoneInput.value,
            committee_id: repCommitteeSelect.value || null,
          };

          submitEditButton.disabled = true;

          axios.post(updateUrlTemplate.replace('__ID__', repId), payload)
            .then(function (response) {
              if (window.toastr) {
                toastr.success(response?.data?.message || 'تم حفظ التعديل بنجاح');
              }
              window.location.reload();
            })
            .catch(function (error) {
              var message = error?.response?.data?.message || 'تعذر حفظ التعديل، يرجى المحاولة مرة أخرى.';
              repError.textContent = message;
              repError.classList.remove('d-none');
            })
            .finally(function () {
              submitEditButton.disabled = false;
            });
        });
      }
    });
  </script>
@endpush
