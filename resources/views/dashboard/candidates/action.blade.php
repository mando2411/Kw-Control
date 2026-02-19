<a href="{{ route('dashboard.candidates.edit', $id) }}">
    <i class="fa fa-edit"></i>
</a>

<a data-delete-url="{{ route('dashboard.candidates.destroy', $id) }}" href="javascript:;"
   type="button" class="btn-delete-resource-modal" data-bs-toggle="modal" data-bs-target="#deleteResourceModal">
    <i class="fa fa-trash"></i>
</a>

@php
    static $resolvedCurrentListLeader = false;
    static $currentListLeaderId = null;

    if (!$resolvedCurrentListLeader && auth()->check()) {
        $currentListLeaderId = \App\Models\Candidate::withoutGlobalScopes()
            ->where('user_id', (int) auth()->id())
            ->where('candidate_type', 'list_leader')
            ->value('id');

        $resolvedCurrentListLeader = true;
    }

    $candidateIsMemberOfCurrentLeader = $currentListLeaderId
        && (int) ($list_leader_candidate_id ?? 0) === (int) $currentListLeaderId
        && (int) $id !== (int) $currentListLeaderId;

    $isStoppedCandidate = (bool) ($is_stopped ?? false);
@endphp

@if($candidateIsMemberOfCurrentLeader)
    <form method="POST" action="{{ route('dashboard.candidates.toggle-status', $id) }}" style="display:inline-block; margin-right: 4px;">
        @csrf
        <button type="submit" class="btn btn-sm {{ $isStoppedCandidate ? 'btn-success' : 'btn-warning' }}" title="{{ $isStoppedCandidate ? 'تفعيل' : 'إيقاف' }}">
            <i class="fa {{ $isStoppedCandidate ? 'fa-check' : 'fa-ban' }}"></i>
        </button>
    </form>
@endif
