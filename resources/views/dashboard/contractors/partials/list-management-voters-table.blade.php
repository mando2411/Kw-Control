<div class="d-md-none mb-2 d-flex align-items-center gap-2">
    <button type="button" class="btn btn-sm btn-outline-secondary js-lm-voters-mobile-toggle" data-expanded="0">
        إظهار تفاصيل أكثر
    </button>
    <input
        type="search"
        class="form-control form-control-sm js-lm-voters-search-mobile"
        placeholder="بحث..."
        aria-label="بحث داخل المضامين"
    >
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle text-center mb-0 list-management-voters-table">
        <thead class="table-light">
            <tr>
                <th class="lm-col-extra">#</th>
                <th>الناخب</th>
                <th class="lm-col-extra">الرقم المدني</th>
                <th class="lm-col-extra">العائلة</th>
                <th class="lm-col-extra">اللجنة</th>
                <th>المرشح</th>
                <th>المتعهد</th>
                <th class="lm-col-extra">تاريخ الإضافة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $index => $row)
                <tr>
                    <td class="lm-col-extra">{{ $index + 1 }}</td>
                    <td>
                        <button type="button" class="btn btn-link p-0 js-list-management-voter-open" data-voter-id="{{ (int) ($row->voter_id ?? 0) }}" style="text-decoration:none;">
                            {{ $row->voter_name ?: '—' }}
                        </button>
                        @if(!empty($row->is_duplicate))
                            <span class="badge bg-danger ms-1">مكرر @if(!empty($row->duplicate_count)) ({{ $row->duplicate_count }}) @endif</span>
                        @endif
                    </td>
                    <td class="lm-col-extra">{{ $row->civil_id ?: '—' }}</td>
                    <td class="lm-col-extra">{{ $row->family_name ?: '—' }}</td>
                    <td class="lm-col-extra">{{ $row->committee_name ?: '—' }}</td>
                    <td>{{ $row->candidate_name ?: '—' }}</td>
                    <td>{{ $row->contractor_name ?: '—' }}</td>
                    <td class="lm-col-extra">{{ $row->attached_at ? \Carbon\Carbon::parse($row->attached_at)->format('Y/m/d H:i') : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">لا توجد مضامين مطابقة للسيليكشن الحالي.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
