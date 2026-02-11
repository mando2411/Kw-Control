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
            //'sub_contractor'    => 'required|exists:contractors,id',
        ];
        return $rules;
    }
    //==================================================================
    public function attributes(): array{
        $attributes = [
            "import"            => "File",
            //"sub_contractor"    => "Contractor",
        ];
        return $attributes;
    }
    //==================================================================
}