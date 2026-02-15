<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Setting;
use App\Models\Election;
use App\Enums\SettingKey;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SettingsRequest;

class SettingController extends Controller
{
    public function show()
    {
        $settings = Setting::all();
        return view('dashboard.settings.show', compact('settings'));
    }

    public function update(SettingsRequest $request)
    {
        foreach (SettingKey::all() as $key) {
            $presentKey = $key . '__present';

            // If the key is not included in this form submission, do not overwrite it.
            // Some pages submit only a subset of settings.
            if (!$request->has($key) && !$request->has($presentKey)) {
                continue;
            }

            // If a key is marked as present but missing, treat it as explicitly cleared.
            $value = $request->has($key) ? $request->get($key) : [];
            $value = $this->normalizeSettingValue($key, $value);

            Setting::where('option_key', $key)->updateOrCreate(
                ['option_key' => $key],
                ['option_key' => $key, 'option_value' => $value]
            );
        }
        session()->flash('message', 'Settings Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }
    //================================================================================================

    private function normalizeSettingValue(string $key, mixed $value): array
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        if ($key === SettingKey::UI_MODE_POLICY->value) {
            $policy = trim((string) ($value[0] ?? ''));
            if (!in_array($policy, ['user_choice', 'modern', 'classic'], true)) {
                $policy = 'user_choice';
            }

            return [$policy];
        }

        $selectableSizeSettings = [
            SettingKey::UI_MODERN_FS_XS->value => ['0.75rem', '0.625rem', '0.875rem', '1rem'],
            SettingKey::UI_MODERN_FS_SM->value => ['0.875rem', '0.75rem', '1rem', '1.125rem'],
            SettingKey::UI_MODERN_FS_BASE->value => ['1rem', '0.875rem', '1.125rem', '1.25rem'],
            SettingKey::UI_MODERN_FS_LG->value => ['1.125rem', '1rem', '1.25rem', '1.375rem'],
            SettingKey::UI_MODERN_FS_XL->value => ['1.25rem', '1.125rem', '1.5rem', '1.75rem'],
            SettingKey::UI_MODERN_RADIUS_CARD->value => ['1rem', '0.5rem', '0.75rem', '1.25rem', '1.5rem'],
            SettingKey::UI_MODERN_RADIUS_INPUT->value => ['0.75rem', '0.375rem', '0.5rem', '1rem'],
            SettingKey::UI_MODERN_RADIUS_BUTTON->value => ['0.75rem', '0.375rem', '0.5rem', '1rem'],
            SettingKey::UI_MODERN_SPACE_SECTION->value => ['1.25rem', '0.75rem', '1rem', '1.5rem', '2rem'],
            SettingKey::UI_MODERN_SPACE_CARD->value => ['1rem', '0.75rem', '1.25rem', '1.5rem'],
            SettingKey::UI_MODERN_CONTAINER_MAX->value => ['1320px', '1140px', '1200px', '1440px', '1600px'],
            SettingKey::UI_MODERN_HOME_AVATAR_SIZE->value => ['62px', '48px', '56px', '72px', '84px'],
        ];

        if (array_key_exists($key, $selectableSizeSettings)) {
            $current = trim((string) ($value[0] ?? ''));
            $allowed = $selectableSizeSettings[$key];
            if (!in_array($current, $allowed, true)) {
                $current = $allowed[0];
            }

            return [$current];
        }

        if ($key === SettingKey::UI_MODERN_THEME_PRESET->value) {
            $preset = strtolower(trim((string) ($value[0] ?? '')));
            if (!preg_match('/^[a-z0-9][a-z0-9-]{0,63}$/', $preset)) {
                $preset = 'default';
            }

            return [$preset];
        }

        if ($key === SettingKey::UI_MODERN_THEME_LIBRARY->value) {
            $raw = (string) ($value[0] ?? '[]');
            $decoded = json_decode($raw, true);
            $decoded = is_array($decoded) ? $decoded : [];

            $safe = [];
            foreach ($decoded as $theme) {
                if (!is_array($theme)) {
                    continue;
                }

                $id = strtolower(trim((string) ($theme['id'] ?? '')));
                if (!preg_match('/^[a-z0-9][a-z0-9-]{0,63}$/', $id)) {
                    continue;
                }

                if (in_array($id, ['default', 'emerald', 'violet', 'custom'], true)) {
                    continue;
                }

                $name = trim((string) ($theme['name'] ?? $id));
                $name = substr($name !== '' ? $name : $id, 0, 80);

                $values = $theme['values'] ?? [];
                if (!is_array($values)) {
                    $values = [];
                }

                $normalizedValues = [];
                foreach ($values as $valueKey => $valueValue) {
                    $tokenKey = trim((string) $valueKey);
                    if ($tokenKey === '') {
                        continue;
                    }

                    $tokenValue = trim((string) $valueValue);
                    if ($tokenValue === '') {
                        continue;
                    }

                    $normalizedValues[$tokenKey] = substr($tokenValue, 0, 64);
                }

                $safe[$id] = [
                    'id' => $id,
                    'name' => $name,
                    'values' => $normalizedValues,
                ];
            }

            $safe = array_slice(array_values($safe), 0, 30);

            return [json_encode($safe, JSON_UNESCAPED_UNICODE) ?: '[]'];
        }

        return array_values($value);
    }

    //================================================================================================

    public function resultControl(){
        $settings = Setting::all();
        $candidates = Candidate::get();
        $elections  = Election::get();
        return view('dashboard.settings.result_control', compact('settings','candidates','elections'));
    }
    //================================================================================================
    public function elec(){
        $elections=Election::select('id','name')->get();
        return view('dashboard.settings.election',compact('elections'));
    }
    public function elecUp(Request $request){
        $election =Election::find($request->election_id);
        if($election){
            
    // Reset all candidates and their related data
        $election->candidates()->chunkById(100, function ($candidates) {
            $candidates->each(function ($candidate) {
                $candidate->update(['votes' => 0]);
                $candidate->committees()->each(function ($committee) {
                    $committee->pivot->update(['votes' => 0]);
                });
            });
        });

    // Reset all voters
        $election->voters()->chunkById(100, function ($voters) {
            $voters->each(function ($voter) {
                $voter->update([
                    'status' => 0,
                    'committee_id' => null,
                    'attend_id' => null
                ]);
            });
        });

        
        }   
        return redirect()->back()->with('success', 'Election data has been successfully reset.');
    }
    //================================================================================================
    public function initalizeAttendant(Request $request){
        // dd($request->all());
        $election_id=$request->election_id;
        try {  
            
            DB::beginTransaction();
            // Update voters
            DB::update("
                UPDATE voters 
                SET status = 0, 
                    committee_id = NULL, 
                    attend_id = NULL 
                WHERE status = 1 
                AND committee_id IS NOT NULL 
                AND attend_id IS NOT NULL 
                AND id IN (SELECT voter_id FROM election_voter WHERE election_id = ?)", 
                [$election_id]
            );
            
            // Update candidates
            DB::update("
                UPDATE candidates 
                SET votes = 0 
                WHERE election_id = ?", 
                [$election_id]
            );
            DB::commit();
            // dd('done');
            session()->flash('message', 'تم تصفير الحضور بنجاح');
            session()->flash('type', 'success');
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('message', 'حدث خطا اثناء التصفير');
            session()->flash('type', 'error');
            return back();

        }
    }
    //================================================================================================
}
