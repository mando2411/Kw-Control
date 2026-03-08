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
            <h5 class="mb-0 rep-home-title">توزيع المندوبين</h5>
            @if(admin()->can('representatives.create'))
              <button type="button" id="openQuickAddRepresentativeForm" class="btn btn-primary rep-home-open-btn" data-bs-toggle="collapse" data-bs-target="#quickAddRepresentativeForm" aria-expanded="false" aria-controls="quickAddRepresentativeForm">
                إضافة مندوب جديد
              </button>
            @endif
          </div>

          <form action="{{ route('dashboard.rep-home') }}" method="get" id="school-form" class="row g-2 align-items-end mt-2 rep-home-filter-form">
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

                  @if(($relations['is_list_leader_user'] ?? false) && ($relations['list_candidates'] ?? collect())->isNotEmpty())
                    <div class="col-12">
                      <div class="candidate-selector-panel">
                        <div class="candidate-selector-head">
                          <label class="form-label mb-0" for="quick_candidate_ids">المرشحون المسموحون للمندوب</label>
                          <span class="candidate-selector-count">المحدد: <strong id="quick_candidate_count">0</strong></span>
                        </div>

                        <select name="candidate_ids[]" id="quick_candidate_ids" class="form-select candidate-selector-input" multiple>
                          @foreach ($relations['list_candidates'] as $candidateOption)
                            <option value="{{ $candidateOption->id }}" @selected(in_array((string) $candidateOption->id, array_map('strval', (array) old('candidate_ids', [])), true))>
                              {{ $candidateOption->user?->name ?? ('مرشح #' . $candidateOption->id) }}
                            </option>
                          @endforeach
                        </select>

                        <div class="candidate-selector-actions">
                          <button type="button" class="btn btn-sm btn-outline-primary" id="quick_candidate_select_all">تحديد الكل</button>
                          <button type="button" class="btn btn-sm btn-outline-secondary" id="quick_candidate_clear_all">إلغاء الكل</button>
                        </div>

                        <small class="text-muted d-block mt-1">المرشح الإداري (غير فعلي) لا يظهر في هذه القائمة. يمكنك اختيار أكثر من مرشح، وسيظهر هؤلاء فقط للمندوب في شاشة الفرز.</small>
                      </div>
                    </div>
                  @endif

                  <div class="col-12 d-flex gap-2">
                    <button class="btn btn-success rep-home-save-btn" type="submit">حفظ</button>
                    <button class="btn btn-outline-secondary rep-home-close-btn" id="quickAddCloseButton" type="button">إغلاق</button>
                  </div>
                </form>
              </div>
            </div>
          @endif
        </div>

        @php
          $representativeRows = collect();
          foreach ($schools as $school) {
              foreach ($school->committees as $com) {
                  foreach ($com->users() as $representative) {
                      $representativeRows->push([
                          'id' => $representative['id'],
                          'name' => $representative['name'],
                          'phone' => $representative['phone'],
                          'committee_id' => $representative['committee_id'] ?? $com->id,
                          'committee_name' => $com->name,
                          'committee_type' => $com->type,
                          'school_name' => $school->name,
                      ]);
                  }
              }
          }

          $representativeRows = $representativeRows
              ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
              ->values();
        @endphp

        <div class="card-body">
          <div class="rep-home-table-toolbar mb-3">
            <div class="rep-home-table-summary">
              إجمالي المندوبين المعروضين: <strong>{{ $representativeRows->count() }}</strong>
            </div>
            <div class="rep-home-table-search-wrap">
              <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" id="repHomeTableSearch" class="form-control" placeholder="ابحث باسم المندوب أو اللجنة أو المدرسة أو الهاتف">
              </div>
            </div>
          </div>

          <div class="table-responsive rep-home-table-wrap">
            <table class="table align-middle text-center rep-home-table" id="repHomeRepresentativesTable">
              <thead>
                <tr>
                  <th>المندوب</th>
                  <th>اللجنة</th>
                  <th>المدرسة</th>
                  <th>الهاتف</th>
                  <th>الإجراءات</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($representativeRows as $representative)
                  <tr class="rep-home-table-row">
                    <td data-label="المندوب" class="fw-semibold">{{ $representative['name'] }}</td>
                    <td data-label="اللجنة">{{ $representative['committee_name'] }} ({{ $representative['committee_type'] }})</td>
                    <td data-label="المدرسة">{{ $representative['school_name'] }}</td>
                    <td data-label="الهاتف" dir="ltr">{{ $representative['phone'] }}</td>
                    <td data-label="الإجراءات">
                      <button
                        type="button"
                        class="btn btn-sm btn-outline-primary js-open-edit-modal rep-home-edit-btn"
                        data-rep-id="{{ $representative['id'] }}"
                        data-name="{{ $representative['name'] }}"
                        data-phone="{{ $representative['phone'] }}"
                        data-committee-id="{{ $representative['committee_id'] }}"
                      >
                        تعديل
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-muted py-4">لا يوجد مندوبون مطابقون للمدرسة المختارة.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
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
    .rep-home-page {
      --rh-font-main: "Cairo", "Tajawal", sans-serif;
      --rh-text: #133758;
      --rh-muted: #627995;
      --rh-primary: #1569c9;
      --rh-primary-dark: #0f56a5;
      --rh-soft-bg: #f4f8ff;
      --rh-border: #d8e5f7;
      --rh-shadow: 0 26px 52px rgba(12, 36, 64, 0.15);

      font-family: var(--rh-font-main);
      font-size: 16px;
      color: var(--rh-text);
    }

    .rep-home-page .form-label,
    .rep-home-page .form-select,
    .rep-home-page .form-control,
    .rep-home-page .btn,
    .rep-home-page table {
      font-size: 1rem;
    }

    .rep-home-shell-card {
      border: 0;
      border-radius: 22px;
      overflow: hidden;
      box-shadow: var(--rh-shadow);
      background: #fff;
    }

    .rep-home-header {
      border-bottom: 1px solid var(--rh-border);
      background: linear-gradient(160deg, #f7fbff 0%, #ffffff 100%);
      padding: 1.25rem;
    }

    .rep-home-title {
      font-size: clamp(1.3rem, 2.2vw, 1.62rem);
      color: #143e65;
      font-weight: 800;
      letter-spacing: 0.01em;
    }

    .rep-home-open-btn {
      border-radius: 12px;
      min-height: 46px;
      font-weight: 800;
      font-size: 1rem;
      background: linear-gradient(135deg, var(--rh-primary) 0%, #1c7ce1 100%);
      border: 0;
      box-shadow: 0 12px 26px rgba(21, 105, 201, 0.24);
    }

    .rep-home-open-btn:hover {
      background: linear-gradient(135deg, var(--rh-primary-dark) 0%, #176ec8 100%);
    }

    .rep-home-filter-form .form-label {
      font-size: 0.96rem;
      font-weight: 700;
      color: #2a517a;
    }

    .rep-home-filter-form .form-select {
      min-height: 47px;
      border-radius: 12px;
      border-color: #c9d9ee;
      font-weight: 700;
      color: #1d436c;
    }

    .rep-home-quick-add {
      border: 1px solid #d4e1f6;
      border-radius: 14px;
      background: linear-gradient(180deg, #fcfdff 0%, #f4f8ff 100%);
    }

    .rep-home-quick-add .form-label {
      font-weight: 700;
      color: #2a5079;
      font-size: 0.95rem;
    }

    .rep-home-quick-add .form-control,
    .rep-home-quick-add .form-select {
      min-height: 46px;
      border-radius: 12px;
      border-color: #c8d8ee;
      font-weight: 700;
      color: #1b4269;
    }

    .rep-home-save-btn,
    .rep-home-close-btn {
      min-height: 45px;
      border-radius: 12px;
      min-width: 120px;
      font-weight: 800;
      font-size: 0.98rem;
    }

    .rep-home-table-toolbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.75rem;
      flex-wrap: wrap;
    }

    .rep-home-table-summary {
      font-weight: 800;
      color: #1b4a77;
      background: #edf4ff;
      padding: 0.58rem 0.9rem;
      border-radius: 11px;
      font-size: 0.99rem;
    }

    .rep-home-table-search-wrap {
      width: min(450px, 100%);
    }

    .rep-home-table-search-wrap .input-group-text {
      border-color: #bfd4ef;
      background: #eff5ff;
      color: #45688f;
      font-size: 1rem;
      border-radius: 12px 0 0 12px;
    }

    .rep-home-table-search-wrap .form-control {
      border-color: #bfd4ef;
      min-height: 45px;
      font-size: 1rem;
      font-weight: 700;
      color: #1e466f;
      border-radius: 0 12px 12px 0;
    }

    .rep-home-table-search-wrap .form-control:focus {
      border-color: rgba(21, 105, 201, 0.55);
      box-shadow: 0 0 0 0.2rem rgba(21, 105, 201, 0.16);
    }

    .rep-home-table-wrap {
      border: 1px solid #d7e4f5;
      border-radius: 14px;
      overflow: hidden;
      background: #fff;
    }

    .rep-home-table thead th {
      background: #f2f7ff;
      color: #1f4a74;
      border-bottom: 1px solid #d8e5f7;
      font-weight: 800;
      font-size: 1.03rem;
      letter-spacing: 0.01em;
      padding-top: 0.9rem;
      padding-bottom: 0.9rem;
    }

    .rep-home-table-row td {
      background: #fff;
      border-bottom: 1px solid #eaf1fb;
      padding: 0.86rem 0.65rem;
      font-size: 1rem;
      font-weight: 600;
      color: #193f67;
    }

    .rep-home-table-row:hover td {
      background: #f6faff;
    }

    .rep-home-edit-btn {
      min-height: 38px;
      min-width: 88px;
      border-radius: 10px;
      font-size: 0.92rem;
      font-weight: 800;
      border-color: #8db4e3;
      color: #1f5a9a;
    }

    .rep-home-edit-btn:hover {
      background: #eaf3ff;
      border-color: #7ea8da;
      color: #1a4d84;
    }

    .candidate-selector-panel {
      border: 1px solid #d8e5fb;
      border-radius: 14px;
      background: linear-gradient(180deg, #ffffff 0%, #f4f9ff 100%);
      padding: 0.95rem;
    }

    .candidate-selector-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.5rem;
      margin-bottom: 0.45rem;
      flex-wrap: wrap;
    }

    .candidate-selector-count {
      background: #e8f0ff;
      color: #1a4f8f;
      border-radius: 999px;
      font-size: 0.86rem;
      font-weight: 800;
      padding: 0.32rem 0.74rem;
    }

    .candidate-selector-input {
      min-height: 150px;
      border-color: #bfd3f4;
      font-weight: 700;
      font-size: 0.96rem;
    }

    .candidate-selector-input option {
      padding: 0.45rem;
      border-bottom: 1px solid #eef3fb;
    }

    .candidate-selector-actions {
      margin-top: 0.55rem;
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }

    @media (max-width: 768px) {
      .rep-home-page {
        font-size: 15px;
      }

      .rep-home-header {
        padding: 1rem;
      }

      .rep-home-title {
        font-size: 1.2rem;
      }

      .rep-home-open-btn,
      .rep-home-save-btn,
      .rep-home-close-btn {
        width: 100%;
      }

      .rep-home-table-toolbar {
        align-items: stretch;
      }

      .rep-home-table-summary {
        text-align: center;
      }

      .rep-home-table thead {
        display: none;
      }

      .rep-home-table,
      .rep-home-table tbody,
      .rep-home-table tr,
      .rep-home-table td {
        display: block;
        width: 100%;
      }

      .rep-home-table-row {
        margin: 0.55rem;
        border: 1px solid #e3ebfa;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 9px 18px rgba(16, 45, 76, 0.08);
      }

      .rep-home-table-row td {
        position: relative;
        text-align: left;
        padding: 0.76rem 0.82rem 0.76rem 6.5rem;
        min-height: 48px;
        font-size: 0.98rem;
      }

      .rep-home-table-row td::before {
        content: attr(data-label);
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #60708a;
        font-size: 0.82rem;
        font-weight: 800;
      }

      .candidate-selector-actions {
        flex-direction: column;
      }

      .candidate-selector-actions .btn {
        width: 100%;
      }
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
      var quickAddFormElement = document.getElementById('quickAddRepresentativeForm');
      var quickAddCloseButton = document.getElementById('quickAddCloseButton');
      var quickCandidateSelect = document.getElementById('quick_candidate_ids');
      var quickCandidateCount = document.getElementById('quick_candidate_count');
      var quickCandidateSelectAll = document.getElementById('quick_candidate_select_all');
      var quickCandidateClearAll = document.getElementById('quick_candidate_clear_all');
      var repHomeTableSearch = document.getElementById('repHomeTableSearch');
      var repHomeTableRows = Array.from(document.querySelectorAll('#repHomeRepresentativesTable tbody tr.rep-home-table-row'));

      var quickAddCollapse = null;
      if (quickAddFormElement && window.bootstrap && bootstrap.Collapse) {
        if (typeof bootstrap.Collapse.getOrCreateInstance === 'function') {
          quickAddCollapse = bootstrap.Collapse.getOrCreateInstance(quickAddFormElement, { toggle: false });
        } else {
          quickAddCollapse = new bootstrap.Collapse(quickAddFormElement, { toggle: false });
        }
      }

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

      if (quickAddCloseButton && quickAddCollapse) {
        quickAddCloseButton.addEventListener('click', function (event) {
          event.preventDefault();
          event.stopPropagation();
          quickAddCollapse.hide();
        });
      }

      // Hard fallback in case Collapse JS plugin is unavailable or fails.
      if (quickAddCloseButton && !quickAddCollapse && quickAddFormElement) {
        quickAddCloseButton.addEventListener('click', function (event) {
          event.preventDefault();
          event.stopPropagation();
          quickAddFormElement.classList.remove('show');
          quickAddFormElement.style.height = null;
        });
      }

      var syncQuickCandidateCount = function () {
        if (!quickCandidateSelect || !quickCandidateCount) {
          return;
        }

        quickCandidateCount.textContent = String(quickCandidateSelect.selectedOptions.length);
      };

      if (quickCandidateSelect) {
        quickCandidateSelect.addEventListener('change', syncQuickCandidateCount);
        syncQuickCandidateCount();
      }

      if (quickCandidateSelectAll && quickCandidateSelect) {
        quickCandidateSelectAll.addEventListener('click', function () {
          Array.from(quickCandidateSelect.options).forEach(function (option) {
            option.selected = true;
          });
          syncQuickCandidateCount();
        });
      }

      if (quickCandidateClearAll && quickCandidateSelect) {
        quickCandidateClearAll.addEventListener('click', function () {
          Array.from(quickCandidateSelect.options).forEach(function (option) {
            option.selected = false;
          });
          syncQuickCandidateCount();
        });
      }

      if (repHomeTableSearch && repHomeTableRows.length) {
        repHomeTableSearch.addEventListener('input', function () {
          var keyword = (this.value || '').toLowerCase().trim();

          repHomeTableRows.forEach(function (row) {
            var rowText = (row.textContent || '').toLowerCase();
            var shouldShow = keyword === '' || rowText.indexOf(keyword) !== -1;
            row.style.display = shouldShow ? '' : 'none';
          });
        });
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
