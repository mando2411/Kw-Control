@extends('layouts.dashboard.app')

@section('content')
<style>
  .sorting-page {
    --sp-ink: #102a43;
    --sp-muted: #486581;
    --sp-soft: #f5f8fc;
    --sp-primary: #0f766e;
    --sp-primary-dark: #0b5f58;
    --sp-accent: #f59e0b;
    --sp-danger: #d64545;
    --sp-success: #1f9d66;
    --sp-border: #d9e2ec;
    --sp-card-shadow: 0 12px 35px rgba(15, 118, 110, 0.14);
    --sp-font-main: "Cairo", "Tajawal", sans-serif;

    position: relative;
    padding: 1.2rem 0 2rem;
    font-family: var(--sp-font-main);
    color: var(--sp-ink);
  }

  .sorting-page::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: 0;
    background:
      radial-gradient(circle at 88% 10%, rgba(245, 158, 11, 0.14), transparent 42%),
      radial-gradient(circle at 12% 16%, rgba(15, 118, 110, 0.13), transparent 40%),
      linear-gradient(180deg, #f9fbff 0%, #eef5f8 100%);
  }

  .sorting-shell {
    position: relative;
    z-index: 2;
  }

  .sorting-card {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--sp-border);
    border-radius: 20px;
    box-shadow: var(--sp-card-shadow);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
  }

  .sorting-hero {
    padding: 1.15rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    animation: spSlideDown 0.42s ease;
  }

  .sorting-title {
    margin: 0;
    font-size: clamp(1.1rem, 2.4vw, 1.55rem);
    font-weight: 800;
    color: var(--sp-ink);
    line-height: 1.65;
  }

  .sorting-subtitle {
    margin: 0.35rem 0 0;
    color: var(--sp-muted);
    font-size: 0.93rem;
  }

  .sorting-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.5rem 0.85rem;
    border-radius: 999px;
    background: rgba(15, 118, 110, 0.1);
    color: var(--sp-primary-dark);
    font-weight: 700;
    font-size: 0.83rem;
    white-space: nowrap;
  }

  .sorting-toolbar {
    margin-top: 1rem;
    padding: 1rem;
    display: grid;
    grid-template-columns: minmax(260px, 1fr) minmax(240px, 1fr);
    gap: 0.9rem;
    align-items: end;
    animation: spFadeUp 0.5s ease;
  }

  .sorting-field {
    display: flex;
    flex-direction: column;
    gap: 0.42rem;
  }

  .sorting-label {
    margin: 0;
    font-size: 0.82rem;
    color: var(--sp-muted);
    font-weight: 700;
  }

  .sorting-input,
  .sorting-select {
    height: 46px;
    border-radius: 12px;
    border: 1px solid #bfccd9;
    background: #fff;
    color: var(--sp-ink);
    font-weight: 700;
    font-size: 0.95rem;
    padding: 0.45rem 0.85rem;
    transition: all 0.2s ease;
  }

  .sorting-input:focus,
  .sorting-select:focus {
    border-color: rgba(15, 118, 110, 0.7);
    box-shadow: 0 0 0 0.2rem rgba(15, 118, 110, 0.18);
    outline: none;
  }

  .search-wrap {
    position: relative;
  }

  .search-wrap i {
    position: absolute;
    top: 50%;
    left: 0.85rem;
    transform: translateY(-50%);
    color: #7b8794;
    pointer-events: none;
  }

  .search-wrap .sorting-input {
    padding-left: 2.2rem;
  }

  .committee-stats {
    margin-top: 1rem;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.8rem;
  }

  .stat-item {
    border-radius: 14px;
    border: 1px solid #d7e6e9;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    padding: 0.8rem;
    text-align: center;
    transition: transform 0.24s ease, box-shadow 0.24s ease;
    animation: spFadeUp 0.45s ease;
  }

  .stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(16, 42, 67, 0.1);
  }

  .stat-title {
    margin: 0;
    color: var(--sp-muted);
    font-size: 0.78rem;
    font-weight: 700;
  }

  .stat-value {
    margin: 0.35rem 0 0;
    color: var(--sp-ink);
    font-weight: 800;
    font-size: 1rem;
  }

  .sorting-control-row {
    margin-top: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.8rem;
    flex-wrap: wrap;
  }

  .sorting-status {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    border-radius: 12px;
    background: #f6f9fc;
    border: 1px solid #d7e2ee;
    color: #334e68;
    padding: 0.55rem 0.85rem;
    font-size: 0.85rem;
    font-weight: 700;
  }

  .sorting-status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--sp-success);
    box-shadow: 0 0 0 0 rgba(31, 157, 102, 0.4);
    animation: spPulse 1.6s infinite;
  }

  .btn-lock-control {
    border: 0;
    border-radius: 13px;
    background: linear-gradient(135deg, var(--sp-primary) 0%, #159e90 100%);
    color: #fff;
    min-width: 190px;
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-weight: 800;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .btn-lock-control:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 18px rgba(15, 118, 110, 0.32);
  }

  .candidates-card {
    margin-top: 1rem;
    overflow: hidden;
    animation: spFadeUp 0.55s ease;
  }

  .candidates-head {
    padding: 0.9rem 1rem;
    border-bottom: 1px solid #d7e2ee;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.65rem;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.95), #f8fcff);
  }

  .candidates-head h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 800;
    color: var(--sp-ink);
  }

  .table-wrap {
    padding: 0 0.6rem 0.7rem;
  }

  #candidates_table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0 0.56rem;
  }

  #candidates_table thead th {
    border: 0;
    color: #486581;
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
    background: transparent;
    letter-spacing: 0.03em;
    padding: 0.45rem;
  }

  #candidates_table tbody tr {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 8px 16px rgba(16, 42, 67, 0.08);
    transition: transform 0.22s ease, box-shadow 0.22s ease;
    animation: spFadeUp 0.35s ease;
    animation-delay: calc(var(--row-index, 0) * 40ms);
  }

  #candidates_table tbody tr:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 22px rgba(16, 42, 67, 0.12);
  }

  #candidates_table tbody td {
    border: 0;
    vertical-align: middle;
    padding: 0.72rem 0.45rem;
    font-size: 0.92rem;
    font-weight: 700;
    color: #243b53;
  }

  .candidate-name {
    color: #102a43;
    font-weight: 800;
  }

  .vote-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 58px;
    border-radius: 999px;
    padding: 0.28rem 0.7rem;
    background: rgba(15, 118, 110, 0.12);
    color: var(--sp-primary-dark);
    font-weight: 800;
  }

  .vote-pill.updated {
    animation: spPulseQuick 0.42s ease;
  }

  .action-btn {
    border: 0;
    border-radius: 11px;
    width: 100%;
    min-height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    color: #fff;
    font-size: 0.82rem;
    font-weight: 800;
    transition: transform 0.16s ease, filter 0.16s ease;
  }

  .action-btn:hover {
    transform: translateY(-1px);
    filter: brightness(0.96);
  }

  .action-plus {
    background: linear-gradient(135deg, #1f9d66 0%, #26b26f 100%);
  }

  .action-minus {
    background: linear-gradient(135deg, #d64545 0%, #e35d5d 100%);
  }

  .action-set {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e64f0 100%);
  }

  .empty-state {
    margin-top: 1rem;
    padding: 1.35rem;
    text-align: center;
    color: #334e68;
    animation: spFadeUp 0.45s ease;
  }

  .empty-state i {
    font-size: 2rem;
    color: #829ab1;
    margin-bottom: 0.55rem;
  }

  #lock-overlay {
    position: fixed;
    inset: 0;
    background: rgba(9, 30, 66, 0.78);
    display: none;
    z-index: 1035;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
  }

  #lock-overlay .lock-icon,
  #lock-overlay .unlock-icon {
    font-size: 92px;
    color: #fff;
  }

  #lock-overlay .lock-icon {
    animation: lock-animation 1.35s ease-in-out infinite;
  }

  #lock-overlay .unlock-icon {
    display: none;
  }

  @media (max-width: 991.98px) {
    .sorting-toolbar {
      grid-template-columns: 1fr;
    }

    .committee-stats {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 767.98px) {
    .sorting-page {
      padding-top: 0.7rem;
    }

    .sorting-hero {
      flex-direction: column;
      align-items: flex-start;
    }

    .sorting-chip {
      font-size: 0.78rem;
    }

    .committee-stats {
      grid-template-columns: 1fr;
      gap: 0.6rem;
    }

    #candidates_table thead {
      display: none;
    }

    #candidates_table,
    #candidates_table tbody,
    #candidates_table tr,
    #candidates_table td {
      display: block;
      width: 100%;
    }

    #candidates_table tbody tr {
      margin-bottom: 0.7rem;
      padding: 0.55rem 0.6rem;
    }

    #candidates_table tbody td {
      position: relative;
      text-align: left;
      padding: 0.5rem 0.5rem 0.5rem 6.35rem;
      min-height: 40px;
    }

    #candidates_table tbody td::before {
      content: attr(data-label);
      position: absolute;
      right: 0.5rem;
      top: 50%;
      transform: translateY(-50%);
      font-size: 0.73rem;
      color: #627d98;
      font-weight: 800;
    }

    .action-btn {
      justify-content: flex-start;
      padding-right: 0.7rem;
    }

    .sorting-control-row {
      flex-direction: column;
      align-items: stretch;
    }

    .btn-lock-control {
      width: 100%;
    }
  }

  @keyframes spSlideDown {
    from {
      transform: translateY(-8px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  @keyframes spFadeUp {
    from {
      transform: translateY(10px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  @keyframes spPulse {
    0% {
      box-shadow: 0 0 0 0 rgba(31, 157, 102, 0.35);
    }
    70% {
      box-shadow: 0 0 0 8px rgba(31, 157, 102, 0);
    }
    100% {
      box-shadow: 0 0 0 0 rgba(31, 157, 102, 0);
    }
  }

  @keyframes spPulseQuick {
    0% {
      transform: scale(1);
    }
    35% {
      transform: scale(1.08);
    }
    100% {
      transform: scale(1);
    }
  }

  @keyframes lock-animation {
    0%,
    100% {
      transform: scale(1);
      opacity: 1;
    }

    50% {
      transform: scale(1.14);
      opacity: 0.76;
    }
  }

  @keyframes unlock-animation {
    0% {
      transform: scale(1);
      opacity: 1;
    }

    100% {
      transform: scale(0.82) rotate(90deg);
      opacity: 0;
    }
  }
</style>

<div class="sorting-page rtl" dir="rtl">
  <div id="lock-overlay">
    <i class="fas fa-lock lock-icon"></i>
    <i class="fas fa-lock-open unlock-icon"></i>
  </div>

  <div class="container-fluid sorting-shell">
    <div class="sorting-card sorting-hero">
      <div>
        <h1 class="sorting-title">لوحة فرز الأصوات</h1>
        <p class="sorting-subtitle">واجهة فرز سريعة ومنظمة لتحديث أصوات المرشحين داخل كل لجنة.</p>
      </div>
      <div class="sorting-chip">
        <i class="fa-solid fa-chart-column"></i>
        <span>تحديث مباشر للنتائج</span>
      </div>
    </div>

    <div class="sorting-card sorting-toolbar">
      @if (auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('مرشح'))
        <form action="{{ route('dashboard.sorting') }}" method="get" id="sorting-form" class="sorting-field">
          @csrf
          <label class="sorting-label" for="sorting-select">اختيار اللجنة</label>
          <select id="sorting-select" name="committee" class="sorting-select">
            <option value="" hidden>اختر اللجنة</option>
            @foreach ($committees as $i => $com)
              <option
                value="{{ $com->id }}"
                @if ($committee && $committee->id == $com->id) selected @endif>
                {{ $i + 1 }} - {{ $com->name }} ({{ $com->type }})
              </option>
            @endforeach
          </select>
        </form>
      @endif

      <div class="sorting-field">
        <label class="sorting-label" for="searchBox">بحث باسم المرشح</label>
        <div class="search-wrap">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" placeholder="اكتب اسم المرشح" class="sorting-input" name="candidateName" id="searchBox" value="">
        </div>
      </div>
    </div>

    @if ($candidates)
      <div class="committee-stats">
        <div class="stat-item">
          <p class="stat-title">اسم اللجنة</p>
          <p class="stat-value">{{ $committee->name }}</p>
        </div>
        <div class="stat-item">
          <p class="stat-title">نوع اللجنة</p>
          <p class="stat-value">{{ $committee->type }}</p>
        </div>
        <div class="stat-item">
          <p class="stat-title">إجمالي الحضور</p>
          <p class="stat-value"><span class="totalAttending">{{ $committee->voters()->count() }}</span></p>
        </div>
        <div class="stat-item">
          <p class="stat-title">مجموع الأصوات المفروزة</p>
          <p class="stat-value"><span class="totalSortingSound">{{ $committee->candidates->sum('pivot.votes') }}</span></p>
        </div>
      </div>

      <div class="sorting-control-row">
        <div class="sorting-status">
          <span class="sorting-status-dot"></span>
          <span id="sortingStatusText">حالة الفرز: نشط</span>
        </div>

        <form action="{{ route('committee.status', $committee->id) }}" method="POST" id="CommStatus">
          @csrf
          <input type="hidden" name="status" id="status" value="{{ $committee->status }}">
          <button id="toggle-lock-button" type="submit" class="btn-lock-control">
            <i id="icon" class="fa-solid fa-unlock"></i>
            <span id="lockButtonText">تبديل حالة الفرز</span>
          </button>
        </form>
      </div>

      <div class="sorting-card candidates-card">
        <div class="candidates-head">
          <h4>قائمة المرشحين داخل اللجنة</h4>
          <span class="sorting-chip">عدد المرشحين: {{ count($candidates) }}</span>
        </div>

        <div class="table-wrap table-responsive">
          <table class="table rtl" id="candidates_table">
            <thead class="text-center">
              <tr>
                <th>الترتيب</th>
                <th>إضافة</th>
                <th>المرشح</th>
                <th>إزالة</th>
                <th>الأصوات</th>
                <th>تحديد</th>
              </tr>
            </thead>

            <tbody class="text-center">
              @foreach ($candidates as $index => $can)
                <tr style="--row-index: {{ $index }};">
                  <td data-label="الترتيب">{{ $index + 1 }}</td>
                  <td data-label="إضافة">
                    <button class="action-btn action-plus plusBtn sortBtn" data-message="{{ 'تأكيد إضافة صوت جديد للمرشح (' . $can['name'] . ')' }}">
                      <i class="fa-solid fa-plus"></i>
                      <span>إضافة</span>
                    </button>
                  </td>
                  <td class="candidate-name fullName" data-label="المرشح">{{ $can['name'] }}</td>
                  <td data-label="إزالة">
                    <button class="action-btn action-minus minusBtn sortBtn" data-message="{{ 'تأكيد حذف صوت من المرشح (' . $can['name'] . ')' }}">
                      <i class="fa-solid fa-minus"></i>
                      <span>إزالة</span>
                    </button>
                  </td>
                  <td data-label="الأصوات">
                    <span id="vote_count_{{ $can['id'] }}" class="vote-pill">{{ $can['votes'] }}</span>
                  </td>
                  <td data-label="تحديد">
                    <button class="action-btn action-set setBtn sortBtn" data-message="{{ 'تأكيد تحديد عدد الأصوات للمرشح (' . $can['name'] . ')' }}">
                      <i class="fa-solid fa-pen-to-square"></i>
                      <span>تحديد</span>
                    </button>
                    <input type="hidden" class="row-committee" value="{{ $can['committee'] }}">
                    <input type="hidden" class="row-candidate-id" value="{{ $can['id'] }}">
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      @include('dashboard.sorting.model_pop_up')
    @else
      <div class="sorting-card empty-state">
        <i class="fa-regular fa-folder-open"></i>
        <h5 class="mb-2">لا توجد بيانات فرز حاليا</h5>
        <p class="mb-0">اختر اللجنة أولا لعرض المرشحين وإدارة الأصوات بشكل مباشر.</p>
      </div>
    @endif
  </div>
</div>
@endsection

@push('js')
<script>
  $(document).ready(function() {
    if ($('#sorting-select').length) {
      $('#sorting-select').on('change', function() {
        $('#sorting-form').submit();
      });
    }

    checkLocked();

    $('.sortBtn').on('click', function(event) {
      event.preventDefault();

      var $row = $(this).closest('tr');
      var message = $(this).data('message');
      var id = $row.find('.row-candidate-id').val();
      var committee = $row.find('.row-committee').val();
      var count_status = 'set';

      if ($(this).hasClass('plusBtn')) {
        count_status = 'increment';
      } else if ($(this).hasClass('minusBtn')) {
        count_status = 'decrement';
      }

      var currentVotes = parseInt($('#vote_count_' + id).text(), 10) || 0;
      var defaultVotes = count_status === 'set' ? currentVotes : 1;

      $('#confirmMessage').text(message);
      $('#candidateIdInput').val(id);
      $('#statusInput').val(count_status);
      $('#vote_count').val(defaultVotes);
      $('#confirmCommitteeInput').val(committee);
      $('#confirmModal').modal('show');
    });

    $('#confirmButton').click(function() {
      var candidate_id = $('#candidateIdInput').val();
      var count_status = $('#statusInput').val();
      var vote_count = $('#vote_count').val();
      var committee = $('#confirmCommitteeInput').val();
      var candidate_vote = parseInt($('#vote_count_' + candidate_id).text(), 10) || 0;

      $('#confirmModal').modal('hide');

      if (vote_count === '' || isNaN(parseInt(vote_count, 10)) || parseInt(vote_count, 10) < 0) {
        errorMessageInModel('يرجى إدخال عدد أصوات صحيح.');
        return;
      }

      if ((count_status === 'decrement') && (parseInt(vote_count, 10) > candidate_vote)) {
        errorMessageInModel('لا يمكن حذف عدد أصوات أكبر من العدد الموجود.');
        return;
      }

      setVote(candidate_id, count_status, vote_count, committee);
    });

    function setVote(candidate_id, count_status, vote_count, committee) {
      if (!candidate_id) {
        return;
      }

      $.ajax({
        url: '/candidates/setVotes',
        type: 'POST',
        data: JSON.stringify({
          _token: $('meta[name="csrf-token"]').attr('content'),
          candidate_id: candidate_id,
          count_status: count_status,
          vote_count: vote_count,
          committee: committee,
        }),
        contentType: 'application/json',
        success: function(data) {
          if ((data.vote_count) || (data.vote_count === 0)) {
            var $votePill = $('#vote_count_' + candidate_id);
            $votePill.text(data.vote_count);
            $votePill.addClass('updated');
            setTimeout(function() {
              $votePill.removeClass('updated');
            }, 420);

            refreshTotalSortingVotes();
          }
          sucessMessageInModel(data.message);
        },
        error: function() {
          errorMessageInModel('حدث خطأ أثناء تحديث الأصوات.');
        }
      });
    }

    function refreshTotalSortingVotes() {
      var total = 0;
      $('.vote-pill').each(function() {
        total += parseInt($(this).text(), 10) || 0;
      });
      $('.totalSortingSound').text(total);
    }

    function errorMessageInModel(msg) {
      $('#successMessage').text(msg);
      $('#successModal .modal-header').removeClass('bg-success').addClass('bg-danger');
      $('#successModal .fa-check-circle').removeClass('text-success').addClass('text-danger');
      $('#successModal').modal('show');
    }

    function sucessMessageInModel(msg) {
      $('#successMessage').text(msg);
      $('#successModal .modal-header').removeClass('bg-danger').addClass('bg-success');
      $('#successModal .fa-check-circle').removeClass('text-danger').addClass('text-success');
      $('#successModal').modal('show');
    }

    $('#CommStatus').on('submit', function(e) {
      e.preventDefault();
      axios.post($(this).attr('action'), $(this).serialize()).then((res) => {
        $('#status').val(res.data.status);
        setTimeout(() => {
          checkLocked();
        }, 800);
      }).catch(error => {
        toastr.error(error.response.data.error ?? '{{ __('main.unexpected - error ') }}');
      });
    });

    let timer;
    $('#searchBox').on('keyup', function() {
      clearTimeout(timer);
      timer = setTimeout(function() {
        searchTable();
      }, 300);
    });

    function searchTable() {
      let entireValue = $('#searchBox').val().toLowerCase();
      $('#candidates_table tbody tr').each(function() {
        let rowText = $(this).text().toLowerCase();
        let matchEntire = entireValue === '';
        if (entireValue !== '') {
          matchEntire = rowText.indexOf(entireValue) > -1;
        }
        $(this).toggle(matchEntire);
      });
    }
  });

  const toggleButton = document.getElementById("toggle-lock-button");
  const lockOverlay = document.getElementById("lock-overlay");
  const lockIcon = lockOverlay.querySelector(".lock-icon");
  const unlockIcon = lockOverlay.querySelector(".unlock-icon");
  const statusText = document.getElementById('sortingStatusText');
  const lockButtonText = document.getElementById('lockButtonText');
  let icon = $('#icon');
  let isLocked = false;

  function checkLocked() {
    const statusElement = $('#status');
    if (!statusElement.length) {
      return;
    }

    let status = statusElement.val();

    if (status == 1) {
      unlockPage();
    } else if (status == 0) {
      lockPage();
    }
  }

  function updateLockLabels(locked) {
    if (!statusText || !lockButtonText) {
      return;
    }

    statusText.textContent = locked ? 'حالة الفرز: مقفل' : 'حالة الفرز: نشط';
    lockButtonText.textContent = locked ? 'فتح الفرز' : 'إيقاف الفرز';
  }

  function lockPage() {
    isLocked = true;

    icon.addClass("fa-unlock");
    icon.removeClass("fa-lock");

    lockOverlay.style.display = "flex";
    lockIcon.style.display = "block";
    lockIcon.style.animation = "lock-animation 1.35s ease-in-out infinite";
    unlockIcon.style.display = "none";
    document.body.style.overflow = 'hidden';
    $('#status').val(1);
    updateLockLabels(true);
  }

  function unlockPage() {
    isLocked = false;
    icon.addClass("fa-lock");
    icon.removeClass("fa-unlock");
    $('#status').val(0);

    lockIcon.style.animation = "none";
    lockIcon.style.display = "none";
    unlockIcon.style.display = "block";
    unlockIcon.style.animation = "unlock-animation 0.8s ease-in-out";
    document.body.style.overflow = '';
    updateLockLabels(false);

    setTimeout(() => {
      lockOverlay.style.display = "none";
      unlockIcon.style.animation = "none";
    }, 800);
  }
</script>
@endpush