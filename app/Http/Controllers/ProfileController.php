<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */

     public function index()
     {
         if(auth()->guard('client')->user())
        {
           return view('site.profile.index');

        }
        else
          return  view('site.about.index');
     }

    public function toggleTheme(Request $request)
    {
        $mode = $request->get('mode');
        $allowedModes = ['classic', 'modern'];

        if (!in_array($mode, $allowedModes, true)) {
            $currentMode = session('ui_mode', auth()->user()->ui_mode ?? 'classic');
            $mode = $currentMode === 'modern' ? 'classic' : 'modern';
        }

        session(['ui_mode' => $mode]);

        if (auth()->check() && Schema::hasColumn('users', 'ui_mode')) {
            auth()->user()->update(['ui_mode' => $mode]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'ui_mode' => $mode,
            ]);
        }

        return back();
    }


    public function change_password(Request $request)
    {

         $validator = Validator::make($request->all(), [
        'old_password' => 'required',
        'new_password' => 'required|min:8',
        'confirm_password' => 'required|same:new_password',
         ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();


            }

             // Get the authenticated user
            $user = auth()->user();

            // Verify the old password
            if (!Hash::check($request->input('old_password'), $user->password)) {
                return redirect()->back()->withErrors(['old_password' => 'The old password is incorrect.'])->withInput();
            }

            // Update the user's password
            $user->password = Hash::make($request->input('new_password'));
            $user->save();

            return redirect()->back()->with('success', 'Password changed successfully.');
        }

 

    public function edit(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('Administrator');

        return view('profile.edit', [
            'user' => $user,
            'isAdmin' => $isAdmin,
            'roles' => $isAdmin ? Role::query()->orderBy('name')->get() : collect(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if (array_key_exists('email', $validated)) {
            $user->email = $validated['email'];
        }

        if (array_key_exists('phone', $validated)) {
            $user->phone = $validated['phone'];
        }

        if ($request->boolean('remove_image')) {
            $this->deleteUserImageFromMediaDisk($user->getRawOriginal('image'));
            $user->image = null;
        }

        if ($request->hasFile('image')) {
            $this->deleteUserImageFromMediaDisk($user->getRawOriginal('image'));
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('', $filename, 'media');
            $user->image = rtrim(config('app.url'), '/') . '/storage/media/' . $filename;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($user->hasRole('Administrator') && ($request->has('roles') || $request->boolean('roles_present'))) {
            $user->syncRoles($request->input('roles', []));
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    private function deleteUserImageFromMediaDisk(?string $imageUrl): void
    {
        if (!$imageUrl) {
            return;
        }

        $path = parse_url($imageUrl, PHP_URL_PATH) ?: $imageUrl;
        $relative = null;

        if (Str::contains($path, '/storage/media/')) {
            $relative = ltrim(Str::after($path, '/storage/media/'), '/');
        } elseif (Str::contains($path, 'storage/media/')) {
            $relative = ltrim(Str::after($path, 'storage/media/'), '/');
        } elseif (Str::contains($path, '/media-file/')) {
            $relative = ltrim(Str::after($path, '/media-file/'), '/');
        }

        if (!$relative) {
            return;
        }

        Storage::disk('media')->delete($relative);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }


   
}
