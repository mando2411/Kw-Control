<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ImportContractorVotersRequest extends FormRequest
{
    //==================================================================
    public function authorize(): bool {
        return true;
    }
    //==================================================================
    public function rules(): array{
        $rules = [
            'import'            => 'required|mimes:xlsx,xls,csv',
            'main_contractor'   => 'required|exists:contractors,id',
            'sub_contractor'    => 'nullable|exists:contractors,id',
        ];
        return $rules;
    }
    //==================================================================
    public function attributes(): array{
        $attributes = [
            "import"            => "File",
            "main_contractor"   => "Main Contractor",
            "sub_contractor"    => "Sub Contractor",
        ];
        return $attributes;
    }
    //==================================================================
}