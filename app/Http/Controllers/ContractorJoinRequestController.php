<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Contractor;
use App\Models\ContractorJoinRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ContractorJoinRequestController extends Controller
{
    public function register(string $slug): View
    {
        $candidate = $this->resolveCandidateFromSlug($slug);

        return view('public.candidates.join-register', compact('candidate', 'slug'));
    }

    public function registerStore(Request $request, string $slug): RedirectResponse
    {
        $candidate = $this->resolveCandidateFromSlug($slug);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $normalizedPhone = preg_replace('/\s+/u', '', (string) $validated['phone']);
        $syntheticEmail = 'join_' . preg_replace('/\D+/', '', $normalizedPhone) . '_' . Str::lower(Str::random(8)) . '@kw-control.local';

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $syntheticEmail,
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return $this->submitForUser($candidate, $user);
    }

    public function submit(Request $request, string $slug): RedirectResponse
    {
        $candidate = $this->resolveCandidateFromSlug($slug);

        if (!Auth::check()) {
            return redirect()->route('candidates.join.register', ['slug' => $slug]);
        }

        return $this->submitForUser($candidate, $request->user());
    }

    public function pending(Request $request, string $slug): View
    {
        $candidate = $this->resolveCandidateFromSlug($slug);

        $joinRequest = ContractorJoinRequest::query()
            ->where('candidate_id', $candidate->id)
            ->where('requester_user_id', (int) $request->user()->id)
            ->firstOrFail();

        return view('public.candidates.join-pending', compact('candidate', 'joinRequest'));
    }

    public function review(Request $request, ContractorJoinRequest $joinRequest): JsonResponse
    {
        $candidate = Candidate::withoutGlobalScopes()->with('user')->find($joinRequest->candidate_id);
        $joinRequest->load('requester');

        if (!$this->canManageJoinRequest($request->user(), $joinRequest, $candidate)) {
            abort(403);
        }

        return response()->json([
            'id' => $joinRequest->id,
            'status' => $joinRequest->status,
            'candidate_name' => $candidate?->user?->name,
            'requester_name' => $joinRequest->requester_name,
            'requester_phone' => $joinRequest->requester_phone,
            'created_at' => optional($joinRequest->created_at)->diffForHumans(),
            'decided_at' => optional($joinRequest->decision_at)->diffForHumans(),
        ]);
    }

    public function decide(Request $request, ContractorJoinRequest $joinRequest): JsonResponse
    {
        $candidate = Candidate::withoutGlobalScopes()->with('user')->find($joinRequest->candidate_id);
        $joinRequest->load('requester');

        if (!$this->canManageJoinRequest($request->user(), $joinRequest, $candidate)) {
            abort(403);
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'decision_note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($joinRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'تم اتخاذ قرار سابقًا على هذا الطلب.',
            ], 422);
        }

        $joinRequest->update([
            'status' => $validated['decision'],
            'decision_note' => $validated['decision_note'] ?? null,
            'decision_at' => now(),
            'decided_by_user_id' => (int) $request->user()->id,
        ]);

        if ($validated['decision'] === 'approved' && $candidate) {
            Contractor::firstOrCreate(
                [
                    'user_id' => $joinRequest->requester_user_id,
                    'creator_id' => $candidate->user_id,
                ],
                [
                    'name' => $joinRequest->requester_name,
                    'phone' => $joinRequest->requester_phone,
                    'email' => $joinRequest->requester?->email,
                    'election_id' => $candidate->election_id,
                    'status' => 'approved',
                    'token' => Str::random(32),
                ]
            );
        }

        $this->finalizeDecisionNotification($request->user(), $joinRequest);

        return response()->json([
            'success' => true,
            'status' => $joinRequest->status,
            'message' => $joinRequest->status === 'approved' ? 'تمت الموافقة على الطلب.' : 'تم رفض الطلب.',
        ]);
    }

    private function canManageJoinRequest(User $user, ContractorJoinRequest $joinRequest, ?Candidate $candidate): bool
    {
        if ($candidate && (int) $candidate->user_id === (int) $user->id) {
            return true;
        }

        if ($candidate && (int) ($user->creator_id ?? 0) === (int) $candidate->user_id) {
            return true;
        }

        return $user->notifications()
            ->where('data->kind', 'contractor_join_request')
            ->where('data->join_request_id', (int) $joinRequest->id)
            ->exists();
    }

    private function submitForUser(Candidate $candidate, User $user): RedirectResponse
    {
        $joinRequest = ContractorJoinRequest::query()->firstOrNew([
            'candidate_id' => (int) $candidate->id,
            'requester_user_id' => (int) $user->id,
        ]);

        $alreadyPending = $joinRequest->exists && $joinRequest->status === 'pending';

        $joinRequest->requester_name = (string) $user->name;
        $joinRequest->requester_phone = (string) ($user->phone ?? '');
        $joinRequest->status = 'pending';
        $joinRequest->decision_note = null;
        $joinRequest->decision_at = null;
        $joinRequest->decided_by_user_id = null;
        $joinRequest->save();

        $candidateOwner = $candidate->user;
        if ($candidateOwner && !$alreadyPending) {
            $candidateOwner->notify(new \App\Notifications\SystemNotification([
                'title' => 'طلب انضمام جديد كمتعهد',
                'body' => 'شخص ما أرسل إليك طلب الانضمام كمتعهد. اضغط للمراجعة واتخاذ القرار.',
                'url' => route('dashboard.notifications.page'),
                'kind' => 'contractor_join_request',
                'join_request_id' => $joinRequest->id,
                'lock_read_until_decision' => true,
                'decision' => 'pending',
            ]));
        }

        $slug = Str::of($candidate->user?->name ?? 'candidate')->trim()->replace(' ', '-')->value() . '-' . $candidate->id;

        return redirect()->route('candidates.join.pending', ['slug' => $slug]);
    }

    private function finalizeDecisionNotification(User $candidateUser, ContractorJoinRequest $joinRequest): void
    {
        $notification = $candidateUser->notifications()
            ->where('data->kind', 'contractor_join_request')
            ->where('data->join_request_id', $joinRequest->id)
            ->latest()
            ->first();

        if (!$notification) {
            return;
        }

        $data = is_array($notification->data) ? $notification->data : [];
        $isApproved = $joinRequest->status === 'approved';

        $data['title'] = $isApproved ? 'تمت الموافقة على طلب الانضمام' : 'تم رفض طلب الانضمام';
        $data['body'] = $isApproved
            ? 'تم قبول المتعهد وإغلاق الطلب.'
            : 'تم رفض طلب الانضمام وإغلاق الطلب.';
        $data['url'] = '#';
        $data['lock_read_until_decision'] = false;
        $data['decision'] = $joinRequest->status;
        $data['decision_closed'] = true;

        $notification->data = $data;
        $notification->read_at = now();
        $notification->save();
    }

    private function resolveCandidateFromSlug(string $slug): Candidate
    {
        $rawSlug = trim(urldecode($slug));
        $candidateId = null;

        if (preg_match('/-(\d+)$/', $rawSlug, $matches)) {
            $candidateId = (int) $matches[1];
        }

        $query = Candidate::withoutGlobalScopes()->with(['election', 'user']);

        if ($candidateId) {
            return $query->findOrFail($candidateId);
        }

        $nameFromSlug = trim(str_replace('-', ' ', $rawSlug));

        return $query
            ->whereHas('user', function ($builder) use ($nameFromSlug, $rawSlug) {
                $builder->where('name', $nameFromSlug)
                    ->orWhereRaw("REPLACE(name, ' ', '-') = ?", [$rawSlug]);
            })
            ->firstOrFail();
    }
}
