<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function attributes(): array
    {
        $attributes = [
            "name" => "Name",
            "type" => "Type",
            "election_id" => "Election",
        ];

        return $attributes;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $schoolId = $this->route('school')?->id;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'type' => [
                'required',
                'in:ذكور,اناث',
                Rule::unique('schools', 'type')
                    ->where(fn ($query) => $query->where('election_id', $this->input('election_id')))
                    ->ignore($schoolId),
            ],
            'election_id' => ['required', 'exists:elections,id'],

        ];

        return $rules;
    }

    /**
     * Get the validated fields.
     *
     * @return array
     */
     public function getSanitized(): array
     {
          return $this->validated();
     }
}
