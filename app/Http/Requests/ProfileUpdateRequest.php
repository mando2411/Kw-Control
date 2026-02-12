<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'email' => ['nullable', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'max:25', Rule::unique(User::class)->ignore($this->user()->id)],
            'image' => ['nullable', 'image', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
        ];

        if ($this->user() && $this->user()->hasRole('Administrator')) {
            $rules['roles'] = ['nullable', 'array'];
            $rules['roles.*'] = ['nullable', Rule::exists('roles', 'id')];
            $rules['roles_present'] = ['nullable', 'boolean'];
        }

        return $rules;
    }
}
