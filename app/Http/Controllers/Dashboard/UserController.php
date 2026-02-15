<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\UserDataTable;
use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UserRequest;
use App\Models\Role;
use App\Models\User;
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
        $user=auth()->user();
        return view('auth.change-pass',compact('user'));

    }
    public function passUpdate(Request $request,User $user)
    {
        $user->update(
            [
                'password'=> \Hash::make($request->get('password')),
            ]
        );
        $rep=$user->representatives()->get();
        $rep[0]->update([
            'status'=>1
        ]);
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
    $users = User::where('creator_id', auth()->user()->id)->paginate(10);

    return response()->json($users);
}
}
