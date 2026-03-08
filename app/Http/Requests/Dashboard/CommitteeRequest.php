<?php

namespace App\Http\Requests\Dashboard;

use App\Models\School;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Validator;

class CommitteeRequest extends FormRequest
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
            'name' => 'اسم اللجنة',
            'school_id' => 'المدرسة',
            'election_id' => 'الحملة الانتخابية',
            'type' => 'النوع',
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
            'name' => ['required', 'string', 'max:255'],
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'election_id' => ['required', 'integer', 'exists:elections,id'],
            'type' => ['nullable', 'string', 'max:50'],
        ];

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (!Schema::hasColumn('schools', 'election_id')) {
                return;
            }

            $schoolId = (int) $this->input('school_id');
            $electionId = (int) $this->input('election_id');

            if (!$schoolId || !$electionId) {
                return;
            }

            $school = School::query()->select('id', 'election_id')->find($schoolId);
            if (!$school) {
                return;
            }

            if ($school->election_id !== null && (int) $school->election_id !== $electionId) {
                $validator->errors()->add('school_id', 'المدرسة المختارة لا تتبع الحملة الانتخابية المحددة.');
            }
        });
    }

    /**
     * Get the validated fields.
     *
     * @return array
     */
     public function getSanitized(): array
     {
          $data = $this->validated();

          $school = School::query()->select('id', 'type')->find((int) $data['school_id']);
          if ($school) {
                $data['type'] = $school->type;
          }

        return $data;
     }
}
