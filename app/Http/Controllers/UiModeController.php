<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Throwable;

class UiModeController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mode' => ['required', 'in:classic,modern'],
        ]);

        // If production didn't run the migration yet, avoid 500s and let UI keep working.
        if (!Schema::hasColumn('users', 'ui_mode')) {
            return response()->json([
                'success' => false,
                'mode' => $data['mode'],
                'message' => 'ui_mode column missing (migration not applied).',
            ], 200);
        }

        $user = $request->user();
        try {
            if ($user) {
                $user->forceFill([
                    'ui_mode' => $data['mode'],
                ])->save();
            }
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'mode' => $data['mode'],
                'message' => 'Failed to persist ui_mode.',
            ], 200);
        }

        return response()->json([
            'success' => true,
            'mode' => $data['mode'],
        ]);
    }
}
