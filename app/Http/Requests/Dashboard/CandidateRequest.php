<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CandidateRequest extends FormRequest
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
"user_id" => "UserId",
"election_id" => "ElectionId",
"max_contractor" => "MaxContractor",
"max_represent" => "MaxRepresent",
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
        $rules = [
            'user_id' => ['nullable'],
            'election_id' => ['nullable'],
            'max_contractor' => ['required','integer','min:0'],
            'max_represent' => ['required','integer','min:0'],
            'banner' => ['nullable'],
            'candidate_type' => ['nullable', Rule::in(['candidate', 'list_leader'])],
            'list_candidates_count' => ['nullable', 'integer', 'min:1', 'required_if:candidate_type,list_leader'],
            'list_name' => ['nullable', 'string', 'max:255', 'required_if:candidate_type,list_leader'],
            'list_logo' => ['nullable'],
            'is_actual_list_candidate' => ['nullable', 'boolean'],
            'list_leader_candidate_id' => ['nullable', 'integer', 'exists:candidates,id'],


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
