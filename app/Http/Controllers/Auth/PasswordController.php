<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Representative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        // Step 1: Check incoming request data
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);
        $user = $request->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        Representative::query()
            ->where('user_id', (int) $user->id)
            ->update(['status' => 1]);

        return back()->with('status', 'password-updated');
    }
    
}
