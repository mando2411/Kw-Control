<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UiModeController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mode' => ['required', 'in:classic,modern'],
        ]);

        $user = $request->user();
        if ($user) {
            $user->forceFill([
                'ui_mode' => $data['mode'],
            ])->save();
        }

        return response()->json([
            'success' => true,
            'mode' => $data['mode'],
        ]);
    }
}
