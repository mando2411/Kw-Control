<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
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
            'name' => 'اسم المدرسة',
            'type' => 'النوع',
            'election_id' => 'الحملة الانتخابية',
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
        $hasSchoolElectionColumn = Schema::hasColumn('schools', 'election_id');

        $typeUniqueRule = Rule::unique('schools', 'type')->ignore($schoolId);
        if ($hasSchoolElectionColumn) {
            $typeUniqueRule->where(fn ($query) => $query->where('election_id', $this->input('election_id')));
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'type' => [
                'required',
                'in:ذكور,اناث',
                $typeUniqueRule,
            ],
            'election_id' => $hasSchoolElectionColumn
                ? ['required', 'integer', 'exists:elections,id']
                : ['nullable'],

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
        $data = $this->validated();

        if (!Schema::hasColumn('schools', 'election_id')) {
            unset($data['election_id']);
        }

        return $data;
     }
}
