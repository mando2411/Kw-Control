<div class="table-responsive">
    <table class="table table-striped table-hover align-middle text-center mb-0">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>الناخب</th>
                <th>الرقم المدني</th>
                <th>العائلة</th>
                <th>اللجنة</th>
                <th>المرشح</th>
                <th>المتعهد</th>
                <th>تاريخ الإضافة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->voter_name ?: '—' }}</td>
                    <td>{{ $row->civil_id ?: '—' }}</td>
                    <td>{{ $row->family_name ?: '—' }}</td>
                    <td>{{ $row->committee_name ?: '—' }}</td>
                    <td>{{ $row->candidate_name ?: '—' }}</td>
                    <td>{{ $row->contractor_name ?: '—' }}</td>
                    <td>{{ $row->attached_at ? \Carbon\Carbon::parse($row->attached_at)->format('Y/m/d H:i') : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">لا توجد مضامين مطابقة للسيليكشن الحالي.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
