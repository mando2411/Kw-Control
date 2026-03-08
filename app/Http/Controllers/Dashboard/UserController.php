<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\UserDataTable;
use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UserRequest;
use App\Models\Candidate;
use App\Models\Contractor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Representative;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{

    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('dashboard.users.index');
    }


    public function create()
    {
        $roles = Role::all();
        return view('dashboard.users.create', compact('roles'));
    }


    public function store(UserRequest $request)
    {
        $user = User::create($request->getSanitized());
        $user->assignRole($request->get('roles'));
        session()->flash('message', 'User Created Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function show(User $user)
    {
        //
    }


    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::all();
        return view('dashboard.users.edit', compact('user', 'roles'));
    }


    public function update(UserRequest $request, User $user)
    {
        $user->update($request->getSanitized());
        $user->syncRoles($request->get('roles'));
        session()->flash('message', 'User Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'message' => 'User Deleted Successfully!'
        ]);
    }
    public function changePassword(){
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        return view('auth.change-pass',compact('user'));

    }
    public function passUpdate(Request $request,User $user)
    {
        $user->update(
            [
                'password'=> \Hash::make($request->get('password')),
            ]
        );

        $rep = $user->representatives()->first();
        if ($rep) {
            $rep->update([
                'status' => 1
            ]);
        }

        Auth::logout();
        return redirect()->route('login')->with('success','تم تحديث البيانات بنجاح');
    }
    public function cards($id=null){
        $users = User::query();
        if($id){
            $users=$users->whereHas('roles', function ($query) use($id) {
                $query->where('role_id', $id);
            });
        }
        $relations=[
            'roles'=>Role::query(),
            'users'=>$users->get(),
            'reps'=> Representative::with('user')->get()
        ];

        return view('dashboard.cards.index',compact('relations'));
    }
    public function change(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users')->ignore($request->id),             ],
                'roles' =>'nullable'
        ]);
        $user=User::where('id',$request->id)->first();
        $user->update($validatedData);
        session()->flash('message', 'تم التعديل بنجاح');
        session()->flash('type', 'success');
        return redirect()->back();
    }

    public function export()
{   $users=User::select('name','phone','id')->get();
    return Excel::download(new UsersExport($users), 'invoices.xlsx');
}
public function keepAlive()
{
    auth()->user()->update(['last_active_at' => now()]);
    return response()->json(['status' => 'success']);
}

public function getUsers(Request $request)
{
    $users = self::visibleOnlineUsersQuery(auth()->user())->paginate(10);

    $users->getCollection()->transform(function (User $user) {
        return self::serializeOnlineUser($user);
    });

    return response()->json($users);
}

public function onlineUsers(Request $request)
{
    if (!Auth::check()) {
        return response()->json([
            'message' => 'Unauthenticated'
        ], 401);
    }

    $users = self::visibleOnlineUsersQuery(auth()->user())->paginate(10);

    $users->getCollection()->transform(function (User $user) {
        return self::serializeOnlineUser($user);
    });

    return response()->json($users);
}

public static function visibleOnlineUsersQuery(User $viewer): Builder
{
    $userIds = self::resolveVisibleOnlineUserIds($viewer);

    if (empty($userIds)) {
        return User::query()->whereRaw('1 = 0');
    }

    return User::query()
        ->whereIn('id', $userIds)
        ->orderByDesc('last_active_at');
}

private static function resolveVisibleOnlineUserIds(User $viewer): array
{
    $viewerId = (int) $viewer->id;

    $isListLeaderUser = $viewer->hasRole('مرشح رئيس قائمة') || Candidate::withoutGlobalScopes()
        ->where('user_id', $viewerId)
        ->where('candidate_type', 'list_leader')
        ->exists();

    if ($isListLeaderUser) {
        $candidateUserIds = self::resolveListLeaderCandidateUserIds($viewerId);

        if (empty($candidateUserIds)) {
            return [];
        }

        $rootContractorUserIds = Contractor::withoutGlobalScopes()
            ->whereIn('creator_id', $candidateUserIds)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $allUserIds = array_values(array_unique(array_merge($candidateUserIds, self::collectDescendantUserIds($rootContractorUserIds))));

        return $allUserIds;
    }

    $isCandidateUser = $viewer->hasRole('مرشح') || Candidate::withoutGlobalScopes()
        ->where('user_id', $viewerId)
        ->where('candidate_type', 'candidate')
        ->exists();

    if ($isCandidateUser) {
        $rootContractorUserIds = Contractor::withoutGlobalScopes()
            ->where('creator_id', $viewerId)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return self::collectDescendantUserIds($rootContractorUserIds);
    }

    if ($viewer->hasRole('Administrator')) {
        return User::query()
            ->where('creator_id', $viewerId)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    return User::query()
        ->where('creator_id', $viewerId)
        ->pluck('id')
        ->map(fn ($id) => (int) $id)
        ->all();
}

private static function resolveListLeaderCandidateUserIds(int $viewerId): array
{
    $listLeaderCandidate = Candidate::withoutGlobalScopes()
        ->where('user_id', $viewerId)
        ->where('candidate_type', 'list_leader')
        ->first();

    if (!$listLeaderCandidate) {
        return [];
    }

    return Candidate::withoutGlobalScopes()
        ->where(function ($query) use ($listLeaderCandidate) {
            $query->where('id', (int) $listLeaderCandidate->id)
                ->orWhere('list_leader_candidate_id', (int) $listLeaderCandidate->id);
        })
        ->whereNotNull('user_id')
        ->pluck('user_id')
        ->map(fn ($id) => (int) $id)
        ->unique()
        ->values()
        ->all();
}

private static function collectDescendantUserIds(array $rootUserIds): array
{
    $knownIds = array_values(array_unique(array_map('intval', array_filter($rootUserIds))));
    $frontier = $knownIds;

    while (!empty($frontier)) {
        $children = User::query()
            ->whereIn('creator_id', $frontier)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $newIds = array_values(array_diff($children, $knownIds));

        if (empty($newIds)) {
            break;
        }

        $knownIds = array_values(array_unique(array_merge($knownIds, $newIds)));
        $frontier = $newIds;
    }

    return $knownIds;
}

private static function serializeOnlineUser(User $user): array
{
    return [
        'id' => $user->id,
        'name' => $user->name,
        'is_online' => $user->isOnline(),
        'is_offline' => $user->isOffline(),
        'last_active_at' => $user->LoginTime($user->last_active_at),
    ];
}
}
