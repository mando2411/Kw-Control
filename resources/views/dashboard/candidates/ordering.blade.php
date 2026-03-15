@extends('layouts.dashboard.app')

@section('title', 'ترتيب المرشحين')

@section('content')
    <div class="container-fluid rtl" dir="rtl">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3">ترتيب المرشحين</h4>

                <form method="GET" action="{{ route('dashboard.candidates.ordering') }}" class="row g-2 align-items-end mb-3">
                    <div class="col-md-6 col-lg-4">
                        <label for="election_id" class="form-label">اختيار الحملة</label>
                        <select name="election_id" id="election_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- اختر الحملة --</option>
                            @foreach ($elections as $election)
                                <option value="{{ $election->id }}" @selected((int) $selectedElectionId === (int) $election->id)>
                                    {{ $election->name ?? ('حملة #' . $election->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                @if (session('success'))
                    <div class="alert alert-success py-2">{{ session('success') }}</div>
                @endif

                @if ($selectedElectionId > 0)
                    <form method="POST" action="{{ route('dashboard.candidates.ordering.update') }}">
                        @csrf
                        <input type="hidden" name="election_id" value="{{ $selectedElectionId }}">

                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">#</th>
                                        <th>المرشح / القائمة</th>
                                        <th style="width: 180px;">النوع</th>
                                        <th style="width: 180px;">رقم الترتيب</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($candidates as $index => $candidate)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $candidate['name'] }}</td>
                                            <td>{{ $candidate['type_label'] }}</td>
                                            <td>
                                                <input type="number" min="1" class="form-control"
                                                    name="orders[{{ $candidate['id'] }}]"
                                                    value="{{ $candidate['sorting_order'] ?? '' }}"
                                                    placeholder="مثال: 1">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">لا يوجد مرشحون داخل هذه الحملة.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($candidates->isNotEmpty())
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">حفظ الترتيب</button>
                            </div>
                        @endif
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
