<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\SettingKey;
use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [];
        foreach (SettingKey::all() as $key) {
            $rules[$key] = ['nullable', 'array'];
        }
        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
